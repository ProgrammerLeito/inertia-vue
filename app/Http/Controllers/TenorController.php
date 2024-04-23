<?php
 
namespace App\Http\Controllers;
 
use App\Models\Tenor;
use Illuminate\Http\Request;
 
class TenorController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'name'=>'required',
        ]);
        $tenor=Tenor::create($validatedData);
        return redirect()->back();
    }
}