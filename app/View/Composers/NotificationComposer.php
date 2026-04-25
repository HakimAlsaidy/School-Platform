<?php

namespace App\View\Composers;

use App\Models\Notification;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // عدد الإشعارات غير المقروءة
        $unreadNotificationsCount = $user->unread_notifications_count ?? 0;

        // آخر الإشعارات
        $recentNotifications = $user->userNotifications()
            ->latest()
            ->take(10)
            ->get();

        $view->with([
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'recentNotifications' => $recentNotifications,
        ]);
    }
}
