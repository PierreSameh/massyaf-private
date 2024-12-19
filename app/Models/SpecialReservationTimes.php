<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialReservationTimes extends Model
{
    protected $fillable = [
        'unit_id',
        'from',
        'to',
        'price',
        'min_reservation_period',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
