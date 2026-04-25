<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'type',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'holiday' => 'إجازة',
            'exam' => 'اختبار',
            'meeting' => 'اجتماع',
            'activity' => 'نشاط',
            'other' => 'أخرى',
            default => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'holiday' => 'success',
            'exam' => 'danger',
            'meeting' => 'primary',
            'activity' => 'info',
            'other' => 'secondary',
            default => 'secondary',
        };
    }
}
