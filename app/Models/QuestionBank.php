<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionBank extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'subject_id',
        'grade_id',
        'teacher_id',
        'type',
        'question',
        'options',
        'correct_answer',
        'points',
        'difficulty',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public static array $typeLabels = [
        'multiple_choice' => 'اختيار من متعدد',
        'true_false' => 'صح أو خطأ',
        'short_answer' => 'إجابة قصيرة',
        'essay' => 'مقالي',
    ];

    public static array $difficultyLabels = [
        'easy' => 'سهل',
        'medium' => 'متوسط',
        'hard' => 'صعب',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    public function getDifficultyLabelAttribute(): string
    {
        return self::$difficultyLabels[$this->difficulty] ?? $this->difficulty;
    }
}
