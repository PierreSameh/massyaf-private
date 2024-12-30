<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = [
        "user_id", "bank_account_id", "amount", "status"
        ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function bankAccount(){
        return $this->belongsTo(BankAccount::class);
    }
}
