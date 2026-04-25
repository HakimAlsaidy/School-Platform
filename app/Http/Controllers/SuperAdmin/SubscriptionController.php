<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * عرض جميع الاشتراكات
     */
    public function index(Request $request)
    {
        $query = SchoolSubscription::with('school')->latest();

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('ends_at', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('ends_at', '<', now());
            } elseif ($request->status === 'trial') {
                $query->where('plan', 'free');
            }
        }

        $subscriptions = $query->paginate(20);

        $stats = [
            'total' => SchoolSubscription::count(),
            'active' => SchoolSubscription::where('is_active', true)->where('ends_at', '>', now())->count(),
            'expired' => SchoolSubscription::where('ends_at', '<', now())->count(),
            'trial' => SchoolSubscription::where('plan', 'free')->count(),
            'paid' => SchoolSubscription::whereIn('plan', ['basic', 'pro', 'enterprise'])->count(),
        ];

        $plans = [
            'free' => ['name' => 'مجاني (تجريبي)', 'color' => 'gray'],
            'basic' => ['name' => 'الأساسي', 'color' => 'blue'],
            'pro' => ['name' => 'المتقدم', 'color' => 'purple'],
            'enterprise' => ['name' => 'المؤسسي', 'color' => 'gold'],
        ];

        return view('superadmin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * تجديد اشتراك
     */
    public function renew(Request $request, SchoolSubscription $subscription)
    {
        $validated = $request->validate([
            'plan' => 'required|in:basic,pro,enterprise',
            'months' => 'required|integer|min:1|max:24',
        ]);

        $pricePerMonth = match($validated['plan']) {
            'basic' => 99,
            'pro' => 199,
            'enterprise' => 499,
        };

        $subscription->update([
            'plan' => $validated['plan'],
            'starts_at' => $subscription->ends_at > now() ? $subscription->ends_at : now(),
            'ends_at' => ($subscription->ends_at > now() ? $subscription->ends_at : now())->addMonths($validated['months']),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'تم تجديد الاشتراك بنجاح');
    }

    /**
     * إلغاء اشتراك
     */
    public function cancel(SchoolSubscription $subscription)
    {
        $subscription->update(['is_active' => false]);
        
        return redirect()->back()->with('success', 'تم إلغاء الاشتراك');
    }
}
