<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalFee extends Model
{
    protected $fillable = [
        'unit_id',
        'fees',
        'amount',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
