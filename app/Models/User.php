<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'image',
        'id_image',
        'phone_number',
        'balance',
        'last_otp',
        'last_otp_expire',
        'phone_verified_at',
        'password',
    ];

    public function getImageAttribute($value)
    {
        // Assuming the 'picture' column stores the image path
        return $value ? url(Storage::url("app/public/" . $value)) : null;
    }
    public function getIdImageAttribute($value)
    {
        // Assuming the 'picture' column stores the image path
        return $value ? url(Storage::url("app/public/" . $value)) : null;
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function units(){
        return $this->hasMany(Unit::class, 'owner_id');
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function bankAccounts(){
        return $this->hasMany(BankAccount::class);
    }

    public function withdraws(){
        return $this->hasMany(Withdraw::class);
    }

    public function sentTransactions(){
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    public function receivedTransactions(){
        return $this->hasMany(Transaction::class, 'receiver_id');
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class);
    }
}
