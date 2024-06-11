<?php

namespace App\Http\Controllers;

use App\Models\Ctrabajo;
use Illuminate\Http\Request;

class CtrabajoController extends Controller
{
    //
    public function store(Request $request){
        $data=$request->validate([
            'categoria'=>'required',
        ]);
        Ctrabajo::create($data);

    }
}
