<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ClassroomMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $query = ClassroomMaterial::with(['classroom.grade', 'subject'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $materials = $query->latest()->paginate(15);
        $classrooms = $teacher->classrooms()->with('grade')->get();
        $subjects = $teacher->subjects;

        return view('teacher.materials.index', compact('materials', 'classrooms', 'subjects'));
    }

    public function store(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:file,link,text,video'],
            'file' => ['required_if:type,file', 'file', 'max:20480'],
            'external_url' => ['nullable', 'url'],
            'content' => ['nullable', 'string'],
        ]);

        $data = [
            'school_id' => auth()->user()->school_id,
            'classroom_id' => $validated['classroom_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $teacher->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'external_url' => $validated['external_url'] ?? null,
            'content' => $validated['content'] ?? null,
        ];

        // رفع الملف
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', 'public');
            $data['file_path'] = $path;
        }

        ClassroomMaterial::create($data);

        ActivityLog::log('add_material', "إضافة مادة دراسية: {$validated['title']}");
        return back()->with('success', 'تمت إضافة المادة الدراسية بنجاح.');
    }

    public function destroy(ClassroomMaterial $material)
    {
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return back()->with('success', 'تم حذف المادة الدراسية.');
    }
}
