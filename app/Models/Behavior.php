<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Behavior extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'student_id',
        'teacher_id',
        'subject_id',
        'type',
        'title',
        'description',
        'points',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'positive' ? 'إيجابي' : 'سلبي';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === 'positive' ? 'success' : 'danger';
    }
}
