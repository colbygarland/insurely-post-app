<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PartnerCodeController extends Controller
{
    public function index()
    {
        return Inertia::render('Partners/index');
    }

    public function process() {}
}
