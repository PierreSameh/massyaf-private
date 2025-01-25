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
