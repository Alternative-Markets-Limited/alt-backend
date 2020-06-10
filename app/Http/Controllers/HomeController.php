<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        Cache::flush();
        return response()->json(["name" => "Alt", "version" => "1.0.0", "description" => "Welcome to Alt api"]);
    }
}
