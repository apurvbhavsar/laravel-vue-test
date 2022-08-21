<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\User;

class UserObserver
{

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        Post::where('user_id', $user->id)->delete();
    }
}
