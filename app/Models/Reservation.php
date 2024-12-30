<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        "unit_id",
        "user_id",
        "date_from",
        "date_to",
        "adults_count",
        "children_count",
        "book_advance",
        "booking_price",
        "paid",
        "status",
        "approved_at",
        "cancelled_at",
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }


    public function ids(){
        return $this->hasMany(ReservationId::class);
    }
}
