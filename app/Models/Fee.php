<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fee extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'amount',
        'type',
        'frequency',
        'status',
        'due_date',
        'is_installment',
        'installments_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_installment' => 'boolean',
    ];

    // العلاقات
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    // التسميات
    public static array $types = [
        'tuition' => 'رسوم دراسية',
        'books' => 'كتب ومصادر',
        'transport' => 'نقل مدرسي',
        'activities' => 'أنشطة',
        'other' => 'أخرى',
    ];

    public static array $frequencyLabels = [
        'one_time' => 'مرة واحدة',
        'term' => 'فصل دراسي',
        'semester' => 'ترم',
        'yearly' => 'سنوي',
    ];

    public static array $statusLabels = [
        'draft' => 'مسودة',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$types[$this->type] ?? $this->type;
    }

    public function getFrequencyLabelAttribute(): string
    {
        return self::$frequencyLabels[$this->frequency] ?? $this->frequency;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }
}

