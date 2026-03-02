<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Therapist;

class WelcomeController extends Controller
{
    public function index()
    {
        $services   = Service::all();
        $therapists = Therapist::where('is_active', 1)->get();

        return view('welcome', compact('services', 'therapists'));
    }
}
