<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menus;
use App\Models\SubMenus;
use Inertia\Inertia;

class SidebarController extends Controller
{
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
}
