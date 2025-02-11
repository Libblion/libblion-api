<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    use HasFactory;

    use HasFactory,HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['otp', 'valid_until', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
