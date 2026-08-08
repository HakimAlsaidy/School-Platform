@extends('layouts.dashboard')

@section('page-title', 'الرسوم الدراسية')
@section('page-description', 'متابعة الرسوم والمدفوعات')

@section('dashboard-content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">إجمالي الرسوم المستحقة</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($totalDue, 2) }} ر.س</p>
        </div>
        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center">
            <i class="fas fa-coins text-red-500 text-2xl"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الرسم</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الطالب</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">المبلغ</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">المدفوع</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">المتبقي</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">إجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($studentFees as $studentFee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $studentFee->fee->title }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $studentFee->student->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ number_format($studentFee->amount_due, 2) }} ر.س</td>
                        <td class="px-6 py-4 text-green-600">{{ number_format($studentFee->amount_paid, 2) }} ر.س</td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ number_format($studentFee->remaining_amount, 2) }} ر.س</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($studentFee->status == 'paid') bg-green-100 text-green-700
                                @elseif($studentFee->status == 'partial') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $studentFee->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($studentFee->remaining_amount > 0)
                                <button onclick="openPayModal({{ $studentFee->id }}, '{{ addslashes($studentFee->fee->title) }}', {{ $studentFee->remaining_amount }})"
                                    class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    دفع
                                </button>
                            @else
                                <span class="text-xs text-gray-400">مدفوع</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">لا توجد رسوم مستحقة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal دفع -->
<div id="payModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closePayModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4">دفع رسوم</h3>
            <form id="payForm" action="" method="POST" class="space-y-4">
                @csrf
                <p id="payFeeTitle" class="text-sm text-gray-600"></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" id="payAmount" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                    <select name="method" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="card">بطاقة ائتمانية</option>
                        <option value="online">تحويل إلكتروني</option>
                        <option value="wallet">محفظة إلكترونية</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">إتمام الدفع</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openPayModal(id, title, remaining) {
    document.getElementById('payForm').action = '/parent/finance/fees/' + id + '/pay';
    document.getElementById('payFeeTitle').textContent = 'الرسم: ' + title;
    document.getElementById('payAmount').max = remaining;
    document.getElementById('payModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePayModal(); });
</script>
@endpush
@endsection
