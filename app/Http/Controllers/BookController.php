<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BookController extends Controller
{
    public function index()
    {
        return response()->json(Book::with(['author', 'category'])->get());
    }

    public function show($id)
    {
        $book = Book::with(['author', 'category'])->find($id);
        if ($book) {
            return response()->json($book);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('cover_image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('cover_image')->getRealPath())->getSecurePath();
            $data['cover_image'] = $uploadedFileUrl;
        }

        $book = Book::create($data);
        return response()->json($book, 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if ($book) {
            $data = $request->all();

            if ($request->hasFile('cover_image')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('cover_image')->getRealPath())->getSecurePath();
                $data['cover_image'] = $uploadedFileUrl;
            }

            $book->update($data);
            return response()->json($book);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        if ($book) {
            $book->delete();
            return response()->json(['message' => 'Book deleted']);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }
}
