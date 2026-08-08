<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'online_quiz_id',
        'student_id',
        'score',
        'max_score',
        'answers',
        'started_at',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'answers' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(OnlineQuiz::class, 'online_quiz_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->max_score == 0) return 0;
        return round(($this->score / $this->max_score) * 100, 2);
    }
}
