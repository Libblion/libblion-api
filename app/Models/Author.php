<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = ['first_name','last_name','email','no_telp'];

    public function books (){
        return $this->hasMany(Book::class);
    }
}
