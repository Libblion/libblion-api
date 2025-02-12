<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function index()
    {
        $data = Review::with(['user', 'book'])->get();
        return response([
            'message' => 'Successfully retrieved review data',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string'
        ]);

        $review = Review::create($validated);

        return response([
            'message' => 'Review successfully added',
            'data' => $review
        ], 201);
    }

    public function show(string $id)
    {
        $review = Review::with(['user', 'book'])->find($id);
        if (!$review) {
            return response(['message' => 'Review not found'], 404);
        }

        return response([
            'message' => 'Successfully retrieved review data',
            'data' => $review
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response(['message' => 'Review not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string'
        ]);

        $review->update($validated);

        return response([
            'message' => 'Review successfully updated',
            'data' => $review
        ], 200);
    }

    public function destroy(string $id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response(['message' => 'Review not found'], 404);
        }

        $review->delete();

        return response([
            'message' => 'Review successfully deleted'
        ], 200);
    }
}
