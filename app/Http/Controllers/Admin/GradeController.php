<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GradeController extends Controller
{
    public function index()
    {
        $query = Grade::withCount(['classrooms', 'students']);
        
        // تحقق من وجود جدول grade_subject
        if (Schema::hasTable('grade_subject')) {
            $query->with('subjects');
        }
        
        $grades = $query->get();
        $subjects = Subject::all();

        return view('admin.grades.index', compact('grades', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.grades.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:grades,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['nullable', 'integer', 'min:0'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
        ]);

        $grade = Grade::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
            'school_id' => auth()->user()->school_id,
        ]);

        if (!empty($validated['subjects']) && Schema::hasTable('grade_subject')) {
            $grade->subjects()->attach($validated['subjects']);
        }

        ActivityLog::log('create_grade', "إضافة صف دراسي: {$grade->name}", $grade);

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم إضافة الصف الدراسي بنجاح.');
    }

    public function edit(Grade $grade)
    {
        $subjects = Subject::all();
        if (Schema::hasTable('grade_subject')) {
            $grade->load('subjects');
        }
        return view('admin.grades.edit', compact('grade', 'subjects'));
    }

    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:grades,name,' . $grade->id],
            'description' => ['nullable', 'string', 'max:500'],
            'order' => ['nullable', 'integer', 'min:0'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
        ]);

        $grade->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order' => $validated['order'] ?? 0,
        ]);

        if (Schema::hasTable('grade_subject')) {
            $grade->subjects()->sync($validated['subjects'] ?? []);
        }

        ActivityLog::log('update_grade', "تحديث الصف الدراسي: {$grade->name}", $grade);

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم تحديث الصف الدراسي بنجاح.');
    }

    public function destroy(Grade $grade)
    {
        $name = $grade->name;
        if (Schema::hasTable('grade_subject')) {
            $grade->subjects()->detach();
        }
        $grade->delete();

        ActivityLog::log('delete_grade', "حذف الصف الدراسي: {$name}");

        return redirect()->route('admin.grades.index')
            ->with('success', 'تم حذف الصف الدراسي بنجاح.');
    }
}
