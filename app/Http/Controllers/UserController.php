<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ]);

        $user = new User([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->save();

        return response()->json(['message' => 'Your account has been created.'], 201);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:old_password',
            'password_confirmation' => 'required|string|min:6|same:new_password',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        if (!password_verify($request->input('old_password'), $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 422);
        }

        $user->password = bcrypt($request->input('new_password'));

        $user->save();

        return response()->json(['message' => 'Password reset successful'], 200);
    }
}
