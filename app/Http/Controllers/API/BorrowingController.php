<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
        Log::info('Borrowing search request', $request->all());

        $query = Borrowing::with(['user', 'book', 'approvedBy']);

        // Filter berdasarkan status
        $status = $request->query('status');
        if ($status) {
            $query->where('status', $status);
            Log::info('Filtering by status', ['status' => $status]);
        }

        // Pencarian berdasarkan keyword (judul buku atau username)
        $search = $request->query('search');
        if ($search) {
            Log::info('Searching with keyword', ['search' => $search]);
            $query->where(function ($q) use ($search) {
                // Pencarian berdasarkan judul buku
                $q->whereHas('book', function ($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', '%' . $search . '%');
                })
                    // Pencarian berdasarkan username
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', '%' . $search . '%');
                    })
                    // Pencarian berdasarkan nama pengguna yang menyetujui
                    ->orWhereHas('approvedBy', function ($approverQuery) use ($search) {
                        $approverQuery->where('username', 'like', '%' . $search . '%');
                    })
                    // Pencarian berdasarkan status
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Urutkan hasil
        $data = $query->orderByRaw("FIELD(status, 'pending', 'approved', 'returned', 'overdue')")
            ->get();

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
        ]);

        $borrowing = Borrowing::create([
            'user_id' => $validated['user_id'],
            'book_id' => $validated['book_id'],
            'borrow_date' => now(),
            'return_date' => Carbon::now()->addDays(7),
            'status' => 'pending'
        ]);

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
            'approved_by' => 'exists:users,id|nullable',
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
