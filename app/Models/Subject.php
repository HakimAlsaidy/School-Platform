<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = ['school_id', 'name', 'code', 'description', 'color', 'is_global'];

public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject')
                    ->withTimestamps();
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class, 'grade_subject')
                    ->withTimestamps();
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(OnlineQuiz::class);
    }

    public function questionBanks(): HasMany
    {
        return $this->hasMany(QuestionBank::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ClassroomMaterial::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
