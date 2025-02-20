<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Google\Service\Compute\PublicAdvertisedPrefixListWarning;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens; 
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;


    public function canAccessPanel(Panel $panel): bool
    {
        return $this->type == 'admin' ? true : false;
    }
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
        'fcm_token',
    ];

    protected $appends = [
        'units_count',
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
        'last_otp',
        'last_otp_expire'
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

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s'; // Customize the format if needed
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'));
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(config('app.timezone'));
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'owner_id');
    }

    public function ownerReservations()
    {
        return $this->hasManyThrough(Reservation::class, Unit::class, 'owner_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class);
    }

    public function sentTransactions()
    {
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'receiver_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function user1Chats()
    {
        return $this->hasMany(Chat::class, 'user1_id');
    }

    public function user2Chats()
    {
        return $this->hasMany(Chat::class, 'iser2_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getUnitsCountAttribute()
{
    return $this->type === 'owner' ? $this->units()->count() : null;
}   

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

}
