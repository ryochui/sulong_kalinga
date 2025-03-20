<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class AuthController extends Controller
// {
//     // Show the login form
//     public function showLoginForm()
//     {
//         return view('auth.login');
//     }

//     // Handle the login logic
//     public function login(Request $request)
//     {
//         // Validate the request data
//         $credentials = $request->only('email', 'password');

//         // Attempt to log in the user
//         if (Auth::attempt($credentials)) {
//             // Authentication was successful
//             return redirect()->intended('/');
//         }

//         // Authentication failed, redirect back with error messages
//         return back()->withErrors(['email' => 'Invalid credentials.']);
//     }
// }
?>

<!-- old auth -->