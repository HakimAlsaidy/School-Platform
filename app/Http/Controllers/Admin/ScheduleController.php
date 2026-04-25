<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    // الحصص الافتراضية
    private $defaultPeriods = [
        1 => ['start' => '07:30', 'end' => '08:15'],
        2 => ['start' => '08:20', 'end' => '09:05'],
        3 => ['start' => '09:10', 'end' => '09:55'],
        4 => ['start' => '10:00', 'end' => '10:45'],
        5 => ['start' => '11:00', 'end' => '11:45'],
        6 => ['start' => '11:50', 'end' => '12:35'],
        7 => ['start' => '12:40', 'end' => '13:25'],
    ];

    /**
     * عرض صفحة الجدول الدراسي الرئيسية
     */
    public function index()
    {
        $classroomsCount = Classroom::count();
        $teachersCount = Teacher::count();
        $schedulesCount = Schedule::count();
        
        return view('admin.schedules.index', compact(
            'classroomsCount', 'teachersCount', 'schedulesCount'
        ));
    }

    /**
     * عرض جدول الفصول
     */
    public function classrooms(Request $request)
    {
        $classrooms = Classroom::with('grade')->get();
        $teachers = Teacher::with('user', 'subjects')->get();
        $subjects = Subject::all();
        
        $selectedClassroom = null;
        $schedules = collect();
        $days = Schedule::$days;
        
        if ($request->filled('classroom_id')) {
            $selectedClassroom = Classroom::with('grade')->find($request->classroom_id);
            $schedules = Schedule::with(['subject', 'teacher.user'])
                ->where('classroom_id', $request->classroom_id)
                ->get()
                ->groupBy('day');
        }
        
        return view('admin.schedules.classrooms', compact(
            'classrooms', 'teachers', 'subjects', 'selectedClassroom', 
            'schedules', 'days'
        ))->with('periods', $this->defaultPeriods);
    }

    /**
     * عرض جدول المعلمين
     */
    public function teachers(Request $request)
    {
        $teachers = Teacher::with(['user', 'subjects'])->get();
        $selectedTeacher = null;
        $schedules = collect();
        $days = Schedule::$days;
        
        if ($request->filled('teacher_id')) {
            $selectedTeacher = Teacher::with(['user', 'subjects'])->find($request->teacher_id);
            $schedules = Schedule::with(['subject', 'classroom.grade'])
                ->where('teacher_id', $request->teacher_id)
                ->get()
                ->groupBy('day');
        }
        
        return view('admin.schedules.teachers', compact(
            'teachers', 'selectedTeacher', 'schedules', 'days'
        ))->with('periods', $this->defaultPeriods);
    }

    /**
     * حفظ حصة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'day' => ['required', 'in:saturday,sunday,monday,tuesday,wednesday'],
            'period_number' => ['required', 'integer', 'min:1', 'max:7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // التحقق من عدم وجود تعارض
        $conflict = Schedule::where('classroom_id', $validated['classroom_id'])
            ->where('day', $validated['day'])
            ->where('period_number', $validated['period_number'])
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->with('error', 'يوجد حصة أخرى في نفس الوقت!')
                ->withInput();
        }

        // التحقق من تعارض المعلم
        $teacherConflict = Schedule::where('teacher_id', $validated['teacher_id'])
            ->where('day', $validated['day'])
            ->where('period_number', $validated['period_number'])
            ->exists();

        if ($teacherConflict) {
            return redirect()->back()
                ->with('error', 'المعلم لديه حصة أخرى في نفس الوقت!')
                ->withInput();
        }

        $validated['school_id'] = auth()->user()->school_id;
        $schedule = Schedule::create($validated);

        // إرسال إشعار للمعلم
        $teacher = Teacher::with('user')->find($validated['teacher_id']);
        if ($teacher && $teacher->user) {
            $schedule->load('subject');
            Notification::scheduleAdded($teacher->user, $schedule);
        }

        return redirect()->back()->with('success', 'تم إضافة الحصة بنجاح');
    }

    /**
     * تحديث حصة
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // التحقق من تعارض المعلم
        $teacherConflict = Schedule::where('teacher_id', $validated['teacher_id'])
            ->where('day', $schedule->day)
            ->where('period_number', $schedule->period_number)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($teacherConflict) {
            return redirect()->back()
                ->with('error', 'المعلم لديه حصة أخرى في نفس الوقت!')
                ->withInput();
        }

        $schedule->update($validated);

        return redirect()->back()->with('success', 'تم تحديث الحصة بنجاح');
    }

    /**
     * حذف حصة
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->back()->with('success', 'تم حذف الحصة بنجاح');
    }

    /**
     * نسخ جدول فصل لفصل آخر
     */
    public function copy(Request $request)
    {
        $validated = $request->validate([
            'from_classroom_id' => ['required', 'exists:classrooms,id'],
            'to_classroom_id' => ['required', 'exists:classrooms,id', 'different:from_classroom_id'],
        ]);

        $sourceSchedules = Schedule::where('classroom_id', $validated['from_classroom_id'])->get();
        $schoolId = auth()->user()->school_id;

        foreach ($sourceSchedules as $schedule) {
            Schedule::create([
                'classroom_id' => $validated['to_classroom_id'],
                'subject_id' => $schedule->subject_id,
                'teacher_id' => $schedule->teacher_id,
                'day' => $schedule->day,
                'period_number' => $schedule->period_number,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'school_id' => $schoolId,
            ]);
        }

        return redirect()->back()->with('success', 'تم نسخ الجدول بنجاح');
    }

    /**
     * حذف جدول فصل كامل
     */
    public function clearClassroom(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
        ]);

        Schedule::where('classroom_id', $validated['classroom_id'])->delete();

        return redirect()->back()->with('success', 'تم حذف جميع حصص الفصل');
    }
}
