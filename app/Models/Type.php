<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'name',
        'type_for',
    ];

    public function units(){
        return $this->hasMany(Unit::class);
    }
}
