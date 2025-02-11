<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $data = Author::all();
        return response([
            'message' => 'Successfully retrieved author data',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:authors,email',
            'no_telp' => 'required|string|max:15'
        ]);

        $author = Author::create($validated);

        return response([
            'message' => 'Author successfully added',
            'data' => $author
        ], 201);
    }

    public function show(string $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response(['message' => 'Author not found'], 404);
        }

        return response([
            'message' => 'Successfully retrieved author data',
            'data' => $author
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response(['message' => 'Author not found'], 404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:authors,email,' . $id,
            'no_telp' => 'required|string|max:15'
        ]);

        $author->update($validated);

        return response([
            'message' => 'Author successfully updated',
            'data' => $author
        ], 200);
    }

    public function destroy(string $id)
    {
        $author = Author::find($id);
        if (!$author) {
            return response(['message' => 'Author not found'], 404);
        }

        $author->delete();

        return response([
            'message' => 'Author successfully deleted'
        ], 200);
    }
}
