<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
class ServiceProvider extends Authenticatable
{
    use HasFactory, Notifiable;

    // Fields that can be mass-assigned
    protected $fillable = [
        'company_name',
        'owner_name',
        'location',
        'email',
        'phone_number',
        'pan_number',
        'services_provided',
        'opening_time',
        'closing_time',
        'password',
        'company_doc',
        'building_photo',
        'status',
          
    ];

    // Hide password and remember_token from JSON responses
    protected $hidden = [
        'password',
         
        
    ];

    // Cast services_provided to array automatically
    protected $casts = [
        'services_provided' => 'array',
    ];
}
