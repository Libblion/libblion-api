<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = ['user_id','borrow_id','overdue_days','fine_amount','paid'];


    public function user (){
        return $this->belongsTo(User::class);
    }
    public function borrow (){
        return $this->belongsTo(Borrowing::class);
    }
}
