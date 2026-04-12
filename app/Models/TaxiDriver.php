<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaxiDriver extends Authenticatable
{
    use Notifiable;

    protected $table = 'taxi_drivers';

    protected $fillable = [
        'f_name', 'l_name', 'email', 'phone',
        'password', 'auth_token',
        'zone_id', 'vehicle_type',
        'license_plate', 'license_number',
        'image', 'identity_image',
        'status', 'active', 'available',
        'earning', 'application_status',
        'current_lat', 'current_lng',
    ];

    protected $hidden = [
        'password',
        'auth_token',
    ];

    protected $casts = [
        'zone_id'   => 'integer',
        'status'    => 'boolean',
        'active'    => 'integer',
        'available' => 'integer',
        'earning'   => 'float',
        'current_lat' => 'float',
        'current_lng' => 'float',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->f_name . ' ' . $this->l_name;
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function rides()
    {
        return $this->hasMany(TaxiRide::class, 'driver_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1)->where('application_status', 'approved');
    }

    public function scopeAvailable($query)
    {
        return $query->where('available', 1)->where('active', 1);
    }
}
