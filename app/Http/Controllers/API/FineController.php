<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\Profile;
use App\Models\User;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "message" => "getfine"
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $borrow_id = $request->query('id');
            $borrow = Borrowing::findOrFail($borrow_id);
            $profile_user = Profile::where('user_id', $borrow->user_id)->first();

            if (!$borrow->return_date) {
                return response()->json(['message' => 'Buku belum dikembalikan'], 400);
            }

            $due_date = Carbon::parse($borrow->overdue_date);
            $return_date = Carbon::parse($borrow->return_date);
            $overdue_days = $return_date->diffInDays($due_date, false);


            if ($overdue_days <= 0) {
                return response()->json(['message' => 'Tidak ada keterlambatan, denda tidak diperlukan'], 200);
            }


            $fine_amount = $overdue_days * 50000;


            $fine = Fine::create([
                'borrow_id' => $borrow->id,
                'user_id' => $borrow->user_id,
                'overdue_days' => $overdue_days,
                'fine_amount' => $fine_amount,
                'paid' => 'unpaid',
            ]);

            $customer_detail = array(
                'first_name' => $profile_user->firstname ?? $borrow->user->username,
                'last_name' =>  $profile_user->firstname ? $profile_user->lastname ?? $borrow->user->username : "",
                'email' => $borrow->user->email,
                'phone' => $profile_user->phone_number,
            );

            $token = '';

            if ($fine) {
                $midtransService = new MidtransService();
                $token = $midtransService->createTransaction($fine->id, $fine->fine_amount, $customer_detail);
            }

            return response()->json(
                ['message' => 'Denda berhasil ditambahkan', 'fine' => $fine, 'token' => $token, 'cd' => $customer_detail],
                201
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
