<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Message;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        $stats = [
            'classrooms' => $teacher->classrooms()->count(),
            'subjects' => $teacher->subjects()->count(),
            'students' => $teacher->classrooms()->withCount('students')->get()->sum('students_count'),
            'assignments' => $teacher->assignments()->count(),
        ];

        // جدول اليوم
        $todaySchedules = Schedule::with(['classroom.grade', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('day', strtolower(now()->format('l')))
            ->orderBy('start_time')
            ->get();

        // الواجبات القادمة
        $upcomingAssignments = Assignment::with(['classroom.grade', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // الإعلانات
        $announcements = Announcement::active()
            ->forTarget('teachers')
            ->latest()
            ->take(5)
            ->get();

        // الرسائل غير المقروءة
        $unreadMessages = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->with('sender')
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact(
            'stats',
            'todaySchedules',
            'upcomingAssignments',
            'announcements',
            'unreadMessages'
        ));
    }
}
