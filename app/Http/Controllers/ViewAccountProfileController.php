<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ViewAccountProfileController extends Controller
{
    public function index()
    {
        // Get the authenticated user with their organization role
        $user = Auth::user()->load('organizationRole');
        
        // Format the user's birthday (if it exists)
        $formattedBirthday = null;
        if ($user->birthday) {
            $formattedBirthday = Carbon::parse($user->birthday)->format('F d, Y');
        }
        
        // Format the account creation date
        $memberSince = Carbon::parse($user->created_at)->format('M Y');
        
        return view('admin.adminViewProfile', compact('user', 'formattedBirthday', 'memberSince'));
    }
    
    public function settings()
    {
        // Redirect to the settings section of the profile page
        return redirect()->route('admin.account.profile.index')->with('activeTab', 'settings');
    }
}