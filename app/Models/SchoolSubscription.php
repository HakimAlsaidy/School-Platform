<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'plan',
        'price',
        'starts_at',
        'ends_at',
        'is_active',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'features' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isValid(): bool
    {
        return $this->is_active && $this->ends_at->isFuture();
    }

    public function getPlanNameAttribute(): string
    {
        return match($this->plan) {
            'free' => 'مجاني',
            'basic' => 'أساسي',
            'premium' => 'متميز',
            'enterprise' => 'مؤسسي',
            default => 'غير محدد',
        };
    }

    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->ends_at, false));
    }
}
