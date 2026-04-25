<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::with(['grade', 'students', 'teachers.user'])
            ->withCount('students');

        if ($request->filled('grade_id')) {
            $query->where('grade_id', $request->grade_id);
        }

        $classrooms = $query->paginate(15);
        $grades = Grade::withCount('students')->get();

        return view('admin.classrooms.index', compact('classrooms', 'grades'));
    }

    public function create()
    {
        $grades = Grade::all();
        $teachers = Teacher::with('user')->get();

        return view('admin.classrooms.create', compact('grades', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_id' => ['required', 'exists:grades,id'],
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        $classroom = Classroom::create($validated);

        ActivityLog::log('create_classroom', "إضافة فصل جديد: {$classroom->full_name}", $classroom);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'تم إضافة الفصل بنجاح.');
    }

    public function show(Classroom $classroom)
    {
        $classroom->load([
            'grade',
            'students.guardian.user',
            'teachers.user',
            'schedules.subject',
            'schedules.teacher.user',
        ]);

        return view('admin.classrooms.show', compact('classroom'));
    }

    public function edit(Classroom $classroom)
    {
        $grades = Grade::all();
        $teachers = Teacher::with('user')->get();

        return view('admin.classrooms.edit', compact('classroom', 'grades', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'grade_id' => ['required', 'exists:grades,id'],
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $classroom->update($validated);

        ActivityLog::log('update_classroom', "تحديث الفصل: {$classroom->full_name}", $classroom);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'تم تحديث الفصل بنجاح.');
    }

    public function destroy(Classroom $classroom)
    {
        $name = $classroom->full_name;
        $classroom->delete();

        ActivityLog::log('delete_classroom', "حذف الفصل: {$name}");

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'تم حذف الفصل بنجاح.');
    }

    /**
     * الحصول على عدد طلاب الصف (AJAX)
     */
    public function getGradeStudentsCount(Grade $grade)
    {
        $studentsCount = $grade->students()->count();
        $classroomsCount = $grade->classrooms()->count();
        
        return response()->json([
            'students_count' => $studentsCount,
            'classrooms_count' => $classroomsCount,
            'grade_name' => $grade->name,
        ]);
    }
}
