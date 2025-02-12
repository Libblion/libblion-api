<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Book;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.api','auth.access'])->except('index','show');
    }

    public function index()
    {
        $books = Book::with(['author', 'category', 'reviews', 'borrowings'])->get();
        return response()->json([
            "message" => "successfully retrieve all books",
            "data" => $books
        ]);
    }

    public function show($id)
    {
        $book = Book::with(['author', 'category', 'reviews', 'borrowings'])->find($id);
        if ($book) {
            return response()->json($book);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'release_year' => 'required|numeric',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('cover_image')->getRealPath())->getSecurePath();
            $validatedData['cover_image'] = $uploadedFileUrl;
        }

        $book = Book::create($validatedData);
        return response()->json([
            'message' => 'Book created successfully',
            'book' => $book->load(['author', 'category', 'reviews', 'borrowings'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);
        if ($book) {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
                'release_year' => 'required|numeric',
                'author_id' => 'required|exists:authors,id',
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($request->hasFile('cover_image')) {
                $uploadedFileUrl = Cloudinary::upload($request->file('cover_image')->getRealPath())->getSecurePath();
                $validatedData['cover_image'] = $uploadedFileUrl;
            }

            $book->update($validatedData);
            return response()->json([
                'message' => 'Book updated successfully',
                'book' => $book->load(['author', 'category', 'reviews', 'borrowings'])
            ]);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    public function destroy($id)
    {
        $book = Book::find($id);
        if ($book) {
            $book->delete();
            return response()->json(['message' => 'Book deleted successfully']);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }
}
