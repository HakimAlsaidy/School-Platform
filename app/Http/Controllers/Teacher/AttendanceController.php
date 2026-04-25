<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();

        $attendances = collect();
        $selectedClassroom = null;
        $selectedDate = $request->date ?? today()->toDateString();

        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::with(['students', 'grade'])->find($request->classroom_id);
            
            $attendances = Attendance::with('student')
                ->whereHas('student', function ($q) use ($request) {
                    $q->where('classroom_id', $request->classroom_id);
                })
                ->whereDate('date', $selectedDate)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance.index', compact(
            'classrooms',
            'attendances',
            'selectedClassroom',
            'selectedDate'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:students,id'],
            'attendance.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendance.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated['attendance'] as $record) {
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                    'recorded_by' => Auth::id(),
                ]
            );

            // إرسال إشعار لولي الأمر عند الغياب أو التأخير
            if (in_array($record['status'], ['absent', 'late'])) {
                $student = Student::with('guardian.user')->find($record['student_id']);
                if ($student && $student->guardian && $student->guardian->user) {
                    $statuses = ['absent' => 'غائب', 'late' => 'متأخر'];
                    $statusText = $statuses[$record['status']];
                    
                    Notification::send(
                        $student->guardian->user->id,
                        'تنبيه حضور',
                        "{$student->name} تم تسجيله {$statusText} اليوم",
                        'attendance',
                        route('parent.students.attendance', $student),
                        'عرض السجل'
                    );
                }
            }
        }

        ActivityLog::log('record_attendance', "تسجيل الحضور للفصل");

        return redirect()->back()->with('success', 'تم تسجيل الحضور بنجاح.');
    }

    public function report(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classrooms = $teacher->classrooms()->with('grade')->get();

        $query = Attendance::with(['student.classroom.grade']);

        if ($request->filled('classroom_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('classroom_id', $request->classroom_id);
            });
        } else {
            $classroomIds = $classrooms->pluck('id');
            $query->whereHas('student', function ($q) use ($classroomIds) {
                $q->whereIn('classroom_id', $classroomIds);
            });
        }

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $attendances = $query->latest('date')->paginate(50);

        $stats = [
            'present' => (clone $query)->where('status', 'present')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'excused' => (clone $query)->where('status', 'excused')->count(),
        ];

        return view('teacher.attendance.report', compact('classrooms', 'attendances', 'stats'));
    }
}
