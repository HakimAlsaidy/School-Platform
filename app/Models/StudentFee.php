<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFee extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'fee_id',
        'student_id',
        'status',
        'amount_due',
        'amount_paid',
        'due_date',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public static array $statusLabels = [
        'pending' => 'قيد الانتظار',
        'partial' => 'مدفوع جزئياً',
        'paid' => 'مدفوع',
        'overdue' => 'متأخر',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float)$this->amount_due - (float)$this->amount_paid);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || $this->remaining_amount <= 0;
    }
}
