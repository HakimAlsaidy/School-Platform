<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * عرض جميع الإشعارات
     */
    public function index()
    {
        $notifications = Auth::user()
            ->userNotifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * الحصول على الإشعارات (AJAX)
     */
    public function get(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $notifications = Auth::user()
            ->userNotifications()
            ->latest()
            ->take($limit)
            ->get();

        $unreadCount = Auth::user()->unread_notifications_count;

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'icon' => $n->icon,
                    'color' => $n->color,
                    'action_url' => $n->action_url,
                    'action_text' => $n->action_text,
                    'is_read' => $n->isRead(),
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(Notification $notification)
    {
        // التحقق من أن الإشعار للمستخدم الحالي
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        // إذا كان هناك رابط، انتقل إليه
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        Notification::markAllAsRead(Auth::id());

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    /**
     * حذف إشعار
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم حذف الإشعار');
    }

    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll()
    {
        Auth::user()->userNotifications()->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم حذف جميع الإشعارات');
    }
}
