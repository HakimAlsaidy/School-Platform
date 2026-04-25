<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * سجل النشاطات - Audit Log
 * يسجل جميع العمليات الحساسة في النظام
 */
class ActivityLog extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'user_id',
        'school_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تسجيل نشاط جديد
     */
    public static function log(
        string $action,
        ?string $description = null,
        ?Model $model = null,
        ?int $userId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'school_id' => auth()->user()?->school_id ?? $model?->school_id,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * تسجيل إنشاء سجل جديد
     */
    public static function logCreated(Model $model, ?string $description = null): self
    {
        return self::log(
            'created',
            $description ?? 'تم إنشاء ' . class_basename($model),
            $model,
            null,
            null,
            $model->toArray()
        );
    }

    /**
     * تسجيل تعديل سجل
     */
    public static function logUpdated(Model $model, array $oldValues, ?string $description = null): self
    {
        return self::log(
            'updated',
            $description ?? 'تم تعديل ' . class_basename($model),
            $model,
            null,
            $oldValues,
            $model->toArray()
        );
    }

    /**
     * تسجيل حذف سجل
     */
    public static function logDeleted(Model $model, ?string $description = null): self
    {
        return self::log(
            'deleted',
            $description ?? 'تم حذف ' . class_basename($model),
            $model,
            null,
            $model->toArray(),
            null
        );
    }

    /**
     * تسجيل تسجيل دخول
     */
    public static function logLogin(?User $user = null): self
    {
        $user = $user ?? auth()->user();
        return self::log(
            'login',
            'تسجيل دخول: ' . $user?->name,
            $user,
            $user?->id
        );
    }

    /**
     * تسجيل تسجيل خروج
     */
    public static function logLogout(?User $user = null): self
    {
        $user = $user ?? auth()->user();
        return self::log(
            'logout',
            'تسجيل خروج: ' . $user?->name,
            $user,
            $user?->id
        );
    }

    /**
     * تسجيل محاولة وصول غير مصرح
     */
    public static function logUnauthorizedAccess(string $resource, ?User $user = null): self
    {
        return self::log(
            'unauthorized_access',
            "محاولة وصول غير مصرح إلى: {$resource}",
            null,
            $user?->id ?? auth()->id()
        );
    }

    /**
     * Scope لجلب سجلات مستخدم معين
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope لجلب سجلات نوع معين
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope لجلب سجلات موديل معين
     */
    public function scopeByModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }
}
