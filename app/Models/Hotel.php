<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Hotel extends Model
{
    use HasTranslations;
    protected $fillable = [
        'name',
        'address',
        'images',
        'description',
        'features',
        'details',
        'city_id',
        'coordinates',
        'base_code',
    ];

    protected $casts = [
        'images' => 'array',
        'coordinates' => 'array',
    ];
    public $translatable = ['name', 'description', 'features', 'details'];

    public function toArray()
    {
        $attributes = parent::toArray();
        
        foreach ($this->translatable as $field) {
            $attributes[$field] = $this->getTranslation($field, app()->getLocale());
        }

        return $attributes;
    }
    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
