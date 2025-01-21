<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'images',
        'description',
        'features',
        'lat_top_right',
        'lng_top_right',
        'lat_top_left',
        'lng_top_left',
        'lat_bottom_right',
        'lng_bottom_right',
        'lat_bottom_left',
        'lng_bottom_left',
        'coordinates'
    ];
    

    protected $casts = [
        'images' => 'array',
        'coordinates' => 'array',

    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function compounds(){
        return $this->hasMany(Compound::class);
    }

    public function hotels(){
        return $this->hasMany(Hotel::class);
    }
}
