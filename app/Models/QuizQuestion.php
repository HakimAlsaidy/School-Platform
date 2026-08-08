<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_quiz_id',
        'question_bank_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'points',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(OnlineQuiz::class, 'online_quiz_id');
    }

    public static array $typeLabels = [
        'multiple_choice' => 'اختيار من متعدد',
        'true_false' => 'صح أو خطأ',
        'short_answer' => 'إجابة قصيرة',
        'essay' => 'مقالي',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }
}
