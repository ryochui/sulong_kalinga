<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CareManagerController extends Controller
{

    public function index(Request $request)
    {

        $search = $request->input('search');
        $filter = $request->input('filter');

        // Fetch careworkers based on the search query and filters
        $caremanagers = User::where('role_id', 2)
        ->with('municipality', 'barangay')
        ->when($search, function ($query, $search) {
            return $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        })
        ->when($filter, function ($query, $filter) {
            if ($filter == 'status') {
                return $query->orderBy('volunteer_status');
            } elseif ($filter == 'municipality') {
                return $query->orderBy('assigned_municipality_id');
            } elseif ($filter == 'barangay') {
                return $query->orderBy('barangay_id');
            }
        })
        ->orderBy('first_name') // Order by first name alphabetically by default
        ->get();

        // Debugging: Check the data
        //dd($careworkers);

        // Pass the data to the Blade template
        return view('admin.careManagerProfile', compact('caremanagers'));
    }

    public function viewCaremanagerDetails(Request $request)
    {
        $caremanager_id = $request->input('caremanager_id');
        $caremanager = User::where('role_id', 2)
        ->with('municipality', 'barangay')->find($caremanager_id);

        if (!$caremanager) {
            return redirect()->route('careManagerProfile')->with('error', 'Care manager not found.');
        }

        return view('admin.viewCaremanagerDetails', compact('caremanager'));
    }

    public function editCaremanagerProfile(Request $request)
    {
        $caremanager_id = $request->input('caremanager_id');
        $caremanager = User::where('role_id', 2)->where('id', $caremanager_id)->first();

        if (!$caremanager) {
            return redirect()->route('careManagerProfile')->with('error', 'Care manager not found.');
        }

        // Update care worker details here
        // ...

        return view('admin.editCaremanagerProfile', compact('caremanager'));    
    }
}