<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\BorrowingController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FineController;
use App\Http\Controllers\API\ProfileControler;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('v1')->group(function () {
    // Auth Route;
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'currentUser'])->middleware('auth.api');
        Route::post('account-verification', [AuthController::class, 'verifyAccount'])->middleware('auth.api');
        Route::post('generate-otp-code', [AuthController::class, 'generateOtpCode'])->middleware('auth.api');
    });

    // Profile Route
    Route::post('/profile', [ProfileControler::class, 'updateProfile'])->middleware('auth.api');
    //Categories Route
    Route::apiResource('categories', CategoryController::class);

    //Books Route
    Route::apiResource('books', BookController::class);

    Route::get('/books/most/recommended', [BookController::class, 'recommendedBooks']);

    //Author Route
    Route::apiResource('authors', AuthorController::class);

    //Review Route
    Route::apiResource('reviews', ReviewController::class);

    //Borrowing Route
    Route::apiResource('borrowings', BorrowingController::class);

    // Fine Route
    Route::apiResource('fine',FineController::class);

    Route::prefix('borrow')->group(function(){
        Route::get('', [BorrowingController::class,'bookBorrowed']);
        Route::get('count', [BorrowingController::class,'countBorrow']);
        Route::get('overdue', [BorrowingController::class,'overdueBorrowing']);
    });

    Route::put('borrowings/{id}/aproved_by', [BorrowingController::class, 'aproved_by']);
});
