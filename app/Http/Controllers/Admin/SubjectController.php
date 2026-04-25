<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('teachers')->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        $subject = Subject::create($validated);

        ActivityLog::log('create_subject', "إضافة مادة دراسية: {$subject->name}", $subject);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم إضافة المادة الدراسية بنجاح.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subject->id],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $subject->update($validated);

        ActivityLog::log('update_subject', "تحديث المادة الدراسية: {$subject->name}", $subject);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم تحديث المادة الدراسية بنجاح.');
    }

    public function destroy(Subject $subject)
    {
        $name = $subject->name;
        $subject->delete();

        ActivityLog::log('delete_subject', "حذف المادة الدراسية: {$name}");

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة الدراسية بنجاح.');
    }
}
