@extends('layouts.dashboard')

@section('page-title', 'إدارة الدرجات')
@section('page-description', 'إضافة وإدارة درجات الطلاب')

@section('dashboard-content')
@php
    $classroomId = request('classroom_id');
    $subjectId = request('subject_id');
    $termId = request('term');
    $monthId = request('month');
@endphp

{{-- المستوى 1: الفصول --}}
@if(!$classroomId)
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">📊 إدارة الدرجات</h2>
                <p class="text-gray-500 mt-1">اختر الفصل لإدارة درجات الطلاب</p>
            </div>
        </div>
        
        @php
            $classroomsByGrade = $classrooms->groupBy(fn($c) => $c->grade->name ?? 'غير محدد');
        @endphp
        
        @forelse($classroomsByGrade as $gradeName => $gradeClassrooms)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white text-sm">
                        {{ $loop->iteration }}
                    </span>
                    {{ $gradeName }}
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($gradeClassrooms as $classroom)
                        @php
                            $colors = [
                                'from-emerald-500 to-emerald-600',
                                'from-blue-500 to-blue-600',
                                'from-violet-500 to-violet-600',
                                'from-rose-500 to-rose-600',
                                'from-amber-500 to-amber-600',
                                'from-cyan-500 to-cyan-600',
                            ];
                            $colorClass = $colors[$loop->index % count($colors)];
                            $studentCount = $classroom->students->count() ?? 0;
                        @endphp
                        
                        <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroom->id]) }}"
                           class="classroom-card group relative bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-emerald-200 transition-all duration-300 hover:-translate-y-1">
                            <div class="h-2 bg-gradient-to-r {{ $colorClass }}"></div>
                            <div class="p-5">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br {{ $colorClass }} rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-school text-white text-xl"></i>
                                    </div>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                        {{ $studentCount }} طالب
                                    </span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-800 mb-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $classroom->name }}
                                </h4>
                                <p class="text-sm text-gray-500">{{ $gradeName }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-school text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد فصول</h3>
                <p class="text-gray-500">لم يتم تعيين أي فصول دراسية لك بعد</p>
            </div>
        @endforelse
    </div>

{{-- المستوى 2: المواد --}}
@elseif(!$subjectId)
    <div class="mb-4">
        <a href="{{ route('teacher.scores.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للفصول</span>
        </a>
    </div>
    
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-school text-3xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $selectedClassroom->name ?? 'الفصل' }}</h2>
                <p class="text-emerald-100">{{ $selectedClassroom->grade->name ?? '' }} • {{ $selectedClassroom->students->count() ?? 0 }} طالب</p>
            </div>
        </div>
    </div>
    
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-book-open text-emerald-500 ml-2"></i>
        اختر المادة
    </h3>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $subjectColors = [
                ['bg' => 'from-blue-500 to-blue-600', 'light' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
                ['bg' => 'from-emerald-500 to-emerald-600', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200'],
                ['bg' => 'from-violet-500 to-violet-600', 'light' => 'bg-violet-50', 'text' => 'text-violet-600', 'border' => 'border-violet-200'],
                ['bg' => 'from-amber-500 to-amber-600', 'light' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200'],
                ['bg' => 'from-rose-500 to-rose-600', 'light' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200'],
                ['bg' => 'from-cyan-500 to-cyan-600', 'light' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'border' => 'border-cyan-200'],
            ];
            $subjectIcons = ['fas fa-calculator', 'fas fa-flask', 'fas fa-book', 'fas fa-globe', 'fas fa-paint-brush', 'fas fa-language'];
        @endphp
        
        @foreach($subjects as $index => $subject)
            @php
                $colors = $subjectColors[$index % count($subjectColors)];
                $iconClass = $subjectIcons[$index % count($subjectIcons)];
            @endphp
            
            <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroomId, 'subject_id' => $subject->id]) }}"
               class="group bg-white rounded-2xl shadow-md border-2 {{ $colors['border'] }} overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
                <div class="h-3 bg-gradient-to-r {{ $colors['bg'] }}"></div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br {{ $colors['bg'] }} rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="{{ $iconClass }} text-white text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-gray-800 group-hover:{{ $colors['text'] }} transition-colors">
                                {{ $subject->name }}
                            </h4>
                            <p class="text-sm text-gray-500">اختر الترم</p>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

{{-- المستوى 3: الترمين --}}
@elseif(!$termId)
    <div class="mb-4 flex gap-2">
        <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroomId]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للمواد</span>
        </a>
    </div>
    
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-book text-3xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $selectedSubject->name ?? 'المادة' }}</h2>
                <p class="text-indigo-100">{{ $selectedClassroom->name ?? '' }} • {{ $selectedClassroom->grade->name ?? '' }}</p>
            </div>
        </div>
    </div>
    
    <h3 class="text-lg font-bold text-gray-800 mb-6">
        <i class="fas fa-calendar-alt text-indigo-500 ml-2"></i>
        اختر الترم الدراسي
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        {{-- الترم الأول --}}
        <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => 1]) }}"
           class="group bg-white rounded-3xl shadow-lg border-2 border-blue-200 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-3 hover:border-blue-400">
            <div class="h-4 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            <div class="p-8 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-xl mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <span class="text-4xl font-bold text-white">1</span>
                </div>
                <h4 class="text-2xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                    📘 الترم الأول
                </h4>
                <p class="text-gray-500">إدارة درجات الترم الأول</p>
                <div class="mt-6 py-3 bg-blue-50 rounded-xl group-hover:bg-blue-600 transition-all duration-300">
                    <span class="text-blue-600 font-bold group-hover:text-white transition-colors">
                        <i class="fas fa-arrow-left ml-2"></i>
                        دخول
                    </span>
                </div>
            </div>
        </a>
        
        {{-- الترم الثاني --}}
        <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => 2]) }}"
           class="group bg-white rounded-3xl shadow-lg border-2 border-emerald-200 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-3 hover:border-emerald-400">
            <div class="h-4 bg-gradient-to-r from-emerald-500 to-emerald-600"></div>
            <div class="p-8 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl flex items-center justify-center shadow-xl mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <span class="text-4xl font-bold text-white">2</span>
                </div>
                <h4 class="text-2xl font-bold text-gray-800 mb-2 group-hover:text-emerald-600 transition-colors">
                    📗 الترم الثاني
                </h4>
                <p class="text-gray-500">إدارة درجات الترم الثاني</p>
                <div class="mt-6 py-3 bg-emerald-50 rounded-xl group-hover:bg-emerald-600 transition-all duration-300">
                    <span class="text-emerald-600 font-bold group-hover:text-white transition-colors">
                        <i class="fas fa-arrow-left ml-2"></i>
                        دخول
                    </span>
                </div>
            </div>
        </a>
    </div>

{{-- المستوى 4: الشهور والمحصلة --}}
@else
    <div class="mb-4 flex gap-2">
        <a href="{{ route('teacher.scores.index', ['classroom_id' => $classroomId, 'subject_id' => $subjectId]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للترمين</span>
        </a>
    </div>
    
    @php
        $termColor = $termId == 1 ? 'from-blue-500 to-blue-600' : 'from-emerald-500 to-emerald-600';
        $termName = $termId == 1 ? 'الترم الأول' : 'الترم الثاني';
    @endphp
    
    <div class="bg-gradient-to-r {{ $termColor }} rounded-2xl p-6 text-white mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                <span class="text-3xl font-bold">{{ $termId }}</span>
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $termName }}</h2>
                <p class="text-white/80">{{ $selectedSubject->name ?? 'المادة' }} • {{ $selectedClassroom->name ?? '' }}</p>
            </div>
        </div>
    </div>
    
    @if(!$monthId)
        {{-- عرض قوالب الشهور --}}
        <h3 class="text-lg font-bold text-gray-800 mb-6">
            <i class="fas fa-calendar-check text-gray-500 ml-2"></i>
            اختر الشهر أو المحصلة والنهائي
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- الشهر الأول --}}
            <a href="{{ route('teacher.scores.create', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => $termId, 'month' => 1]) }}"
               class="group bg-white rounded-2xl shadow-md border-2 border-violet-200 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-violet-400">
                <div class="h-3 bg-gradient-to-r from-violet-500 to-violet-600"></div>
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-day text-white text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 group-hover:text-violet-600 transition-colors">الشهر الأول</h4>
                    <p class="text-sm text-gray-500 mt-1">إدخال درجات الشهر الأول</p>
                    @php
                        $month1Count = \App\Models\Score::where('subject_id', $subjectId)
                            ->where('term', $termId)->where('month', 1)
                            ->whereHas('student', fn($q) => $q->where('classroom_id', $classroomId))->count();
                    @endphp
                    @if($month1Count > 0)
                        <span class="inline-block mt-3 px-3 py-1 bg-violet-100 text-violet-600 rounded-full text-xs font-medium">
                            {{ $month1Count }} درجة مسجلة
                        </span>
                    @endif
                </div>
            </a>
            
            {{-- الشهر الثاني --}}
            <a href="{{ route('teacher.scores.create', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => $termId, 'month' => 2]) }}"
               class="group bg-white rounded-2xl shadow-md border-2 border-amber-200 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-amber-400">
                <div class="h-3 bg-gradient-to-r from-amber-500 to-amber-600"></div>
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-day text-white text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 group-hover:text-amber-600 transition-colors">الشهر الثاني</h4>
                    <p class="text-sm text-gray-500 mt-1">إدخال درجات الشهر الثاني</p>
                    @php
                        $month2Count = \App\Models\Score::where('subject_id', $subjectId)
                            ->where('term', $termId)->where('month', 2)
                            ->whereHas('student', fn($q) => $q->where('classroom_id', $classroomId))->count();
                    @endphp
                    @if($month2Count > 0)
                        <span class="inline-block mt-3 px-3 py-1 bg-amber-100 text-amber-600 rounded-full text-xs font-medium">
                            {{ $month2Count }} درجة مسجلة
                        </span>
                    @endif
                </div>
            </a>
            
            {{-- الشهر الثالث --}}
            <a href="{{ route('teacher.scores.create', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => $termId, 'month' => 3]) }}"
               class="group bg-white rounded-2xl shadow-md border-2 border-rose-200 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-rose-400">
                <div class="h-3 bg-gradient-to-r from-rose-500 to-rose-600"></div>
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-day text-white text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 group-hover:text-rose-600 transition-colors">الشهر الثالث</h4>
                    <p class="text-sm text-gray-500 mt-1">إدخال درجات الشهر الثالث</p>
                    @php
                        $month3Count = \App\Models\Score::where('subject_id', $subjectId)
                            ->where('term', $termId)->where('month', 3)
                            ->whereHas('student', fn($q) => $q->where('classroom_id', $classroomId))->count();
                    @endphp
                    @if($month3Count > 0)
                        <span class="inline-block mt-3 px-3 py-1 bg-rose-100 text-rose-600 rounded-full text-xs font-medium">
                            {{ $month3Count }} درجة مسجلة
                        </span>
                    @endif
                </div>
            </a>
            
            {{-- المحصلة والنهائي --}}
            <a href="{{ route('teacher.scores.final', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => $termId]) }}"
               class="group bg-white rounded-2xl shadow-md border-2 border-emerald-200 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-emerald-400">
                <div class="h-3 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-trophy text-white text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 group-hover:text-emerald-600 transition-colors">المحصلة والنهائي</h4>
                    <p class="text-sm text-gray-500 mt-1">عرض المحصلة وإدخال النهائي</p>
                    @php
                        $finalCount = \App\Models\Score::where('subject_id', $subjectId)
                            ->where('term', $termId)->whereNull('month')
                            ->whereHas('student', fn($q) => $q->where('classroom_id', $classroomId))->count();
                    @endphp
                    @if($finalCount > 0)
                        <span class="inline-block mt-3 px-3 py-1 bg-emerald-100 text-emerald-600 rounded-full text-xs font-medium">
                            {{ $finalCount }} درجة نهائية
                        </span>
                    @endif
                </div>
            </a>
        </div>
    @else
        {{-- عرض جدول الدرجات للشهر المحدد --}}
        @php
            $monthNames = [1 => 'الشهر الأول', 2 => 'الشهر الثاني', 3 => 'الشهر الثالث'];
        @endphp
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    📋 درجات {{ $monthNames[$monthId] ?? '' }}
                </h3>
                <a href="{{ route('teacher.scores.create', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => $termId, 'month' => $monthId]) }}" 
                   class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition">
                    <i class="fas fa-edit ml-2"></i>تعديل الدرجات
                </a>
            </div>
        </div>
        
        @if($scores && $scores->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-4 text-right text-sm font-semibold text-gray-600">الطالب</th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">الحضور<br><span class="text-xs text-gray-400">(20)</span></th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">الواجبات<br><span class="text-xs text-gray-400">(20)</span></th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">المواظبة<br><span class="text-xs text-gray-400">(20)</span></th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600">التحريري<br><span class="text-xs text-gray-400">(40)</span></th>
                                <th class="px-3 py-4 text-center text-sm font-semibold text-gray-600 bg-emerald-50">المجموع<br><span class="text-xs text-emerald-400">(100)</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($scores as $score)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($score->student->name) }}&background=6366f1&color=fff" 
                                                 alt="" class="w-10 h-10 rounded-full">
                                            <div>
                                                <p class="font-semibold text-gray-800 text-sm">{{ $score->student->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $score->student->student_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center font-medium text-gray-700">{{ $score->attendance ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center font-medium text-gray-700">{{ $score->homework ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center font-medium text-gray-700">{{ $score->discipline ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center font-medium text-gray-700">{{ $score->written ?? '-' }}</td>
                                    <td class="px-3 py-4 text-center font-bold text-emerald-600 bg-emerald-50">{{ $score->month_total ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($scores->hasPages())
                    <div class="p-4 border-t">
                        {{ $scores->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-4xl text-emerald-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد درجات مسجلة</h3>
                <p class="text-gray-500 mb-4">لم يتم تسجيل أي درجات لهذا الشهر</p>
                <a href="{{ route('teacher.scores.create', ['classroom_id' => $classroomId, 'subject_id' => $subjectId, 'term' => $termId, 'month' => $monthId]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition shadow-lg">
                    <i class="fas fa-plus ml-2"></i>إضافة درجات
                </a>
            </div>
        @endif
    @endif
@endif
@endsection
