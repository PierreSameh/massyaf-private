<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    protected $fillable = ["type", "from", "to", "percentage"];
}
