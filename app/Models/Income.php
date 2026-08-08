<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'amount',
        'category',
        'income_date',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'income_date' => 'date',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static array $categoryLabels = [
        'tuition' => 'رسوم دراسية',
        'donation' => 'تبرعات',
        'funding' => 'تمويل',
        'rental' => 'إيجارات',
        'other' => 'أخرى',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::$categoryLabels[$this->category] ?? $this->category;
    }
}
