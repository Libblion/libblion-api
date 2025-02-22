<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth.api', 'auth.access'])->only('aproved_by');
    }


    public function aproved_by(string $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        $admin = auth()->user();

        $borrowing->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);


        return response()->json([
            'message' => 'Borrowing approved successfully',
            'data' => $borrowing
        ], 200);
    }

    public function index()
    {
        $data = Borrowing::with(['user', 'book', 'approvedBy'])->get();
        return response([
            'message' => 'Successfully retrieved borrowing data',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'return_date' => 'required|date'
        ]);

        $borrowing = Borrowing::create($validated);

        return response([
            'message' => 'Borrowing successfully added',
            'data' => $borrowing
        ], 201);
    }

    public function show(string $id)
    {
        $borrowing = Borrowing::with(['user', 'book'])->find($id);
        if (!$borrowing) {
            return response(['message' => 'Borrowing not found'], 404);
        }

        return response([
            'message' => 'Successfully retrieved borrowing data',
            'data' => $borrowing
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $borrowing = Borrowing::find($id);
        if (!$borrowing) {
            return response(['message' => 'Borrowing not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'status' => 'required|string|max:255',
            'approved_by' => 'required|exists:users,id',
            'return_date' => 'required|date'
        ]);

        $borrowing->update($validated);

        return response([
            'message' => 'Borrowing successfully updated',
            'data' => $borrowing
        ], 200);
    }

    public function destroy(string $id)
    {
        $borrowing = Borrowing::find($id);
        if (!$borrowing) {
            return response(['message' => 'Borrowing not found'], 404);
        }

        $borrowing->delete();

        return response([
            'message' => 'Borrowing successfully deleted'
        ], 200);
    }
}
