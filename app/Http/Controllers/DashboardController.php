<?php

namespace App\Http\Controllers;

use App\Models\Post;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'posts' => Post::all(),
        ]);
    }
}
