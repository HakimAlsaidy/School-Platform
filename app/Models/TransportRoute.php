<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'bus_id',
        'name',
        'description',
        'pickup_time',
        'dropoff_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

public function transportStudents(): HasMany
    {
        return $this->hasMany(TransportStudent::class, 'route_id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, TransportStudent::class, 'route_id', 'id', 'id', 'student_id');
    }
}
