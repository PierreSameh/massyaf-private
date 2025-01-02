<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "unit_id",
        "user_id",
        "date_from",
        "date_to",
        "adults_count",
        "children_count",
        "book_advance",
        "booking_price",
        "owner_profit",
        "paid",
        "status",
        "approved_at",
        "cancelled_at",
        "transaction_id"
    ];
    protected $appends = ['days_count'];

    public function getDaysCountAttribute()
    {
        $dateFrom = Carbon::parse($this->date_from);
        $dateTo = Carbon::parse($this->date_to);
        $daysCount = $dateFrom->diffInDays($dateTo) + 1;

        return $daysCount;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function ids()
    {
        return $this->hasMany(ReservationId::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
