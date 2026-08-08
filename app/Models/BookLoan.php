<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookLoan extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'book_id',
        'student_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'notes',
        'issued_by',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static array $statusLabels = [
        'borrowed' => 'مستعار',
        'returned' => 'مُعاد',
        'overdue' => 'متأخر',
        'lost' => 'مفقود',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }
}
