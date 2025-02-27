<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->uuid();
            $table->uuid('user_id');
            $table->uuid('borrow_id');
            $table->integer('overdue_days');
            $table->decimal('fine_amount',10,2);
            $table->enum('paid',['success','pending','unpaid']);


            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('borrow_id')->references('id')->on('borrowings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
