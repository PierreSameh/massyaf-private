<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ["user_id","account_name", "bank", "account_number"];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function withdraws(){
        return $this->hasMany(Withdraw::class);
    }
}
