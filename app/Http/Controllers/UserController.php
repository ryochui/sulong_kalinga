<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function validatePassword(Request $request)
    {
        $user = Auth::user();
        $password = $request->input('password');

        if (Hash::check($password, $user->password)) {
            return response()->json(['valid' => true]);
        } else {
            return response()->json(['valid' => false], 401);
        }
    }
}