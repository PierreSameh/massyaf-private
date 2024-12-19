<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'unit_id',
        'from',
        'to',
        'sale_percentage',
    ];

    public function unit(){
        return $this->belongsTo(Unit::class);
    }
}
