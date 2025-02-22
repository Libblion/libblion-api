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
        $this->middleware(['auth.api', 'auth.access'])->except('index', 'show', 'recommendedBooks');
    }

    public function index(Request $request)
    {
        $categoryId = $request->query('category_id'); // Ambil category_id dari request (opsional)
        $search = $request->query('search'); // Ambil parameter pencarian judul (opsional)

        $books = Book::with(['author', 'category', 'reviews'])
            ->when($categoryId, function ($query) use ($categoryId) {
                // Jika category_id diberikan, filter berdasarkan kategori
                $query->where('category_id', $categoryId);
            })
            ->when($search, function ($query) use ($search) {
                // Jika search query diberikan, cari berdasarkan judul buku saja
                $query->where('title', 'like', "%{$search}%");
            })
            ->get();

        return response()->json([
            "message" => "Successfully retrieved books",
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

    public function recommendedBooks(Request $request)
    {

        $skip = $request->query('skip', 0);
        $limit = $request->query('limit', 2);

        $mostBorrowedBooks = Book::with('author')->withCount('borrowings')
            ->orderBy('borrowings_count', 'DESC')
            ->skip($skip)
            ->limit($limit)
            ->get()
            ->map(function ($book) {
                $book->author->makeHidden(['created_at', 'updated_at']);
                return $book;
            });

        $recommendedBooks = Book::with('author')->whereNotIn('id', $mostBorrowedBooks->pluck('id'))
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->get()
            ->map(function ($book) {
                $book->author->makeHidden(['created_at', 'updated_at']);
                return $book;
            });

        if ($mostBorrowedBooks->isEmpty()) {
            return response()->json(['message' => 'No books found'], 404);
        }

        return response()->json([
            "message" => "Most Borrowed Books",
            "data" => [
                "most_borrowed_books" => $mostBorrowedBooks,
                "recommended_books" => $recommendedBooks
            ]
        ]);
    }
}
