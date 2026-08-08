<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomMaterial extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'classroom_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'type',
        'file_path',
        'external_url',
        'content',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public static array $typeLabels = [
        'file' => 'ملف',
        'link' => 'رابط',
        'text' => 'نص',
        'video' => 'فيديو',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }
}
