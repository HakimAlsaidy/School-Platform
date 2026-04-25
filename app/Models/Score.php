<?php

namespace App\Models;

use App\Models\Traits\UsesSchoolSchema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    use HasFactory, UsesSchoolSchema;

    protected $fillable = [
        'school_id',
        'student_id',
        'subject_id',
        'teacher_id',
        'exam_type',
        'exam_date',
        'score',
        'max_score',
        'semester',
        'notes',
        // الترم والشهر
        'term',          // الترم: 1 أو 2
        'month',         // الشهر: 1, 2, 3 أو null للمحصلة والنهائي
        // درجات الأعمال الفصلية التفصيلية
        'attendance',    // الحضور من 20
        'homework',      // الواجبات من 20
        'discipline',    // المواظبة من 20
        'written',       // التحريري من 40
        'month_total',   // مجموع الشهر من 100
        'total_20',      // المحصلة من 20
        'final_30',      // النهائي من 30
        'total_50',      // المجموع من 50
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'exam_date' => 'date',
        'term' => 'integer',
        'month' => 'integer',
        'attendance' => 'decimal:2',
        'homework' => 'decimal:2',
        'discipline' => 'decimal:2',
        'written' => 'decimal:2',
        'month_total' => 'decimal:2',
        'total_20' => 'decimal:2',
        'final_30' => 'decimal:2',
        'total_50' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getPercentageAttribute(): float
    {
        return round(($this->score / $this->max_score) * 100, 2);
    }

    public function getExamTypeLabelAttribute(): string
    {
        return match($this->exam_type) {
            'quiz' => 'اختبار قصير',
            'midterm' => 'اختبار نصفي',
            'final' => 'اختبار نهائي',
            'homework' => 'واجب منزلي',
            'participation' => 'مشاركة',
            default => $this->exam_type,
        };
    }

    public function getGradeAttribute(): string
    {
        $percentage = $this->percentage;
        
        return match(true) {
            $percentage >= 90 => 'ممتاز',
            $percentage >= 80 => 'جيد جداً',
            $percentage >= 70 => 'جيد',
            $percentage >= 60 => 'مقبول',
            default => 'ضعيف',
        };
    }

    public function getTermLabelAttribute(): string
    {
        return match($this->term) {
            1 => 'الترم الأول',
            2 => 'الترم الثاني',
            default => 'غير محدد',
        };
    }

    public function getMonthLabelAttribute(): string
    {
        return match($this->month) {
            1 => 'الشهر الأول',
            2 => 'الشهر الثاني',
            3 => 'الشهر الثالث',
            null => 'المحصلة والنهائي',
            default => 'غير محدد',
        };
    }

    /**
     * حساب المحصلة للترم
     * (مجموع الشهر1 + مجموع الشهر2 + مجموع الشهر3) ÷ 15
     */
    public static function calculateTermResult($studentId, $subjectId, $term): float
    {
        $monthScores = self::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->where('term', $term)
            ->whereIn('month', [1, 2, 3])
            ->get();

        $totalMonths = $monthScores->sum('month_total');
        return round($totalMonths / 15, 2);
    }
}
