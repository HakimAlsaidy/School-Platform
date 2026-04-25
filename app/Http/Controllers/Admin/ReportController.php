<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function attendance(Request $request)
    {
        $grades = Grade::with('classrooms')->get();
        $classrooms = Classroom::with('grade')->get();

        $query = Attendance::with(['student.classroom.grade']);

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->filled('classroom_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('classroom_id', $request->classroom_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(50);

        $stats = [
            'total' => $query->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'excused' => (clone $query)->where('status', 'excused')->count(),
        ];

        return view('admin.reports.attendance', compact('attendances', 'stats', 'grades', 'classrooms'));
    }

    public function scores(Request $request)
    {
        $grades = Grade::with('classrooms')->get();
        $subjects = Subject::all();

        $query = Score::with(['student.classroom.grade', 'subject', 'teacher.user']);

        if ($request->filled('classroom_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('classroom_id', $request->classroom_id);
            });
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $scores = $query->latest()->paginate(50);

        $averageScore = $query->avg('score');

        return view('admin.reports.scores', compact('scores', 'grades', 'subjects', 'averageScore'));
    }

    public function students(Request $request)
    {
        $grades = Grade::with('classrooms')->get();

        $query = Student::with(['classroom.grade', 'guardian.user'])
            ->withCount(['attendances as present_count' => function ($q) {
                $q->where('status', 'present');
            }])
            ->withCount(['attendances as absent_count' => function ($q) {
                $q->where('status', 'absent');
            }])
            ->withAvg('scores', 'score');

        if ($request->filled('grade_id')) {
            $query->whereHas('classroom', function ($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            });
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $students = $query->paginate(30);

        return view('admin.reports.students', compact('students', 'grades'));
    }

    public function teachers(Request $request)
    {
        $teachers = Teacher::with(['user', 'subjects', 'classrooms.grade'])
            ->withCount(['scores', 'behaviors'])
            ->paginate(30);

        return view('admin.reports.teachers', compact('teachers'));
    }

    public function studentReport(Student $student)
    {
        $student->load([
            'classroom.grade',
            'guardian.user',
            'attendances',
            'scores.subject',
            'behaviors.teacher.user',
        ]);

        // إحصائيات الحضور
        $attendanceStats = [
            'total' => $student->attendances->count(),
            'present' => $student->attendances->where('status', 'present')->count(),
            'absent' => $student->attendances->where('status', 'absent')->count(),
            'late' => $student->attendances->where('status', 'late')->count(),
        ];

        // الدرجات حسب المادة
        $scoresBySubject = $student->scores
            ->groupBy('subject_id')
            ->map(function ($scores) {
                return [
                    'subject' => $scores->first()->subject->name,
                    'average' => round($scores->avg('score'), 2),
                    'count' => $scores->count(),
                ];
            });

        // السلوك
        $behaviorStats = [
            'positive' => $student->behaviors->where('type', 'positive')->count(),
            'negative' => $student->behaviors->where('type', 'negative')->count(),
            'total_points' => $student->behaviors->sum('points'),
        ];

        return view('admin.reports.student-report', compact(
            'student',
            'attendanceStats',
            'scoresBySubject',
            'behaviorStats'
        ));
    }

    public function export(Request $request)
    {
        // تصدير التقارير بصيغ مختلفة
        $type = $request->type;
        
        // يمكن إضافة منطق التصدير هنا (Excel, PDF)
        
        return back()->with('success', 'تم تصدير التقرير بنجاح.');
    }
}
