<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory, UsesSchoolSchema;

protected $fillable = [
        'school_id',
        'author_id',
        'title',
        'content',
        'target',
        'is_active',
        'is_pinned',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForTarget($query, string $target)
    {
        return $query->where('target', 'all')
                     ->orWhere('target', $target);
    }

    public function getTargetLabelAttribute(): string
    {
        return match($this->target) {
            'all' => 'الجميع',
            'teachers' => 'المعلمين',
            'parents' => 'أولياء الأمور',
            'students' => 'الطلاب',
            default => $this->target,
        };
    }
}
