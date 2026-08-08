<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\Bus;
use App\Models\TransportRoute;
use App\Models\QuestionBank;
use App\Models\OnlineQuiz;
use App\Models\QuizAttempt;
use App\Models\ClassroomMaterial;
use App\Models\Student;
use Illuminate\Http\Request;

class FeatureOverviewController extends Controller
{
    /**
     * نظرة عامة على جميع الميزات عبر المنصة
     * (للإشراف على استخدام الميزات في جميع المدارس)
     */
    public function index()
    {
        // إجمالي استخدام الميزات عبر جميع المدارس
        $totals = [
            'fees' => Fee::withoutGlobalScope('school')->count(),
            'payments' => Payment::withoutGlobalScope('school')->count(),
            'expenses' => Expense::withoutGlobalScope('school')->count(),
            'incomes' => Income::withoutGlobalScope('school')->count(),
            'books' => Book::withoutGlobalScope('school')->count(),
            'book_loans' => BookLoan::withoutGlobalScope('school')->count(),
            'buses' => Bus::withoutGlobalScope('school')->count(),
            'transport_routes' => TransportRoute::withoutGlobalScope('school')->count(),
            'question_bank' => QuestionBank::withoutGlobalScope('school')->count(),
            'online_quizzes' => OnlineQuiz::withoutGlobalScope('school')->count(),
            'quiz_attempts' => QuizAttempt::withoutGlobalScope('school')->count(),
            'materials' => ClassroomMaterial::withoutGlobalScope('school')->count(),
        ];

        // الإيرادات والمصروفات الكلية
        $totals['total_income'] = Income::withoutGlobalScope('school')->sum('amount');
        $totals['total_expense'] = Expense::withoutGlobalScope('school')->sum('amount');
        $totals['total_payments'] = Payment::withoutGlobalScope('school')->sum('amount');
        $totals['net'] = $totals['total_income'] - $totals['total_expense'];

        // تفاصيل استخدام الميزات لكل مدرسة
        $schoolStats = School::withCount([
            'students',
            'teachers',
            'classrooms',
        ])->orderByDesc('id')->get()->map(function ($school) {
            return [
                'id' => $school->id,
                'name' => $school->name,
                'subdomain' => $school->subdomain,
                'is_active' => $school->is_active,
                // تأكد من أن العدادات تشمل مقيّد المدرسة فقط
                'students_count' => $school->students_count,
                'teachers_count' => $school->teachers_count,
                'classrooms_count' => $school->classrooms_count,
                'fees' => Fee::where('school_id', $school->id)->count(),
                'payments' => Payment::where('school_id', $school->id)->count(),
                'expenses' => Expense::where('school_id', $school->id)->count(),
                'incomes' => Income::where('school_id', $school->id)->count(),
                'books' => Book::where('school_id', $school->id)->count(),
                'book_loans' => BookLoan::where('school_id', $school->id)->count(),
                'buses' => Bus::where('school_id', $school->id)->count(),
                'transport_routes' => TransportRoute::where('school_id', $school->id)->count(),
                'question_bank' => QuestionBank::where('school_id', $school->id)->count(),
                'quizzes' => OnlineQuiz::where('school_id', $school->id)->count(),
                // quiz_attempts لا يحتوي على school_id مباشرة؛ نحسبها عبر الاختبارات التابعة للمدرسة
                'quiz_attempts' => QuizAttempt::whereIn('online_quiz_id', OnlineQuiz::where('school_id', $school->id)->pluck('id'))->count(),
                'materials' => ClassroomMaterial::where('school_id', $school->id)->count(),
            ];
        });

        // المدارس التي تستخدم كل ميزة
        $featureAdoption = $this->getFeatureAdoption();

        return view('superadmin.features.index', compact('totals', 'schoolStats', 'featureAdoption'));
    }

/**
     * نسبة اعتماد كل ميزة بين المدارس
     */
    protected function getFeatureAdoption(): array
    {
        $schoolCount = max(School::count(), 1);

        // عدد المدارس التي تستخدم كل ميزة (أو استعلام مباشر)
        $featureSchools = [
            'fees' => Fee::withoutGlobalScope('school')->distinct('school_id')->count('school_id'),
            'library' => Book::withoutGlobalScope('school')->distinct('school_id')->count('school_id'),
            'transport' => Bus::withoutGlobalScope('school')->distinct('school_id')->count('school_id'),
            'quizzes' => OnlineQuiz::withoutGlobalScope('school')->distinct('school_id')->count('school_id'),
            'materials' => ClassroomMaterial::withoutGlobalScope('school')->distinct('school_id')->count('school_id'),
            'accounting' => Income::withoutGlobalScope('school')->distinct('school_id')->count('school_id'),
        ];

        $adoption = [];
        foreach ($featureSchools as $feature => $count) {
            $adoption[$feature] = [
                'schools' => $count,
                'percentage' => round(($count / $schoolCount) * 100),
            ];
        }

        return $adoption;
    }

    /**
     * استخدام الميزات عبر جميع المدارس (بدون نطاق المدرسة)
     */
    protected function countGlobal($model, string $column = 'school_id'): int
    {
        return $model::withoutGlobalScope('school')->count();
    }
}

