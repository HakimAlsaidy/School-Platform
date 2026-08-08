<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'student_fee_id',
        'student_id',
        'user_id',
        'payment_ref',
        'amount',
        'method',
        'status',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static array $methodLabels = [
        'cash' => 'نقدي',
        'card' => 'بطاقة بنكية',
        'transfer' => 'تحويل بنكي',
        'online' => 'دفع إلكتروني',
        'wallet' => 'محفظة إلكترونية',
    ];

    public static array $statusLabels = [
        'pending' => 'قيد الانتظار',
        'completed' => 'مكتمل',
        'failed' => 'فشل',
        'refunded' => 'مسترجع',
    ];

    public function getMethodLabelAttribute(): string
    {
        return self::$methodLabels[$this->method] ?? $this->method;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }
}
