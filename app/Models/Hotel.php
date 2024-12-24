<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'address',
        'details',
        'lat',
        'lng',
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }
}
