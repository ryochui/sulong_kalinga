<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;  // Assuming User model corresponds to cose_users table
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login submission
    public function login(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Attempt to find the user by email
        $user = \DB::table('cose_users')
                    ->where('email', $request->input('email'))
                    ->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'User not found'])->withInput();
        }

        // Check if the password matches using Hash::check
        if (Hash::check($request->input('password'), $user->password)) {
            // If password is correct, log in the user
            Auth::loginUsingId($user->id);

            return redirect()->route('landing');  // Redirect to the home route or dashboard
        } else {
            // If password doesn't match, return an error
            return redirect()->back()->withErrors(['password' => 'Invalid password'])->withInput();
        }
    }

    // Handle logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
