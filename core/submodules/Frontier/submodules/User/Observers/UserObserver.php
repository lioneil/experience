<?php

namespace User\Observers;

use User\Models\User;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  \User\Models\User  $resource
     * @return void
     */
    public function created(User $resource)
    {
        // save fields
        session()->flash('title', $resource->name);
        session()->flash('message', "User successfully created");
        session()->flash('type', 'success');
    }

    /**
     * Listen to the User updated event.
     *
     * @param  \User\Models\User  $resource
     * @return void
     */
    public function updated(User $resource)
    {
        session()->flash('title', $resource->name);
        session()->flash('message', "User successfully updated");
        session()->flash('type', 'success');
    }

    /**
     * Listen to the User deleted event.
     *
     * @param  \User\Models\User  $resource
     * @return void
     */
    public function deleted(User $resource)
    {
        session()->flash('title', $resource->name);
        session()->flash('message', "User successfully removed");
        session()->flash('type', 'success');
    }

    /**
     * Listen to the User restored event.
     *
     * @param  \User\Models\User  $resource
     * @return void
     */
    public function restored(User $resource)
    {
        session()->flash('title', $resource->name);
        session()->flash('message', "User successfully restored");
        session()->flash('type', 'success');
    }
}
