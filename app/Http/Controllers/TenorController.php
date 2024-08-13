<?php

namespace App\Http\Controllers;

use App\Models\Tenor;
use Illuminate\Http\Request;

class TenorController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'name'=>'required|unique:tenors',
        ]);
        Tenor::create($validatedData);
        return redirect()->back();
    }
}
