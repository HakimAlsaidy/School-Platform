@extends('layouts.superadmin')

@section('title', 'نظرة عامة على الميزات')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">📊 نظرة عامة على الميزات</h1>
    <p class="text-gray-600 mt-2">استخدام الوحدات الوظيفية (المالية، المكتبة، النقل، الاختبارات، المواد) عبر جميع المدارس</p>
</div>

<!-- الإحصائيات الإجمالية -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">إجمالي الرسوم</p>
        <p class="text-2xl font-bold text-indigo-600">{{ $totals['fees'] }}</p>
        <p class="text-xs text-gray-400">{{ $featureAdoption['fees']['schools'] }} مدرسة</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">المدفوعات</p>
        <p class="text-2xl font-bold text-green-600">{{ $totals['payments'] }}</p>
        <p class="text-xs text-gray-400">{{ number_format($totals['total_payments']) }} ريال</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">المصروفات</p>
        <p class="text-2xl font-bold text-red-600">{{ $totals['expenses'] }}</p>
        <p class="text-xs text-gray-400">{{ number_format($totals['total_expense']) }} ريال</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">الإيرادات</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $totals['incomes'] }}</p>
        <p class="text-xs text-gray-400">{{ number_format($totals['total_income']) }} ريال</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">الكتب / الإعارات</p>
        <p class="text-2xl font-bold text-blue-600">{{ $totals['books'] }} / {{ $totals['book_loans'] }}</p>
        <p class="text-xs text-gray-400">{{ $featureAdoption['library']['schools'] }} مدرسة</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">الحافلات / المسارات</p>
        <p class="text-2xl font-bold text-purple-600">{{ $totals['buses'] }} / {{ $totals['transport_routes'] }}</p>
        <p class="text-xs text-gray-400">{{ $featureAdoption['transport']['schools'] }} مدرسة</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">بنك الأسئلة</p>
        <p class="text-2xl font-bold text-amber-600">{{ $totals['question_bank'] }}</p>
        <p class="text-xs text-gray-400">سؤال</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">الاختبارات</p>
        <p class="text-2xl font-bold text-rose-600">{{ $totals['online_quizzes'] }}</p>
        <p class="text-xs text-gray-400">{{ $totals['quiz_attempts'] }} محاولة</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500">المواد الدراسية</p>
        <p class="text-2xl font-bold text-cyan-600">{{ $totals['materials'] }}</p>
        <p class="text-xs text-gray-400">{{ $featureAdoption['materials']['schools'] }} مدرسة</p>
    </div>
</div>

<!-- استخدام الميزات -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-chart-pie text-indigo-500 ml-2"></i>
                اعتماد الميزات بين المدارس
            </h2>
        </div>
        <div class="p-6 space-y-5">
            @php
                $features = [
                    'fees' => ['الرسوم الدراسية', 'fa-money-bill-wave', 'indigo'],
                    'library' => ['المكتبة', 'fa-book-reader', 'blue'],
                    'transport' => ['النقل المدرسي', 'fa-bus', 'purple'],
                    'quizzes' => ['الاختبارات', 'fa-file-alt', 'rose'],
                    'materials' => ['المواد الدراسية', 'fa-folder-open', 'cyan'],
                    'accounting' => ['المصروفات والإيرادات', 'fa-chart-line', 'green'],
                ];
            @endphp
            @foreach($features as $key => [$label, $icon, $color])
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-sm font-medium text-gray-700">
                            <i class="fas {{ $icon }} text-{{ $color }}-500 ml-1"></i>
                            {{ $label }}
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ $featureAdoption[$key]['schools'] }} مدرسة ({{ $featureAdoption[$key]['percentage'] }}%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="bg-{{ $color }}-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $featureAdoption[$key]['percentage'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ملخص مالي -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fas fa-wallet text-green-500 ml-2"></i>
                ملخص الاستخدام المالي
            </h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                <span class="text-green-700 font-medium">إجمالي المدفوعات</span>
                <span class="text-green-700 font-bold">{{ number_format($totals['total_payments']) }} ريال</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                <span class="text-emerald-700 font-medium">إجمالي الإيرادات</span>
                <span class="text-emerald-700 font-bold">{{ number_format($totals['total_income']) }} ريال</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                <span class="text-red-700 font-medium">إجمالي المصروفات</span>
                <span class="text-red-700 font-bold">{{ number_format($totals['total_expense']) }} ريال</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-xl">
                <span class="text-indigo-700 font-medium">صافي الدخل</span>
                <span class="text-indigo-700 font-bold">{{ number_format($totals['net']) }} ريال</span>
            </div>
        </div>
    </div>
</div>

<!-- استخدام الميزات لكل مدرسة -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">
            <i class="fas fa-school text-indigo-500 ml-2"></i>
            تفاصيل استخدام الميزات لكل مدرسة
        </h2>
    </div>
    <div class="p-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-right text-gray-500 border-b border-gray-100">
                    <th class="py-3 pr-4">المدرسة</th>
                    <th class="py-3">الرسوم</th>
                    <th class="py-3">المدفوعات</th>
                    <th class="py-3">المصروفات</th>
                    <th class="py-3">الإيرادات</th>
                    <th class="py-3">الكتب</th>
                    <th class="py-3">الإعارات</th>
                    <th class="py-3">الحافلات</th>
                    <th class="py-3">المسارات</th>
                    <th class="py-3">البنك</th>
                    <th class="py-3">الاختبارات</th>
                    <th class="py-3">المواد</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schoolStats as $s)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-3 pr-4">
                            <span class="font-semibold text-gray-800">{{ $s['name'] }}</span>
                            <span class="text-xs text-gray-400 block">{{ $s['subdomain'] }}</span>
                        </td>
                        <td class="py-3">{{ $s['fees'] }}</td>
                        <td class="py-3">{{ $s['payments'] }}</td>
                        <td class="py-3">{{ $s['expenses'] }}</td>
                        <td class="py-3">{{ $s['incomes'] }}</td>
                        <td class="py-3">{{ $s['books'] }}</td>
                        <td class="py-3">{{ $s['book_loans'] }}</td>
                        <td class="py-3">{{ $s['buses'] }}</td>
                        <td class="py-3">{{ $s['transport_routes'] }}</td>
                        <td class="py-3">{{ $s['question_bank'] }}</td>
                        <td class="py-3">{{ $s['quizzes'] }} <span class="text-xs text-gray-400">({{ $s['quiz_attempts'] }})</span></td>
                        <td class="py-3">{{ $s['materials'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="py-8 text-center text-gray-400">
                            <i class="fas fa-school text-4xl mb-2 block"></i>
                            لا توجد مدارس مسجلة
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

