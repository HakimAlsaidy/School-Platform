<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target' => ['required', 'in:all,teachers,parents,students'],
            'is_pinned' => ['nullable'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $validated['author_id'] = auth()->id();
        $validated['is_pinned'] = $request->has('is_pinned');
        $validated['school_id'] = auth()->user()->school_id;

        $announcement = Announcement::create($validated);

        ActivityLog::log('create_announcement', "إضافة إعلان: {$announcement->title}", $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم إضافة الإعلان بنجاح.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target' => ['required', 'in:all,teachers,parents,students'],
            'is_pinned' => ['nullable'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $validated['is_pinned'] = $request->has('is_pinned');

        $announcement->update($validated);

        ActivityLog::log('update_announcement', "تحديث الإعلان: {$announcement->title}", $announcement);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم تحديث الإعلان بنجاح.');
    }

    public function destroy(Announcement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        ActivityLog::log('delete_announcement', "حذف الإعلان: {$title}");

        return redirect()->route('admin.announcements.index')
            ->with('success', 'تم حذف الإعلان بنجاح.');
    }
}
