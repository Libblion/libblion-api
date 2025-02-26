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

    public function index(Request $request)
    {
        $status = $request->query('status');
        if ($status) {
            $data = Borrowing::where('status', $status)->with(['user', 'book', 'approvedBy'])->get();
        } else {
            $data = Borrowing::with(['user', 'book', 'approvedBy'])
                ->orderByRaw("FIELD(status, 'pending', 'approved', 'returned', 'overdue')")
                ->get();
        }
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


    public function countBorrow(Request $request)
    {
        $status = $request->query('status');
        if ($status) {
            $count = Borrowing::where('status', $status)->count();
        } else {
            $count = Borrowing::count();
        }

        return response()->json([
            "message" => "Total books count",
            "count" => $count
        ]);
    }

    public function bookBorrowed()
    {
        $borrow = Borrowing::with('book.author')->whereNot('status', 'done')
            ->limit(5)
            ->get();

        return response()->json([
            "message" => "sucessfully get borrowed book",
            "data" => $borrow
        ]);
    }

    public function overdueBorrowing()
    {
        $books = Borrowing::where('status', 'overdue')->with('book.author', 'user')->get();
        return response()->json([
            "message" => "successfully get overdue borowwing",
            "data" => $books
        ]);
    }
}
