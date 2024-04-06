<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menus;
use App\Models\SubMenus;
use Inertia\Inertia;

class SidebarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menus::all();
        $submenus = SubMenus::all();

        return inertia('', [
            'menus' => $menus,
            'submenus' => $submenus,
        ]);
    }

    public function sidebar(Request $request)
    {
        if (!auth()->check()) {
            // Redirect to login or handle unauthorized access appropriately
            return redirect()->route('login');
        }
    
        $user = auth()->user(); // Get authenticated user object
    
        //dd($user);

        return Inertia::render('Sidebar', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
