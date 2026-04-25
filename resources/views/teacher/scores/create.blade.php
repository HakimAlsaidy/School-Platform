@extends('layouts.dashboard')

@section('page-title', 'إضافة درجات الشهر')
@section('page-description', 'إضافة درجات الطلاب للشهر')

@section('dashboard-content')
@php
    $monthNames = [1 => 'الشهر الأول', 2 => 'الشهر الثاني', 3 => 'الشهر الثالث'];
    $termName = ($term ?? 1) == 1 ? 'الترم الأول' : 'الترم الثاني';
@endphp

<div class="mb-4">
    @if($classroom && $subject && isset($term))
        <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroom->id, 'subject_id' => $subject->id, 'term' => $term]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للشهور</span>
        </a>
    @else
        <a href="{{ route('teacher.scores.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
            <span>العودة</span>
        </a>
    @endif
</div>

@if(!$classroom || !$subject)
    <!-- اختيار الفصل والمادة إذا لم تكن محددة -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-clipboard-list ml-2 text-indigo-600"></i>
            اختر الفصل والمادة والترم والشهر
        </h3>
        
        <form action="{{ route('teacher.scores.create') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفصل</label>
                <select name="classroom_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر الفصل...</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ request('classroom_id') == $cls->id ? 'selected' : '' }}>
                            {{ $cls->grade->name ?? '' }} - {{ $cls->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المادة</label>
                <select name="subject_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر المادة...</option>
                    @foreach($subjects as $subj)
                        <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                            {{ $subj->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الترم</label>
                <select name="term" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="1" {{ request('term') == 1 ? 'selected' : '' }}>الترم الأول</option>
                    <option value="2" {{ request('term') == 2 ? 'selected' : '' }}>الترم الثاني</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الشهر</label>
                <select name="month" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="1" {{ request('month') == 1 ? 'selected' : '' }}>الشهر الأول</option>
                    <option value="2" {{ request('month') == 2 ? 'selected' : '' }}>الشهر الثاني</option>
                    <option value="3" {{ request('month') == 3 ? 'selected' : '' }}>الشهر الثالث</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                    <i class="fas fa-arrow-left ml-2"></i>التالي
                </button>
            </div>
        </form>
    </div>
@else
    <!-- Header -->
    <div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 text-white mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-day text-3xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold">{{ $monthNames[$month] ?? 'الشهر' }}</h2>
                <p class="text-violet-100">{{ $subject->name }} • {{ $termName }} • {{ $classroom->name }}</p>
            </div>
            <span class="px-4 py-2 bg-white/20 rounded-xl">
                {{ $students->count() }} طالب
            </span>
        </div>
    </div>
    
    @if($students->count() > 0)
        <form action="{{ route('teacher.scores.store') }}" method="POST">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <input type="hidden" name="term" value="{{ $term }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="semester" value="الفصل الدراسي">
            
            <!-- جدول إدخال الدرجات -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span class="text-sm text-gray-600">
                            أدخل الدرجات لكل طالب: الحضور (20) + الواجبات (20) + المواظبة (20) + التحريري (40) = المجموع (100)
                        </span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-4 text-right text-sm font-semibold text-gray-600">#</th>
                                <th class="px-4 py-4 text-right text-sm font-semibold text-gray-600 min-w-[200px]">اسم الطالب</th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">
                                    الحضور<br><span class="text-xs text-violet-400 font-normal">(من 20)</span>
                                </th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">
                                    الواجبات<br><span class="text-xs text-amber-400 font-normal">(من 20)</span>
                                </th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">
                                    المواظبة<br><span class="text-xs text-rose-400 font-normal">(من 20)</span>
                                </th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">
                                    التحريري<br><span class="text-xs text-blue-400 font-normal">(من 40)</span>
                                </th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-emerald-50">
                                    المجموع<br><span class="text-xs text-emerald-400 font-normal">(من 100)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($students as $index => $student)
                                @php
                                    $existingScore = $existingScores[$student->id] ?? null;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <input type="hidden" name="scores[{{ $index }}][student_id]" value="{{ $student->id }}">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff" 
                                                 alt="" class="w-10 h-10 rounded-full">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $student->student_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <input type="number" 
                                               name="scores[{{ $index }}][attendance]" 
                                               min="0" max="20" step="0.5"
                                               value="{{ $existingScore?->attendance ?? '' }}"
                                               class="w-20 px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 text-center font-medium"
                                               placeholder="0"
                                               oninput="calcMonthTotal({{ $index }})">
                                    </td>
                                    <td class="px-3 py-4">
                                        <input type="number" 
                                               name="scores[{{ $index }}][homework]" 
                                               min="0" max="20" step="0.5"
                                               value="{{ $existingScore?->homework ?? '' }}"
                                               class="w-20 px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 text-center font-medium"
                                               placeholder="0"
                                               oninput="calcMonthTotal({{ $index }})">
                                    </td>
                                    <td class="px-3 py-4">
                                        <input type="number" 
                                               name="scores[{{ $index }}][discipline]" 
                                               min="0" max="20" step="0.5"
                                               value="{{ $existingScore?->discipline ?? '' }}"
                                               class="w-20 px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 text-center font-medium"
                                               placeholder="0"
                                               oninput="calcMonthTotal({{ $index }})">
                                    </td>
                                    <td class="px-3 py-4">
                                        <input type="number" 
                                               name="scores[{{ $index }}][written]" 
                                               min="0" max="40" step="0.5"
                                               value="{{ $existingScore?->written ?? '' }}"
                                               class="w-20 px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-center font-medium"
                                               placeholder="0"
                                               oninput="calcMonthTotal({{ $index }})">
                                    </td>
                                    <td class="px-3 py-4 bg-emerald-50">
                                        <input type="text" 
                                               id="month_total_{{ $index }}"
                                               readonly
                                               value="{{ $existingScore?->month_total ?? '0' }}"
                                               class="w-20 px-3 py-2 border-0 bg-transparent text-center font-bold text-emerald-600 text-lg">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-lightbulb text-amber-500 ml-1"></i>
                            سيتم حساب المجموع تلقائياً عند إدخال الدرجات
                        </p>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-violet-500 to-purple-600 text-white rounded-xl hover:from-violet-600 hover:to-purple-700 transition flex items-center gap-2 shadow-lg">
                            <i class="fas fa-save"></i>
                            حفظ درجات {{ $monthNames[$month] ?? 'الشهر' }}
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
@endif

<script>
function calcMonthTotal(idx) {
    const attendance = parseFloat(document.querySelector(`[name='scores[${idx}][attendance]']`)?.value) || 0;
    const homework = parseFloat(document.querySelector(`[name='scores[${idx}][homework]']`)?.value) || 0;
    const discipline = parseFloat(document.querySelector(`[name='scores[${idx}][discipline]']`)?.value) || 0;
    const written = parseFloat(document.querySelector(`[name='scores[${idx}][written]']`)?.value) || 0;
    
    // المجموع = الحضور + الواجبات + المواظبة + التحريري
    const total = attendance + homework + discipline + written;
    
    document.getElementById(`month_total_${idx}`).value = total.toFixed(1);
}

// حساب المجموع لكل الطلاب عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    const studentCount = {{ $students->count() ?? 0 }};
    for (let i = 0; i < studentCount; i++) {
        calcMonthTotal(i);
    }
});
</script>
@endsection
