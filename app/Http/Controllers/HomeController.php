<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json(["name" => "Alt", "version" => "1.0.0", "description" => "Welcome to Alt api"]);
    }
}
