<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index()
    {
        return Inertia::render('Partners/index');
    }
}
