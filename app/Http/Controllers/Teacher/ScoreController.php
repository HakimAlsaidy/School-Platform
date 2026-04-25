<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    /**
     * عرض الفصول أو الترمين أو الشهور أو الدرجات
     */
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        $scores = collect();
        $selectedClassroom = null;
        $selectedSubject = null;

        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::with(['students', 'grade'])->find($request->classroom_id);
            
            if ($request->filled('subject_id')) {
                $selectedSubject = Subject::find($request->subject_id);
            }
            
            // عرض الدرجات إذا تم تحديد الفصل والمادة والترم والشهر
            if ($request->filled(['subject_id', 'term', 'month'])) {
                $query = Score::with(['student', 'subject'])
                    ->where('teacher_id', $teacher->id)
                    ->where('subject_id', $request->subject_id)
                    ->where('term', $request->term)
                    ->where('month', $request->month)
                    ->whereHas('student', function ($q) use ($request) {
                        $q->where('classroom_id', $request->classroom_id);
                    });

                $scores = $query->latest()->paginate(50);
            }
        }

        return view('teacher.scores.index', compact(
            'classrooms',
            'subjects',
            'scores',
            'selectedClassroom',
            'selectedSubject'
        ));
    }

    /**
     * صفحة إضافة درجات شهر معين
     */
    public function create(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with(['grade', 'students'])->get();
        $subjects = $teacher->subjects;

        $students = collect();
        $classroom = null;
        $subject = null;
        $term = $request->get('term', 1);
        $month = $request->get('month', 1);
        
        if ($request->filled('classroom_id')) {
            $classroom = $classrooms->find($request->classroom_id);
            $students = Student::where('classroom_id', $request->classroom_id)
                ->where('is_active', true)
                ->get();
        }
        
        if ($request->filled('subject_id')) {
            $subject = $subjects->find($request->subject_id);
        }

        // جلب الدرجات الموجودة (إن وجدت)
        $existingScores = collect();
        if ($classroom && $subject) {
            $existingScores = Score::where('subject_id', $subject->id)
                ->where('term', $term)
                ->where('month', $month)
                ->whereHas('student', fn($q) => $q->where('classroom_id', $classroom->id))
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.scores.create', compact(
            'classrooms', 'subjects', 'students', 'classroom', 'subject',
            'term', 'month', 'existingScores'
        ));
    }

    /**
     * حفظ درجات شهر معين
     */
    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term' => ['required', 'in:1,2'],
            'month' => ['required', 'in:1,2,3'],
            'semester' => ['nullable', 'string', 'max:50'],
            'scores' => ['required', 'array'],
            'scores.*.student_id' => ['required', 'exists:students,id'],
            'scores.*.attendance' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'scores.*.homework' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'scores.*.discipline' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'scores.*.written' => ['nullable', 'numeric', 'min:0', 'max:40'],
            'scores.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['scores'] as $scoreData) {
            $attendance = floatval($scoreData['attendance'] ?? 0);
            $homework = floatval($scoreData['homework'] ?? 0);
            $discipline = floatval($scoreData['discipline'] ?? 0);
            $written = floatval($scoreData['written'] ?? 0);
            
            // مجموع الشهر = الحضور + الواجبات + المواظبة + التحريري
            $monthTotal = $attendance + $homework + $discipline + $written;

            Score::updateOrCreate(
                [
                    'student_id' => $scoreData['student_id'],
                    'subject_id' => $validated['subject_id'],
                    'term' => $validated['term'],
                    'month' => $validated['month'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'exam_type' => 'quiz', // استخدام قيمة موجودة في ENUM
                    'exam_date' => now(),
                    'score' => $monthTotal,
                    'max_score' => 100,
                    'semester' => $validated['semester'] ?? 'الفصل الدراسي',
                    'attendance' => $attendance,
                    'homework' => $homework,
                    'discipline' => $discipline,
                    'written' => $written,
                    'month_total' => $monthTotal,
                    'notes' => $scoreData['notes'] ?? null,
                ]
            );

            // إرسال إشعار لولي الأمر
            $student = Student::with('guardian.user')->find($scoreData['student_id']);
            if ($student && $student->guardian && $student->guardian->user) {
                $subject = Subject::find($validated['subject_id']);
                Notification::send(
                    $student->guardian->user->id,
                    'درجة جديدة',
                    "حصل {$student->name} على {$monthTotal} في {$subject->name}",
                    'score',
                    route('parent.students.scores', $student),
                    'عرض الدرجات'
                );
            }
        }

        ActivityLog::log('record_scores', "تسجيل درجات الشهر {$validated['month']} - الترم {$validated['term']}");

        return redirect()->route('teacher.scores.index', [
            'classroom_id' => $validated['classroom_id'],
            'subject_id' => $validated['subject_id'],
            'term' => $validated['term']
        ])->with('success', 'تم تسجيل درجات الشهر بنجاح.');
    }

    /**
     * صفحة المحصلة والنهائي
     */
    public function finalScores(Request $request)
    {
        $teacher = Auth::user()->teacher;
        
        $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term' => ['required', 'in:1,2'],
        ]);

        $classroom = Classroom::with(['students', 'grade'])->find($request->classroom_id);
        $subject = Subject::find($request->subject_id);
        $term = $request->term;

        $students = Student::where('classroom_id', $request->classroom_id)
            ->where('is_active', true)
            ->get();

        // حساب المحصلة لكل طالب
        $studentsData = [];
        foreach ($students as $student) {
            // جلب مجاميع الشهور الثلاثة
            $monthScores = Score::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('term', $term)
                ->whereIn('month', [1, 2, 3])
                ->get()
                ->keyBy('month');

            $month1Total = $monthScores->get(1)?->month_total ?? 0;
            $month2Total = $monthScores->get(2)?->month_total ?? 0;
            $month3Total = $monthScores->get(3)?->month_total ?? 0;

            // المحصلة = (مجموع الشهر1 + مجموع الشهر2 + مجموع الشهر3) ÷ 15
            $result = round(($month1Total + $month2Total + $month3Total) / 15, 2);

            // جلب درجة النهائي إن وجدت
            $finalScore = Score::where('student_id', $student->id)
                ->where('subject_id', $subject->id)
                ->where('term', $term)
                ->whereNull('month')
                ->first();

            $studentsData[] = [
                'student' => $student,
                'month1' => $month1Total,
                'month2' => $month2Total,
                'month3' => $month3Total,
                'result' => $result,
                'final' => $finalScore?->final_30 ?? 0,
                'total' => $result + ($finalScore?->final_30 ?? 0),
            ];
        }

        return view('teacher.scores.final', compact(
            'classroom', 'subject', 'term', 'studentsData'
        ));
    }

    /**
     * حفظ درجات النهائي
     */
    public function storeFinal(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term' => ['required', 'in:1,2'],
            'scores' => ['required', 'array'],
            'scores.*.student_id' => ['required', 'exists:students,id'],
            'scores.*.final_30' => ['nullable', 'numeric', 'min:0', 'max:30'],
        ]);

        foreach ($validated['scores'] as $scoreData) {
            $studentId = $scoreData['student_id'];
            $final30 = floatval($scoreData['final_30'] ?? 0);

            // حساب المحصلة
            $result = Score::calculateTermResult($studentId, $validated['subject_id'], $validated['term']);
            
            // المجموع النهائي للمادة = المحصلة + النهائي
            $total50 = round($result + $final30, 2);

            Score::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $validated['subject_id'],
                    'term' => $validated['term'],
                    'month' => null, // null يعني المحصلة والنهائي
                ],
                [
                    'teacher_id' => $teacher->id,
                    'exam_type' => 'final', // استخدام قيمة موجودة في ENUM
                    'exam_date' => now(),
                    'score' => $total50,
                    'max_score' => 50,
                    'semester' => 'الفصل الدراسي',
                    'total_20' => $result,
                    'final_30' => $final30,
                    'total_50' => $total50,
                ]
            );
        }

        ActivityLog::log('record_final_scores', "تسجيل درجات النهائي - الترم {$validated['term']}");

        return redirect()->route('teacher.scores.index', [
            'classroom_id' => $validated['classroom_id'],
            'subject_id' => $validated['subject_id'],
            'term' => $validated['term']
        ])->with('success', 'تم تسجيل درجات النهائي بنجاح.');
    }

    public function edit(Score $score)
    {
        $this->authorize('update', $score);

        return view('teacher.scores.edit', compact('score'));
    }

    public function update(Request $request, Score $score)
    {
        $this->authorize('update', $score);

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:' . $score->max_score],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $score->update($validated);

        ActivityLog::log('update_score', "تحديث درجة الطالب", $score);

        return redirect()->route('teacher.scores.index')
            ->with('success', 'تم تحديث الدرجة بنجاح.');
    }

    public function destroy(Score $score)
    {
        $this->authorize('delete', $score);

        $score->delete();

        ActivityLog::log('delete_score', "حذف درجة");

        return redirect()->back()->with('success', 'تم حذف الدرجة بنجاح.');
    }

    public function report(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        $query = Score::with(['student.classroom.grade', 'subject'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('classroom_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('classroom_id', $request->classroom_id);
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $scores = $query->latest()->paginate(50);

        $averageScore = $query->avg('score');

        return view('teacher.scores.report', compact('classrooms', 'subjects', 'scores', 'averageScore'));
    }
}
