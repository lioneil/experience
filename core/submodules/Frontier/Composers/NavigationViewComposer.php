<?php

namespace Frontier\Composers;

use Crowfeather\Traverser\Traverser;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Pluma\Support\Composers\BaseViewComposer;
use Pluma\Support\Modules\Traits\Module;

class NavigationViewComposer extends BaseViewComposer
{
    use Module;

    /**
     * Starting depth of the traversables.
     *
     * @var integer
     */
    protected $depth = 1;

    /**
     * The navigational menu.
     *
     * @var array|object|mixed
     */
    protected $menus;

    /**
     * The breadcrumbs menu.
     *
     * @var array|object|mixed
     */
    protected $breadcrumbs;

    /**
     * Prefix for url.
     *
     * @var string
     */
    protected $urlPrefix;

    /**
     * Main function to tie everything together.
     *
     * @param  Illuminate\View\View   $view
     * @return void
     */
    public function compose(View $view)
    {
        parent::compose($view);

        $this->setMenus($this->requireFileFromModules('config/menus.php', modules(true, null, false)));
        $this->setBreadcrumbs($this->getCurrentUrl());
        $this->setVariablename("navigation");

        $view->with($this->getVariablename(), $this->handle());
    }

    /**
     * Sets the menus.
     *
     * @param array|object|mixed $menus
     */
    public function setMenus($menus)
    {
        $traverser = new Traverser();
        $traverser->set($menus)->flatten();
        $traverser->prepare();

        $this->menus = $traverser->rechild('root');
        $this->menus = $traverser->reorder($this->menus);

        $this->menus = $traverser->update($this->menus, function ($key, &$menu, &$parent) use ($traverser) {
            $menu['active'] = isset($menu['slug']) ? (url($this->getCurrentUrl()) === $menu['slug']) : false;
            if ($menu['active']) {
                $parent['active'] = $menu['active'];
            }

            $menu['can_be_accessed'] = false;
            if (user()->isRoot() ||
                (isset($menu['always_viewable']) && $menu['always_viewable']) ||
                (isset($menu['is_header']) && $menu['is_header']) ||
                user()->isPermittedTo($menu['name'])) {
                $menu['can_be_accessed'] = true;
            }

            $childRoutes = isset($menu['routes']['children']) ? $menu['routes']['children'] : [];
            $currentRouteName = $this->getCurrentRouteName();
            if ($menu['child']['active'] = in_array($currentRouteName, $childRoutes)) {
                $parent['active'] = $menu['child']['active'];
            }
        });

        return $this;
    }

    /**
     * Sets the breadcrumbs.
     *
     * @param string $currentUrl
     */
    public function setBreadcrumbs($currentUrl)
    {
        $url = explode('/', $currentUrl);
        $old = "";
        foreach ($url as &$segment) {
            $old .= "/$segment";
            $segment = $this->swapWord($segment);

            $segment = [
                'active' => end($url) === $segment,
                'label' => ucfirst($segment),
                'name' => $segment,
                'slug' => $old,
                'url' => url($old),
            ];
        }

        $this->breadcrumbs = $url;
    }

    /**
     * Handles the view to compose.
     *
     * @return Object|StdClass
     */
    private function handle()
    {
        return json_decode(json_encode([
            'menu' => $this->menu(),
            'sidebar' => $this->sidebar(),
            'breadcrumbs' => $this->breadcrumbs(),
            // 'utilitybar' => $this->utilitybar(),
        ]));
    }

    /**
     * Generates menus.
     *
     * @return array
     */
    private function menu()
    {
        //
    }

    /**
     * Generates breadcrumbs.
     *
     * @return array
     */
    private function breadcrumbs()
    {
        return json_decode(json_encode([
            'collect' => collect(json_decode(json_encode($this->breadcrumbs)))
        ]));
    }

    /**
     * Generates sidebar menus.
     *
     * @return array
     */
    private function sidebar()
    {
        $this->menus = $this->unsetForbiddenRoutes($this->menus);

        // ------------------------------------------------------
        // Note to future self:
        // This will remove the headers if no menus are under it.
        // TODO: refactor this.
        foreach ($this->menus as $i => &$current) {
            $next = next($this->menus);
            if (isset($current['is_header'])) {
                if (! $next) {
                    unset($this->menus[$i]);
                }

                if ($next && isset($next['is_header'])) {
                    unset($this->menus[$i]);
                }
            }
        }
        // ------------------------------------------------------

        return json_decode(json_encode([
            // 'generate' => $this->generateSidebar(collect(json_decode(json_encode($this->menus)))),
            'collect' => collect(json_decode(json_encode($this->menus))),
        ]));
    }

    /**
     * Generate sidebar.
     *
     * @param  object $menus
     * @return html|string
     */
    private function generateSidebar($menus)
    {
        $depth = $this->depth;

        return view("Frontier::templates.navigations.sidebar")->with(compact('menus', 'depth'))->render();
    }

    /**
     * Remove all routes the user is
     * restricted access.
     *
     * @param  array $menus
     * @return void
     */
    public function unsetForbiddenRoutes(&$menus = null)
    {
        if (user()->isRoot()) {
            return $menus;
        }

        $menus = is_null($menus) ? $this->menus : $menus;

        foreach ($menus as $i => &$menu) {
            if (isset($menu['children']) && ! empty($menu['children'])) {
                $menu['children'] = $this->unsetForbiddenRoutes($menu['children']);
            }

            if ((! $menu['can_be_accessed'] && ! $menu['is_parent']) ||
                (! $menu['can_be_accessed'] && $menu['is_parent'] && empty($menu['children']))) {
                unset($menus[$i]);
            }
        }

        return $menus;
    }
}
