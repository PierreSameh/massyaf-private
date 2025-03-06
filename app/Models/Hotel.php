<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
        'policies'
    ];

    protected $casts = [
        'images' => 'array',
        'coordinates' => 'array',
    ];
    public $translatable = ['name', 'description', 'features', 'details', 'policies'];

    public function toArray()
    {
        $attributes = parent::toArray();

        foreach ($this->translatable as $field) {
            $attributes[$field] = $this->getTranslation($field, app()->getLocale());
        }

        return $attributes;
    }

    public function getImagesAttribute($value){
        $images = json_decode($value, true);
        $json = [];
        $isApiRequest = str_contains(request()->path(), 'api');
        if($isApiRequest){
            foreach( $images as $image ){
               $json[] = url(Storage::url( $image));
            }
        } else {
            foreach( $images as $image ){
                $json[] = $image;
            }
        }
            return $json;
    }
    public function units(){
        return $this->hasMany(Unit::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
