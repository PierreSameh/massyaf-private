<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compound extends Model
{
    protected $fillable = [
        'name',
        'images',
        'description',
        'features',
        'city_id',
        'address',
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
