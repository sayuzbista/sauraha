<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorService extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'vendor_services';

    // Mass assignable fields
    protected $fillable = [
        'vendor_id',
        'title',
        'category',
        'price',
        'maxPersons',
        'offer',
        'availability',
        'images',
        'promotion_category',       // new
        'payment_screenshot',       // new
        'status'           
    ];

    // Cast images JSON to array automatically
    protected $casts = [
        'images' => 'array'
    ];

    // Relationship: service belongs to vendor
    public function vendor() {
        return $this->belongsTo(\App\Models\ServiceProvider::class, 'vendor_id');
        return $this->belongsTo(ServiceProvider::class, 'vendor_id');
    }
    public function bookings()
{
    return $this->hasMany(Booking::class, 'vendor_service_id');
}
}
