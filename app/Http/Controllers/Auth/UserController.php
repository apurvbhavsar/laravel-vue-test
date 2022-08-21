<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegisterd;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUser;
use App\Http\Requests\CreateUserByAdmin;
use App\Http\Requests\UpdateUser;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Request $request)
    {
        return new UserResource($request->user());
    }


    /**
     * Update the specified user in storage
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUser $request)
    {
        User::where('id', request()->get('id', auth('api')->user()->id))->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return new UserResource(User::find(request()->get('id', auth('api')->user()->id)));
    }

    /**
     * List of users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if(auth('api')->user()->can('viewAny', User::class)) {
            $result = new User;

            if (request()->get('search') && request()->get('search') !== 'null') {
                $result->where('name', 'like', '%' . request()->get('search') . '%');
                $result->orWhere('email', 'like', '%' . request()->get('search') . '%');
            }

            return UserResource::collection(
                $result->orderBy(request()->get('sort', 'created_at'), request()->get('direction', 'desc'))
                    ->paginate(
                        request()->get('per_page', 10)
                    )
            );
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserByAdmin $request)
    {
        if (!auth('api')->user()->can('create', User::class)) {
            return response()->json([
                'message' => 'You are not authorized to create a user.'
            ], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            UserRegisterd::dispatch($user);
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}
