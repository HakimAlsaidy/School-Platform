<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        $days = Schedule::$days;
        
        // الحصول على جميع حصص المعلم
        $schedules = Schedule::with(['classroom.grade', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->get()
            ->groupBy('day');
        
        // الحصص الافتراضية
        $periods = [
            1 => ['start' => '07:30', 'end' => '08:10'],
            2 => ['start' => '08:15', 'end' => '08:55'],
            3 => ['start' => '09:00', 'end' => '09:40'],
            4 => ['start' => '09:45', 'end' => '10:25'],
            5 => ['start' => '10:40', 'end' => '11:20'],
            6 => ['start' => '11:25', 'end' => '12:05'],
            7 => ['start' => '12:10', 'end' => '12:50'],
        ];
        
        // إحصائيات
        $stats = [
            'total_periods' => $schedules->flatten()->count(),
            'subjects' => $schedules->flatten()->unique('subject_id')->count(),
            'classrooms' => $schedules->flatten()->unique('classroom_id')->count(),
            'days_count' => $schedules->keys()->count(),
        ];
        
        return view('teacher.schedule.index', compact('schedules', 'days', 'periods', 'stats'));
    }
}
