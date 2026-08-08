@extends('layouts.dashboard')

@section('page-title', 'سجل المدفوعات')
@section('page-description', 'سجل جميع المدفوعات السابقة')

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
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-sm text-indigo-600">{{ $payment->payment_ref }}</td>
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $payment->student->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->studentFee->fee->title ?? '-' }}</td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ number_format($payment->amount, 2) }} ر.س</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->method_label }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->payment_date->format('Y/m/d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">لا توجد مدفوعات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $payments->links() }}</div>
</div>
@endsection
