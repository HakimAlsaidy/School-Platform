<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'action_url',
        'action_text',
        'notifiable_type',
        'notifiable_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // العلاقات
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    // أيقونات الأنواع
    public function getIconAttribute($value)
    {
        if ($value) return $value;
        
        return match($this->type) {
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'danger' => 'fas fa-times-circle',
            'schedule' => 'fas fa-calendar-alt',
            'score' => 'fas fa-star',
            'attendance' => 'fas fa-clipboard-check',
            'behavior' => 'fas fa-award',
            'message' => 'fas fa-envelope',
            default => 'fas fa-bell',
        };
    }

    // ألوان الأنواع
    public function getColorAttribute()
    {
        return match($this->type) {
            'success' => 'green',
            'warning' => 'amber',
            'danger' => 'red',
            'schedule' => 'indigo',
            'score' => 'blue',
            'attendance' => 'emerald',
            'behavior' => 'purple',
            'message' => 'cyan',
            default => 'gray',
        };
    }

    // دوال مساعدة ثابتة لإنشاء الإشعارات
    public static function send(int $userId, string $title, string $message, string $type = 'info', ?string $actionUrl = null, ?string $actionText = null, ?Model $notifiable = null): self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
        ]);
    }

    // إشعار للجدول الدراسي
    public static function scheduleAdded(User $user, Schedule $schedule): self
    {
        $day = Schedule::$days[$schedule->day] ?? $schedule->day;
        return self::send(
            userId: $user->id,
            title: 'حصة جديدة في جدولك',
            message: "تمت إضافة حصة {$schedule->subject->name} يوم {$day} الحصة {$schedule->period_number}",
            type: 'schedule',
            actionUrl: route('teacher.schedule.index'),
            actionText: 'عرض الجدول',
            notifiable: $schedule
        );
    }

    // إشعار للدرجات
    public static function scoreAdded(User $guardianUser, Score $score, Student $student): self
    {
        return self::send(
            userId: $guardianUser->id,
            title: 'درجة جديدة',
            message: "حصل {$student->name} على {$score->score} في {$score->subject->name}",
            type: 'score',
            actionUrl: route('parent.students.scores', $student),
            actionText: 'عرض الدرجات',
            notifiable: $score
        );
    }

    // إشعار للحضور
    public static function attendanceRecorded(User $guardianUser, Attendance $attendance, Student $student): self
    {
        $statuses = [
            'present' => 'حاضر',
            'absent' => 'غائب',
            'late' => 'متأخر',
            'excused' => 'مستأذن',
        ];
        $status = $statuses[$attendance->status] ?? $attendance->status;
        
        return self::send(
            userId: $guardianUser->id,
            title: 'سجل الحضور',
            message: "{$student->name} تم تسجيله {$status} بتاريخ {$attendance->date->format('Y/m/d')}",
            type: 'attendance',
            actionUrl: route('parent.students.attendance', $student),
            actionText: 'عرض السجل',
            notifiable: $attendance
        );
    }

    // إشعار للسلوك
    public static function behaviorRecorded(User $guardianUser, Behavior $behavior, Student $student): self
    {
        $typeText = $behavior->type === 'positive' ? 'إيجابي' : 'سلبي';
        
        return self::send(
            userId: $guardianUser->id,
            title: "سلوك {$typeText}",
            message: "{$student->name}: {$behavior->title}",
            type: 'behavior',
            actionUrl: route('parent.students.behaviors', $student),
            actionText: 'عرض السلوك',
            notifiable: $behavior
        );
    }

    // تحديد عدة إشعارات كمقروءة
    public static function markAllAsRead(int $userId): int
    {
        return self::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
