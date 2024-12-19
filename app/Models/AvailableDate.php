<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableDate extends Model
{
    protected $fillable = [
        'unit_id',
        'from',
        'to',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
