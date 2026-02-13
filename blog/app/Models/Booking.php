<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_service_id',
        'booked_units',
        'persons',
        'booking_date',
        'time_slot'
    ];

    public function service()
    {
        return $this->belongsTo(VendorService::class, 'vendor_service_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}