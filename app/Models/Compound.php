<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compound extends Model
{
    protected $fillable = [
        'name',
        'lat_top_right',
        'lng_top_right',
        'lat_top_left',
        'lng_top_left',
        'lat_bottom_right',
        'lng_bottom_right',
        'lat_bottom_left',
        'lng_bottom_left',
        'city_id'
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
