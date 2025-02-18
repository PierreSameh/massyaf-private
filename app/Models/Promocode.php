<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Promocode extends Model
{
    use HasTranslations;

    public $translatable = ['description'];
    protected $fillable = [
        'promocode',
        'description',
        'percentage',
        'amount_total',
        'amount_night',
        'expired_at',
        'active',
    ];
}
