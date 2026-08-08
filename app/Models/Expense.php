<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'amount',
        'category',
        'expense_date',
        'recorded_by',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static array $categoryLabels = [
        'salaries' => 'رواتب',
        'utilities' => 'مرافق',
        'supplies' => 'لوازم',
        'maintenance' => 'صيانة',
        'transport' => 'نقل',
        'activities' => 'أنشطة',
        'other' => 'أخرى',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::$categoryLabels[$this->category] ?? $this->category;
    }
}
