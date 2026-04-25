@extends('layouts.dashboard')

@section('page-title', 'المحصلة والنهائي')
@section('page-description', 'عرض المحصلة وإدخال درجة النهائي')

@section('dashboard-content')
@php
    $termName = $term == 1 ? 'الترم الأول' : 'الترم الثاني';
@endphp

<div class="mb-4">
    <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroom->id, 'subject_id' => $subject->id, 'term' => $term]) }}" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
        <i class="fas fa-arrow-right"></i>
        <span>العودة للشهور</span>
    </a>
</div>

<!-- Header -->
<div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-trophy text-3xl"></i>
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold">المحصلة والنهائي</h2>
            <p class="text-emerald-100">{{ $subject->name }} • {{ $termName }} • {{ $classroom->name }}</p>
        </div>
        <span class="px-4 py-2 bg-white/20 rounded-xl">
            {{ count($studentsData) }} طالب
        </span>
    </div>
</div>

<!-- توضيح النظام -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-calculator text-emerald-500 ml-2"></i>
        نظام حساب الدرجات
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-violet-50 rounded-xl border border-violet-200">
            <p class="font-bold text-violet-700 mb-1">مجموع الشهور</p>
            <p class="text-sm text-violet-600">شهر1 + شهر2 + شهر3 (كل شهر من 100)</p>
        </div>
        <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
            <p class="font-bold text-amber-700 mb-1">المحصلة (من 20)</p>
            <p class="text-sm text-amber-600">(مجموع الشهور) ÷ 15</p>
        </div>
        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
            <p class="font-bold text-emerald-700 mb-1">المجموع النهائي (من 50)</p>
            <p class="text-sm text-emerald-600">المحصلة + النهائي (30)</p>
        </div>
    </div>
</div>

@if(count($studentsData) > 0)
    <form action="{{ route('teacher.scores.storeFinal') }}" method="POST">
        @csrf
        <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <input type="hidden" name="term" value="{{ $term }}">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-4 text-right text-sm font-semibold text-gray-600">#</th>
                            <th class="px-4 py-4 text-right text-sm font-semibold text-gray-600 min-w-[180px]">اسم الطالب</th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-violet-50">
                                الشهر 1<br><span class="text-xs text-violet-400 font-normal">(100)</span>
                            </th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-amber-50">
                                الشهر 2<br><span class="text-xs text-amber-400 font-normal">(100)</span>
                            </th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-rose-50">
                                الشهر 3<br><span class="text-xs text-rose-400 font-normal">(100)</span>
                            </th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-indigo-50">
                                المحصلة<br><span class="text-xs text-indigo-400 font-normal">(من 20)</span>
                            </th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">
                                النهائي<br><span class="text-xs text-gray-400 font-normal">(من 30)</span>
                            </th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-emerald-50">
                                المجموع<br><span class="text-xs text-emerald-400 font-normal">(من 50)</span>
                            </th>
                            <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">التقدير</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($studentsData as $index => $data)
                            @php
                                $total50 = $data['total'];
                                $gradeLabel = match(true) {
                                    $total50 >= 45 => 'ممتاز',
                                    $total50 >= 40 => 'جيد جداً',
                                    $total50 >= 35 => 'جيد',
                                    $total50 >= 30 => 'مقبول',
                                    default => 'ضعيف',
                                };
                                $gradeColor = match(true) {
                                    $total50 >= 45 => 'bg-emerald-100 text-emerald-700',
                                    $total50 >= 40 => 'bg-blue-100 text-blue-700',
                                    $total50 >= 35 => 'bg-amber-100 text-amber-700',
                                    $total50 >= 30 => 'bg-orange-100 text-orange-700',
                                    default => 'bg-red-100 text-red-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $data['student']->id }}">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($data['student']->name) }}&background=6366f1&color=fff" 
                                             alt="" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $data['student']->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $data['student']->student_id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-center bg-violet-50">
                                    <span class="font-medium text-violet-700">{{ $data['month1'] }}</span>
                                </td>
                                <td class="px-3 py-4 text-center bg-amber-50">
                                    <span class="font-medium text-amber-700">{{ $data['month2'] }}</span>
                                </td>
                                <td class="px-3 py-4 text-center bg-rose-50">
                                    <span class="font-medium text-rose-700">{{ $data['month3'] }}</span>
                                </td>
                                <td class="px-3 py-4 text-center bg-indigo-50">
                                    <span class="font-bold text-indigo-700 text-lg">{{ $data['result'] }}</span>
                                </td>
                                <td class="px-3 py-4">
                                    <input type="number" 
                                           name="scores[{{ $index }}][final_30]" 
                                           min="0" max="30" step="0.5"
                                           value="{{ $data['final'] > 0 ? $data['final'] : '' }}"
                                           class="w-20 px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 text-center font-medium mx-auto block"
                                           placeholder="0"
                                           oninput="calcTotal({{ $index }}, {{ $data['result'] }})">
                                </td>
                                <td class="px-3 py-4 text-center bg-emerald-50">
                                    <span class="font-bold text-emerald-600 text-lg" id="total_{{ $index }}">{{ $data['total'] }}</span>
                                </td>
                                <td class="px-3 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $gradeColor }}" id="grade_{{ $index }}">
                                        {{ $gradeLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 border-t border-gray-100 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6 text-sm">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            ممتاز (45-50)
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            جيد جداً (40-44)
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            جيد (35-39)
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                            مقبول (30-34)
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            ضعيف (<30)
                        </span>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition flex items-center gap-2 shadow-lg">
                        <i class="fas fa-save"></i>
                        حفظ درجات النهائي
                    </button>
                </div>
            </div>
        </div>
    </form>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-600 mb-2">لا يوجد طلاب</h3>
        <p class="text-gray-500">لا يوجد طلاب في هذا الفصل</p>
    </div>
@endif

<script>
function calcTotal(idx, result) {
    const final30 = parseFloat(document.querySelector(`[name='scores[${idx}][final_30]']`)?.value) || 0;
    const total = (result + final30).toFixed(1);
    
    document.getElementById(`total_${idx}`).textContent = total;
    
    // تحديث التقدير
    const gradeEl = document.getElementById(`grade_${idx}`);
    let gradeLabel, gradeColor;
    
    if (total >= 45) {
        gradeLabel = 'ممتاز';
        gradeColor = 'bg-emerald-100 text-emerald-700';
    } else if (total >= 40) {
        gradeLabel = 'جيد جداً';
        gradeColor = 'bg-blue-100 text-blue-700';
    } else if (total >= 35) {
        gradeLabel = 'جيد';
        gradeColor = 'bg-amber-100 text-amber-700';
    } else if (total >= 30) {
        gradeLabel = 'مقبول';
        gradeColor = 'bg-orange-100 text-orange-700';
    } else {
        gradeLabel = 'ضعيف';
        gradeColor = 'bg-red-100 text-red-700';
    }
    
    gradeEl.textContent = gradeLabel;
    gradeEl.className = `px-3 py-1 rounded-full text-xs font-medium ${gradeColor}`;
}
</script>
@endsection
