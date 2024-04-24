<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Response;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    const Numero_de_items_pagina =25;
    public function index()
    {
        $categories = DB::table('categories')
                        ->select('id', 'name') // Selecciona los campos que desees
                        ->paginate(self::Numero_de_items_pagina);

        return inertia('Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return inertia('Categories/Create');
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('categories.index');
    }

    public function edit(Category $category)
    {
        return inertia('Categories/Edit', ['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index');
    }
}
