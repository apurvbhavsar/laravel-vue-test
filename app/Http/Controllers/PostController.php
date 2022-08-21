<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePost;
use App\Models\Post;
use App\Http\Resources\Post as PostResource;

class PostController extends Controller
{
    /**
     * List of posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = Post::with('user');

        if(request()->get('search') && request()->get('search') !== 'null') {
            $result->where('title', 'like', '%' . request()->get('search') . '%');
            $result->orWhere('description', 'like', '%' . request()->get('search') . '%');
        }

        return PostResource::collection(
            $result->activeUser()
                ->orderBy(request()->get('sort', 'created_at'), request()->get('direction', 'desc'))
                ->paginate(request()->get('per_page', 10)
        ));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePost $request)
    {
        if(!auth('api')->user()->can('create', Post::class)) {
            return response()->json([
                'message' => 'You are not authorized to create a post.'
            ], 403);
        }

        $post = Post::create([
            'user_id' => auth('api')->id(),
            'title' => $request->title,
            'description' => $request->description
        ]);;

        return new PostResource($post);
    }

    /**
     * Update the specified post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(CreatePost $request, Post $post)
    {
        $post->update([
            'user_id' => auth('api')->id(),
            'title' => $request->title,
            'description' => $request->description
        ]);;

        return new PostResource($post);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
