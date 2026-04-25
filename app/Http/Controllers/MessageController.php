<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->orWhere('sender_id', Auth::id())
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(20);

        return view('messages.index', compact('messages'));
    }

    public function inbox()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->with('sender')
            ->latest()
            ->paginate(20);

        return view('messages.inbox', compact('messages'));
    }

    public function sent()
    {
        $messages = Message::where('sender_id', Auth::id())
            ->with('receiver')
            ->latest()
            ->paginate(20);

        return view('messages.sent', compact('messages'));
    }

    public function create()
    {
        // فلترة المستخدمين حسب المدرسة الحالية
        $currentUser = Auth::user();
        $query = User::where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->with('role');

        // إذا لم يكن Super Admin، فلتر حسب المدرسة
        if (!$currentUser->isSuperAdmin() && $currentUser->school_id) {
            $query->where('school_id', $currentUser->school_id);
        }

        $users = $query->get();

        return view('messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $validated['sender_id'] = Auth::id();

        $message = Message::create($validated);

        ActivityLog::log('send_message', "إرسال رسالة", $message);

        return redirect()->route('messages.sent')
            ->with('success', 'تم إرسال الرسالة بنجاح.');
    }

    public function show(Message $message)
    {
        // التحقق من الصلاحية
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        // تحديد كمقروءة
        if ($message->receiver_id === Auth::id() && !$message->is_read) {
            $message->markAsRead();
        }

        return view('messages.show', compact('message'));
    }

    public function reply(Request $request, Message $message)
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $reply = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $message->sender_id,
            'subject' => 'رد: ' . $message->subject,
            'content' => $validated['content'],
        ]);

        ActivityLog::log('reply_message', "الرد على رسالة", $reply);

        return redirect()->route('messages.show', $reply)
            ->with('success', 'تم إرسال الرد بنجاح.');
    }

    public function destroy(Message $message)
    {
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()->route('messages.inbox')
            ->with('success', 'تم حذف الرسالة بنجاح.');
    }
}
