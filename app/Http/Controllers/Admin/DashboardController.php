<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Event;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::where('is_active', true)->count(),
            'teachers' => Teacher::count(),
            'guardians' => Guardian::count(),
            'classrooms' => Classroom::count(),
            'grades' => Grade::count(),
            'subjects' => Subject::count(),
        ];

        $recentStudents = Student::with(['classroom.grade', 'guardian.user'])
            ->latest()
            ->take(5)
            ->get();

        $todayAttendance = Attendance::whereDate('date', today())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $announcements = Announcement::with('author')
            ->active()
            ->latest()
            ->take(5)
            ->get();

        $upcomingEvents = Event::where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentStudents',
            'todayAttendance',
            'announcements',
            'upcomingEvents'
        ));
    }
}
