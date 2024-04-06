<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function dashboard(Request $request)
    {
        if (!auth()->check()) {
            // Redirect to login or handle unauthorized access appropriately
            return redirect()->route('login');
        }
    
        $user = auth()->user(); // Get authenticated user object
    
        // dd($user);

        return inertia('Dashboard', [
            'user' => $user
        ]);
    }

}
