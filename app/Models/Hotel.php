<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'address',
        'images',
        'description',
        'features',
        'details',
        'lat',
        'lng',
        'city_id'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
