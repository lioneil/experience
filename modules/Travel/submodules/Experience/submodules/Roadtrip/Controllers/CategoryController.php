<?php

namespace Roadtrip\Controllers;

use Category\Models\Category;
use Category\Requests\CategoryRequest;
use Roadtrip\Models\Roadtrip;
use Frontier\Controllers\AdminController;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $resources = Category::type('roadtrip')->get();

        return view("Roadtrip::categories.index")->with(compact('resources'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //

        return view("Theme::categories.show");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view("Theme::categories.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Category\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        $category->alias = $request->input('alias');
        $category->code = $request->input('code');
        $category->description = $request->input('description');
        $category->icon = $request->input('icon');
        $category->categorable_type = $request->input('categorable_type');
        $category->save();

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //

        return view("Theme::categories.edit");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Roadtrip\Requests\CategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        //

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //

        return redirect()->route('categories.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trash()
    {
        //

        return view("Theme::categories.trash");
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \Roadtrip\Requests\CategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(CategoryRequest $request, $id)
    {
        //

        return back();
    }

    /**
     * Delete the specified resource from storage permanently.
     *
     * @param  \Roadtrip\Requests\CategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(CategoryRequest $request, $id)
    {
        //

        return redirect()->route('categories.trash');
    }
}
