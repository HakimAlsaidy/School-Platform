<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $students = $this->guardianStudentsQuery()
            ->with(['classroom.grade'])
            ->latest('id')
            ->get();

        return view('guardian.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $student->load([
            'classroom.grade',
            'attendances' => fn($q) => $q->latest()->take(30),
            'scores.subject',
            'behaviors.teacher.user',
            'submissions.assignment',
        ]);

        $attendanceCounts = $student->attendances
            ->groupBy('status')
            ->map->count();

        $attendanceStats = [
            'total' => $student->attendances->count(),
            'present' => $attendanceCounts->get('present', 0),
            'absent' => $attendanceCounts->get('absent', 0),
            'late' => $attendanceCounts->get('late', 0),
        ];

        $scoresBySubject = $student->scores
            ->groupBy('subject_id')
            ->map(function ($scores) {
                return [
                    'subject' => $scores->first()->subject->name,
                    'average' => round($scores->avg('score'), 2),
                    'scores' => $scores,
                ];
            });

        return view('guardian.students.show', compact(
            'student',
            'attendanceStats',
            'scoresBySubject'
        ));
    }

    public function attendance(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $attendances = $student->attendances()
            ->latest('date')
            ->paginate(30);

        $statusCounts = $student->attendances()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'present' => (int) $statusCounts->get('present', 0),
            'absent' => (int) $statusCounts->get('absent', 0),
            'late' => (int) $statusCounts->get('late', 0),
            'excused' => (int) $statusCounts->get('excused', 0),
        ];

        $totalAttendance = $stats['present'] + $stats['absent'] + $stats['late'] + $stats['excused'];
        $attendanceRate = $totalAttendance > 0 ? round(($stats['present'] / $totalAttendance) * 100, 2) : 0;

        return view('guardian.students.attendance', compact('student', 'attendances', 'stats', 'attendanceRate'));
    }

    public function scores(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $scores = $student->scores()
            ->with(['subject', 'teacher.user'])
            ->latest()
            ->paginate(30);

        $scoresCollection = $student->scores()
            ->with('subject')
            ->get();

        $scoresBySubject = $scoresCollection
            ->groupBy('subject_id')
            ->map(function ($scores) {
                return [
                    'subject' => $scores->first()->subject->name,
                    'average' => round($scores->avg('score'), 2),
                ];
            });

        $averageScore = round($scoresCollection->avg('score') ?? 0, 2);
        $highestScore = $scoresCollection->max('score') ?? 0;
        $lowestScore = $scoresCollection->min('score') ?? 0;
        $totalExams = $scoresCollection->count();
        $subjects = Subject::whereIn('id', $scoresCollection->pluck('subject_id')->unique())->get();

        return view('guardian.students.scores', compact('student', 'scores', 'scoresBySubject', 'averageScore', 'highestScore', 'lowestScore', 'totalExams', 'subjects'));
    }

    public function behaviors(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $behaviors = $student->behaviors()
            ->with(['teacher.user'])
            ->latest()
            ->paginate(30);

        $behaviorCounts = $student->behaviors()
            ->selectRaw('type, COUNT(*) as total, COALESCE(SUM(points), 0) as points_sum')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $stats = [
            'positive' => (int) optional($behaviorCounts->get('positive'))->total,
            'negative' => (int) optional($behaviorCounts->get('negative'))->total,
            'total_points' => (int) $student->behaviors()->sum('points'),
        ];

        return view('guardian.students.behaviors', compact('student', 'behaviors', 'stats'));
    }

    public function schedule(Student $student)
    {
        $this->authorizeStudentAccess($student);

        $schedules = $student->classroom->schedules()
            ->with(['subject', 'teacher.user'])
            ->orderBy('day')
            ->orderBy('period_number')
            ->get()
            ->groupBy('day');

        $days = [
            'saturday' => 'السبت',
            'sunday' => 'الأحد',
            'monday' => 'الاثنين',
            'tuesday' => 'الثلاثاء',
            'wednesday' => 'الأربعاء',
        ];

        // الحصص من 1 إلى 7
        $periods = [];
        $startTime = \Carbon\Carbon::createFromTime(7, 0);
        for ($i = 1; $i <= 7; $i++) {
            $periods[$i] = [
                'start' => $startTime->format('H:i'),
                'end' => $startTime->copy()->addMinutes(40)->format('H:i'),
            ];
            $startTime->addMinutes(45); // 40 دقيقة حصة + 5 دقائق استراحة
        }

        return view('guardian.students.schedule', compact('student', 'schedules', 'days', 'periods'));
    }

    private function guardianStudentsQuery()
    {
        return Auth::user()->guardian->students();
    }

    private function authorizeStudentAccess(Student $student): void
    {
        $ownsStudent = $this->guardianStudentsQuery()
            ->whereKey($student->id)
            ->exists();

        abort_unless($ownsStudent, 403);
    }
}
