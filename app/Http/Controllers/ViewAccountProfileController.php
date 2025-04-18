<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ViewAccountProfileController extends Controller
{
    public function index()
    {
        return view('admin.adminViewProfile');
    }
}
?>
