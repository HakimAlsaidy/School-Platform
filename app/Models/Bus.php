<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'bus_number',
        'plate_number',
        'capacity',
        'driver_name',
        'driver_phone',
        'supervisor_name',
        'supervisor_phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(TransportRoute::class);
    }
}
