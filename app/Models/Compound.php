<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compound extends Model
{
    protected $fillable = [
        'name',
        'city_id',
        'address',
        'lng',
        'lat'
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
