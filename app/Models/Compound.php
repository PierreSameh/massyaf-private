<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class Compound extends Model
{
    use HasTranslations;
    protected $fillable = [
        'name',
        'images',
        'description',
        'features',
        'city_id',
        'address',
        'coordinates',
        'base_code',
    ];

    protected $casts = [
        'images' => 'array',
        'coordinates' => 'array',
    ];
    public $translatable = ['name', 'description', 'features'];

    public function toArray()
    {
        $attributes = parent::toArray();

        foreach ($this->translatable as $field) {
            $attributes[$field] = $this->getTranslation($field, app()->getLocale());
        }

        return $attributes;
    }
    public function getImagesAttribute($value)
    {
        $images = json_decode($value, true) ?? []; // Ensure $images is always an array
        $json = [];
        $isApiRequest = str_contains(request()->path(), 'api');

        if ($isApiRequest) {
            foreach ($images as $image) {
                $json[] = url(Storage::url("app/public/" . $image));
            }
        } else {
            foreach ($images as $image) {
                $json[] = $image;
            }
        }

        return $json;
    }
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
