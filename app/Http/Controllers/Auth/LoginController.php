<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use App\Models\PortalAccount; 
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

        // Check in the cose_users table
        $user = \DB::table('cose_users')
                    ->where('email', $request->input('email'))
                    ->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // If user is found in cose_users and password matches, log in the user
            Auth::loginUsingId($user->id);
            session(['user_type' => 'cose']); // Store user type in session
            
            if ($user->role_id == 1) {
                session()->put('show_welcome', true);
                return redirect('/admin/dashboard');
            }
            if ($user->role_id == 2) {
                session()->put('show_welcome', true);
                return redirect()->route('managerdashboard');
            }
            if ($user->role_id == 3) {
                session()->put('show_welcome', true);
                return redirect()->route('workerdashboard');
            }
        }

        // If not found in cose_users, check in the portal_accounts table
        $user = \DB::table('portal_accounts')
                        ->where('portal_email', $request->input('email'))
                        ->first();

        if ($user && Hash::check($request->input('password'), $user->portal_password)) {
            // If user is found in portal_accounts and password matches, log in the user
            Auth::loginUsingId($user->id); // Use portal_account_id as the user ID
            session(['user_type' => 'family']); // Store user type in session
            return redirect()->route('landing'); // Redirect to the landing page
        }

        // If no user is found in either table, return an error
        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();

        
    }

    // Handle logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
