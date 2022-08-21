<?php

namespace App\Observers;

use App\Mail\PostDeletedByAdmin;
use App\Models\Post;
use Illuminate\Support\Facades\Mail;

class PostObserver
{
    /**
     * Handle the Post "deleted" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function deleted(Post $post)
    {
        if(auth()->user()->role == 'admin') {
            Mail::to($post->user->email)->send(new PostDeletedByAdmin($post));
        }
    }
}
