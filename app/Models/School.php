<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'subdomain',
        'logo',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'principal_name',
        'principal_phone',
        'type',
        'level',
        'max_students',
        'max_teachers',
        'license_expires_at',
        'is_active',
        'is_verified',
        'settings',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'license_expires_at' => 'date',
            'settings' => 'array',
        ];
    }

    // ==========================================
    // العلاقات
    // ==========================================

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_active', false)->orWhere('is_verified', false);
    }

    // ==========================================
    // Accessors
    // ==========================================

    public function getFullUrlAttribute(): string
    {
        $domain = config('app.domain', 'localhost');
        return "https://{$this->subdomain}.{$domain}";
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4f46e5&color=fff&size=200';
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'public' => 'حكومية',
            'private' => 'أهلية',
            'international' => 'دولية',
            default => 'أخرى',
        };
    }

    public function getLevelNameAttribute(): string
    {
        return match($this->level) {
            'elementary' => 'ابتدائي',
            'middle' => 'متوسط',
            'high' => 'ثانوي',
            'all' => 'جميع المراحل',
            default => 'أخرى',
        };
    }

    // ==========================================
    // Methods
    // ==========================================

    public function isLicenseValid(): bool
    {
        if (!$this->license_expires_at) {
            return true; // بدون تاريخ انتهاء = مفعّل دائماً
        }
        return $this->license_expires_at->isFuture();
    }

    public function getActiveSubscription()
    {
        return $this->subscriptions()
            ->where('is_active', true)
            ->where('ends_at', '>=', now())
            ->latest()
            ->first();
    }

    public function canAddStudents($count = 1): bool
    {
        return $this->students()->count() + $count <= $this->max_students;
    }

    public function canAddTeachers($count = 1): bool
    {
        return $this->teachers()->count() + $count <= $this->max_teachers;
    }

    public function getStats(): array
    {
        return [
            'students_count' => $this->students()->count(),
            'teachers_count' => $this->teachers()->count(),
            'guardians_count' => $this->guardians()->count(),
            'classrooms_count' => $this->classrooms()->count(),
            'grades_count' => $this->grades()->count(),
        ];
    }
}
