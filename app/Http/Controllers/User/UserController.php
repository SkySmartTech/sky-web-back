<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('admin');

        $users = User::query()->latest()->get(['id','name','email','role','created_at']);

        return response()->json($users);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = \App\Models\User::create($data);

        return response()->json([
            'message' => 'User created',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
        ], 201);
    }

    public function update(UpdateUserRequest $request, \App\Models\User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User updated',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    public function destroy(\App\Models\User $user)
    {
        \Illuminate\Support\Facades\Gate::authorize('admin');

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
