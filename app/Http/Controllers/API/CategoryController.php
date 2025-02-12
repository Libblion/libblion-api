<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.api','auth.access'])->except('index','show');
    }

    public function index()
    {
        return response()->json(Category::all());
    }

    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json($category);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($validatedData);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if ($category) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);

            $category->update($validatedData);
            return response()->json($category);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Category deleted']);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
}
