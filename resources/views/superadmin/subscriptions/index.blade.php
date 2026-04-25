@extends('layouts.superadmin')

@section('title', 'إدارة الاشتراكات')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">💳 إدارة الاشتراكات</h1>
    <p class="text-gray-600 mt-2">عرض وإدارة اشتراكات المدارس</p>
</div>

<!-- الإحصائيات -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">إجمالي الاشتراكات</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-list text-xl text-gray-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">النشطة</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-check-circle text-xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">التجريبية</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['trial'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-gift text-xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">المدفوعة</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['paid'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-crown text-xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">المنتهية</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['expired'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-times-circle text-xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- الفلاتر -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
    <form action="{{ route('superadmin.subscriptions.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <select name="plan" class="px-4 py-2 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
            <option value="">جميع الخطط</option>
            <option value="free" {{ request('plan') == 'free' ? 'selected' : '' }}>مجاني</option>
            <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>الأساسي</option>
            <option value="pro" {{ request('plan') == 'pro' ? 'selected' : '' }}>المتقدم</option>
            <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>المؤسسي</option>
        </select>

        <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none">
            <option value="">جميع الحالات</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
            <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>تجريبي</option>
        </select>

        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-filter ml-1"></i>
            تصفية
        </button>
    </form>
</div>

<!-- جدول الاشتراكات -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">المدرسة</th>
                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">الخطة</th>
                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">تاريخ البدء</th>
                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">تاريخ الانتهاء</th>
                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">الحالة</th>
                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $subscription)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $subscription->school->logo_url ?? 'https://ui-avatars.com/api/?name='.$subscription->school->name }}" 
                                 alt="{{ $subscription->school->name }}" 
                                 class="w-10 h-10 rounded-xl object-cover">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $subscription->school->name }}</p>
                                <p class="text-sm text-gray-500">{{ $subscription->school->subdomain }}.schoolpla.com</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $planColors = [
                                'free' => 'gray',
                                'basic' => 'blue',
                                'pro' => 'purple',
                                'enterprise' => 'yellow'
                            ];
                            $color = $planColors[$subscription->plan] ?? 'gray';
                        @endphp
                        <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-700 rounded-full text-sm font-medium">
                            {{ $plans[$subscription->plan]['name'] ?? $subscription->plan }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $subscription->starts_at?->format('Y/m/d') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $subscription->ends_at?->format('Y/m/d') ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($subscription->is_active && $subscription->ends_at > now())
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">نشط</span>
                        @elseif($subscription->ends_at < now())
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">منتهي</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">معلق</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <button onclick="openRenewModal({{ $subscription->id }})" 
                                    class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition" title="تجديد">
                                <i class="fas fa-sync"></i>
                            </button>
                            <form action="{{ route('superadmin.subscriptions.cancel', $subscription) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الاشتراك؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="إلغاء">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>لا توجد اشتراكات</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $subscriptions->links() }}
</div>

<!-- Modal تجديد الاشتراك -->
<div id="renew-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">تجديد الاشتراك</h3>
        <form id="renew-form" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخطة</label>
                    <select name="plan" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none" required>
                        <option value="basic">الأساسي - 99 ريال/شهر</option>
                        <option value="pro">المتقدم - 199 ريال/شهر</option>
                        <option value="enterprise">المؤسسي - 499 ريال/شهر</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدة (بالأشهر)</label>
                    <input type="number" name="months" min="1" max="24" value="12" 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-indigo-500 outline-none" required>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeRenewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    إلغاء
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition">
                    تجديد
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRenewModal(subscriptionId) {
    document.getElementById('renew-form').action = `/superadmin/subscriptions/${subscriptionId}/renew`;
    document.getElementById('renew-modal').classList.remove('hidden');
    document.getElementById('renew-modal').classList.add('flex');
}

function closeRenewModal() {
    document.getElementById('renew-modal').classList.add('hidden');
    document.getElementById('renew-modal').classList.remove('flex');
}
</script>
@endpush
@endsection
