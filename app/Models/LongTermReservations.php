<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LongTermReservations extends Model
{
    protected $fillable = [
        'unit_id',
        'more_than_days',
        'sale_percentage',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
