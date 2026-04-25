<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guardian = Auth::user()->guardian;
        $students = $guardian->students()->with([
            'classroom.grade',
            'attendances' => fn($q) => $q->latest()->take(5),
            'scores' => fn($q) => $q->latest()->take(5)->with('subject'),
        ])->get();

        // إحصائيات الأبناء
        $stats = [
            'children' => $students->count(),
            'average_attendance' => $students->avg(fn($s) => $s->attendance_rate),
            'average_score' => $students->avg(fn($s) => $s->average_score),
        ];

        // الإعلانات
        $announcements = Announcement::active()
            ->forTarget('parents')
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

        return view('guardian.dashboard', compact(
            'students',
            'stats',
            'announcements',
            'unreadMessages'
        ));
    }
}
