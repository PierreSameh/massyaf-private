<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelPoliciy extends Model
{
    protected $fillable = [
        'unit_id',
        'days',
        'penalty',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
