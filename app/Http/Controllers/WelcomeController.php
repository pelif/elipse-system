<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function index()
    {
        // dump(auth()->user());

        return Inertia::render('Welcome', [
            'message' => 'Olá do servidor Laravel!',
        ]);
    }
}
