<?php

namespace App\Models\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Trait BelongsToSchool
 * يُستخدم لإضافة دعم Multi-Tenant للموديلات
 */
trait BelongsToSchool
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToSchool(): void
    {
        // تلقائياً إضافة school_id عند الإنشاء
        static::creating(function ($model) {
            if (!$model->school_id && app()->bound('current_school')) {
                $model->school_id = app('current_school')->id;
            }
        });

        // Global Scope لفلترة البيانات حسب المدرسة
        // يتحقق أولاً من وجود العمود في الجدول
        static::addGlobalScope('school', function (Builder $builder) {
            $model = $builder->getModel();
            $tableName = $model->getTable();
            
            // تحقق من وجود عمود school_id في الجدول
            if (!Schema::hasColumn($tableName, 'school_id')) {
                return; // لا تطبّق الفلتر إذا لم يكن العمود موجوداً
            }
            
            $isSuperAdmin = app()->bound('is_super_admin') ? app('is_super_admin') : false;
            if (app()->bound('current_school') && !$isSuperAdmin) {
                $builder->where($tableName . '.school_id', app('current_school')->id);
            }
        });
    }

    /**
     * العلاقة مع المدرسة
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Scope لجلب بيانات مدرسة معينة
     */
    public function scopeForSchool(Builder $query, $schoolId): Builder
    {
        return $query->where($this->getTable() . '.school_id', $schoolId);
    }

    /**
     * Scope لجلب جميع البيانات (بدون فلترة)
     */
    public function scopeWithoutSchoolScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('school');
    }
}
