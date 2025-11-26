<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(User $user)
    {
        //
    }

    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ]);

        // save avatar
        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('user-avatar');
        }

        return $user;
    }

    public function destroy(User $user)
    {
        //
    }

    public function profile(): User
    {
        return Auth::user();
    }
}
