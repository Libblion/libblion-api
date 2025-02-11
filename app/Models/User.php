<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = ['username', 'email', 'password', 'email_verified_at', 'role_id'];

    public static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            $model->generateOtp();
        });
    }

    public function generateOtp()
    {
        do {
            $randNumber = mt_rand(100000, 999999);
            $check = Otp::where('otp', $randNumber)->first();
        } while ($check);

        $now = Carbon::now();

        Otp::updateOrCreate(
            ['user_id' => $this->id],
            ['otp' => $randNumber, 'valid_until' => $now->addMinutes(5)],
        );
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}