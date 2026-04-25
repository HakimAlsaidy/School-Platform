<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

/**
 * خدمة إدارة الـ Schemas لنظام Multi-Tenant
 * كل مدرسة لها Schema خاص بها
 */
class SchemaManager
{
    /**
     * اسم الـ Schema الافتراضي للجداول المشتركة
     */
    protected string $defaultSchema = 'public';

    /**
     * بادئة الـ Schema للمدارس
     */
    protected string $schoolPrefix = 'school_';

    /**
     * الجداول الخاصة بكل مدرسة
     */
    protected array $tenantTables = [
        'students',
        'teachers',
        'guardians',
        'grades',
        'classrooms',
        'subjects',
        'scores',
        'attendances',
        'behaviors',
        'schedules',
        'assignments',
        'assignment_submissions',
        'announcements',
        'events',
        'messages',
        'notifications',
        'activity_logs',
    ];

    /**
     * الجداول المشتركة (في الـ Schema الافتراضي)
     */
    protected array $sharedTables = [
        'users',
        'schools',
        'school_subscriptions',
        'roles',
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
    ];

    protected function driver(): string
    {
        return DB::connection()->getDriverName();
    }

    protected function isPostgres(): bool
    {
        return $this->driver() === 'pgsql';
    }

    /**
     * إنشاء Schema جديد لمدرسة
     */
    public function createSchoolSchema(int $schoolId): bool
    {
        $schemaName = $this->getSchemaName($schoolId);

        try {
            // إنشاء الـ Schema
            DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$schemaName}\"");

            // إنشاء الجداول داخل الـ Schema
            $this->createTenantTables($schemaName);

            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to create schema for school {$schoolId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * حذف Schema مدرسة
     */
    public function dropSchoolSchema(int $schoolId): bool
    {
        $schemaName = $this->getSchemaName($schoolId);

        try {
            DB::statement("DROP SCHEMA IF EXISTS \"{$schemaName}\" CASCADE");
            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to drop schema for school {$schoolId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * التبديل إلى Schema مدرسة معينة
     */
    public function switchToSchool(int $schoolId): void
    {
        $schemaName = $this->getSchemaName($schoolId);
        
        if ($this->isPostgres()) {
            $searchPath = "\"{$schemaName}\", \"{$this->defaultSchema}\"";
            DB::statement("SET search_path TO {$searchPath}");
        }
        
        // حفظ الـ Schema الحالي في التطبيق
        app()->instance('current_schema', $schemaName);
        app()->instance('current_school_id', $schoolId);
    }

    /**
     * العودة إلى الـ Schema الافتراضي
     */
    public function switchToDefault(): void
    {
        if ($this->isPostgres()) {
            DB::statement("SET search_path TO \"{$this->defaultSchema}\"");
        }
        
        app()->forgetInstance('current_schema');
        app()->forgetInstance('current_school_id');
    }

    public function qualifyTable(string $table, ?string $schema = null): string
    {
        $schema = $schema ?? (app()->bound('current_schema') ? app('current_schema') : null);

        if (!$schema) {
            return $table;
        }

        if ($this->isPostgres()) {
            return "\"{$schema}\".\"{$table}\"";
        }

        return "{$schema}_{$table}";
    }

    /**
     * الحصول على اسم الـ Schema للمدرسة
     */
    public function getSchemaName(int $schoolId): string
    {
        return $this->schoolPrefix . $schoolId;
    }

    /**
     * التحقق من وجود Schema للمدرسة
     */
    public function schemaExists(int $schoolId): bool
    {
        $schemaName = $this->getSchemaName($schoolId);
        
        $result = DB::select(
            "SELECT schema_name FROM information_schema.schemata WHERE schema_name = ?",
            [$schemaName]
        );
        
        return !empty($result);
    }

    /**
     * إنشاء الجداول داخل Schema المدرسة
     */
    protected function createTenantTables(string $schemaName): void
    {
        foreach ($this->tenantTables as $table) {
            $this->createTenantTable($schemaName, $table);
        }
    }

    /**
     * إنشاء جدول واحد داخل الـ Schema
     */
    protected function createTenantTable(string $schemaName, string $tableName): void
    {
        $fullTableName = "\"{$schemaName}\".\"{$tableName}\"";
        
        // نسخ هيكل الجدول من الـ Schema الافتراضي
        // أو إنشاؤه من الصفر حسب التعريف
        $tableDefinitions = $this->getTableDefinitions();
        
        if (isset($tableDefinitions[$tableName])) {
            DB::statement($tableDefinitions[$tableName]($schemaName));
        }
    }

    /**
     * تعريفات الجداول
     */
    protected function getTableDefinitions(): array
    {
        return [
            'students' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"students\" (
                    id BIGSERIAL PRIMARY KEY,
                    student_id VARCHAR(50) UNIQUE,
                    name VARCHAR(255) NOT NULL,
                    gender VARCHAR(10),
                    birth_date DATE,
                    grade_id BIGINT,
                    classroom_id BIGINT,
                    guardian_id BIGINT,
                    photo VARCHAR(255),
                    medical_notes TEXT,
                    address TEXT,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    deleted_at TIMESTAMP
                )
            ",
            
            'teachers' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"teachers\" (
                    id BIGSERIAL PRIMARY KEY,
                    user_id BIGINT REFERENCES public.users(id) ON DELETE CASCADE,
                    employee_id VARCHAR(50),
                    name VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    specialization VARCHAR(255),
                    qualification VARCHAR(255),
                    hire_date DATE,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'guardians' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"guardians\" (
                    id BIGSERIAL PRIMARY KEY,
                    user_id BIGINT REFERENCES public.users(id) ON DELETE CASCADE,
                    name VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    email VARCHAR(255),
                    relation VARCHAR(50),
                    occupation VARCHAR(255),
                    emergency_phone VARCHAR(20),
                    address TEXT,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'grades' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"grades\" (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    level INT,
                    description TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'classrooms' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"classrooms\" (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    grade_id BIGINT,
                    capacity INT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'subjects' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"subjects\" (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    code VARCHAR(50),
                    description TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'scores' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"scores\" (
                    id BIGSERIAL PRIMARY KEY,
                    student_id BIGINT,
                    subject_id BIGINT,
                    teacher_id BIGINT,
                    classroom_id BIGINT,
                    term INT,
                    month INT,
                    attendance DECIMAL(5,2),
                    homework DECIMAL(5,2),
                    discipline DECIMAL(5,2),
                    written DECIMAL(5,2),
                    month_total DECIMAL(5,2),
                    total_20 DECIMAL(5,2),
                    final_30 DECIMAL(5,2),
                    total_50 DECIMAL(5,2),
                    score DECIMAL(5,2),
                    exam_type VARCHAR(50),
                    notes TEXT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'attendances' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"attendances\" (
                    id BIGSERIAL PRIMARY KEY,
                    student_id BIGINT,
                    classroom_id BIGINT,
                    date DATE,
                    status VARCHAR(20),
                    notes TEXT,
                    recorded_by BIGINT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'behaviors' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"behaviors\" (
                    id BIGSERIAL PRIMARY KEY,
                    student_id BIGINT,
                    teacher_id BIGINT,
                    type VARCHAR(20),
                    title VARCHAR(255),
                    description TEXT,
                    points INT DEFAULT 0,
                    date DATE,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'schedules' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"schedules\" (
                    id BIGSERIAL PRIMARY KEY,
                    classroom_id BIGINT,
                    subject_id BIGINT,
                    teacher_id BIGINT,
                    day VARCHAR(20),
                    period INT,
                    start_time TIME,
                    end_time TIME,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'assignments' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"assignments\" (
                    id BIGSERIAL PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    teacher_id BIGINT,
                    classroom_id BIGINT,
                    subject_id BIGINT,
                    due_date DATE,
                    max_score INT,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'assignment_submissions' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"assignment_submissions\" (
                    id BIGSERIAL PRIMARY KEY,
                    assignment_id BIGINT,
                    student_id BIGINT,
                    file_path VARCHAR(255),
                    content TEXT,
                    score DECIMAL(5,2),
                    feedback TEXT,
                    submitted_at TIMESTAMP,
                    graded_at TIMESTAMP,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'announcements' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"announcements\" (
                    id BIGSERIAL PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    content TEXT,
                    author_id BIGINT,
                    target_audience VARCHAR(50),
                    is_active BOOLEAN DEFAULT TRUE,
                    published_at TIMESTAMP,
                    expires_at TIMESTAMP,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'events' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"events\" (
                    id BIGSERIAL PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    start_date TIMESTAMP,
                    end_date TIMESTAMP,
                    location VARCHAR(255),
                    type VARCHAR(50),
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'messages' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"messages\" (
                    id BIGSERIAL PRIMARY KEY,
                    sender_id BIGINT,
                    receiver_id BIGINT,
                    subject VARCHAR(255),
                    body TEXT,
                    is_read BOOLEAN DEFAULT FALSE,
                    read_at TIMESTAMP,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'notifications' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"notifications\" (
                    id BIGSERIAL PRIMARY KEY,
                    user_id BIGINT,
                    type VARCHAR(100),
                    title VARCHAR(255),
                    message TEXT,
                    data JSON,
                    is_read BOOLEAN DEFAULT FALSE,
                    read_at TIMESTAMP,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
            
            'activity_logs' => fn($schema) => "
                CREATE TABLE IF NOT EXISTS \"{$schema}\".\"activity_logs\" (
                    id BIGSERIAL PRIMARY KEY,
                    user_id BIGINT,
                    action VARCHAR(100),
                    model_type VARCHAR(255),
                    model_id BIGINT,
                    description TEXT,
                    old_values JSON,
                    new_values JSON,
                    ip_address VARCHAR(45),
                    user_agent VARCHAR(500),
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP
                )
            ",
        ];
    }

    /**
     * الحصول على قائمة الجداول الخاصة بالمدرسة
     */
    public function getTenantTables(): array
    {
        return $this->tenantTables;
    }

    /**
     * الحصول على قائمة الجداول المشتركة
     */
    public function getSharedTables(): array
    {
        return $this->sharedTables;
    }

    /**
     * نسخ بيانات من مدرسة لأخرى (للنسخ الاحتياطي)
     */
    public function copySchemaData(int $fromSchoolId, int $toSchoolId): bool
    {
        $fromSchema = $this->getSchemaName($fromSchoolId);
        $toSchema = $this->getSchemaName($toSchoolId);

        try {
            foreach ($this->tenantTables as $table) {
                DB::statement("
                    INSERT INTO \"{$toSchema}\".\"{$table}\"
                    SELECT * FROM \"{$fromSchema}\".\"{$table}\"
                ");
            }
            return true;
        } catch (\Exception $e) {
            logger()->error("Failed to copy schema data: " . $e->getMessage());
            return false;
        }
    }
}
