<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Logout function
    public function logout()
    {
        Auth::logout(); // Log out the user
        session()->flush(); // Clear the session data
        return redirect()->route('login'); // Redirect to the login page
    }
}
?>

<!-- old auth -->