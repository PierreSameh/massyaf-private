<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'status',
        'type',
        'ref',
        'payment_method',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'ref'
    ];

    public $timestamps = false;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function reserve(){
        return $this->hasOne(Reservation::class);
    }

    public function withdraw(){
        return $this->hasOne(Withdraw::class);
    }
}
