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
        'city_id',
        'coordinates'
    ];

    protected $casts = [
        'images' => 'array',
        'coordinates' => 'array',
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
