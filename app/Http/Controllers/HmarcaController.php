<?php

namespace App\Http\Controllers;

use App\Models\Hmarca;
use Illuminate\Http\Request;

class HmarcaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:hmarcas',
        ]);
        $hmarca = Hmarca::create($validatedData);

        return redirect()->back();
    }
}
