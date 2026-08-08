@extends('layouts.dashboard')

@section('page-title', 'المدفوعات')
@section('page-description', 'سجل المدفوعات المدرسية')

@section('dashboard-content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">رقم العملية</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الطالب</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الرسم</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">المبلغ</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الطريقة</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600">{{ $payment->payment_ref }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $payment->student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->student->classroom->grade->name ?? '' }} - {{ $payment->student->classroom->name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->studentFee->fee->title ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ number_format($payment->amount, 2) }} ر.س</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->method_label }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($payment->status == 'completed') bg-green-100 text-green-700
                                @elseif($payment->status == 'pending') bg-amber-100 text-amber-700
                                @elseif($payment->status == 'refunded') bg-gray-100 text-gray-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $payment->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->payment_date->format('Y/m/d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-credit-card text-5xl text-gray-300 mb-4 block"></i>
                            لا توجد مدفوعات مسجلة
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
</div>
@endsection
