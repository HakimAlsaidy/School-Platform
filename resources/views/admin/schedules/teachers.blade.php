@extends('layouts.dashboard')

@section('page-title', 'جداول المعلمين')
@section('page-description', 'عرض جداول المعلمين الأسبوعية')

@section('dashboard-content')
<div class="space-y-6">
    {{-- شريط الأدوات --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.schedules.index') }}" 
               class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
            
            <form method="GET" action="{{ route('admin.schedules.teachers') }}" class="flex items-center gap-3">
                <select name="teacher_id" onchange="this.form.submit()" 
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">-- اختر المعلم --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->user->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
    
    @if($selectedTeacher)
    {{-- معلومات المعلم --}}
    <div class="bg-gradient-to-l from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold">{{ $selectedTeacher->user->name }}</h2>
                <p class="text-emerald-200">
                    المواد: 
                    @foreach($selectedTeacher->subjects as $subject)
                        <span class="inline-block px-2 py-0.5 bg-white/20 rounded-full text-xs mr-1">{{ $subject->name }}</span>
                    @endforeach
                </p>
            </div>
            <div class="text-left">
                <div class="text-3xl font-bold">{{ $schedules->flatten()->count() }}</div>
                <div class="text-emerald-200 text-sm">حصة أسبوعياً</div>
            </div>
        </div>
    </div>
    
    {{-- جدول الحصص --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 w-24">الحصة</th>
                        @foreach($days as $dayKey => $dayName)
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">{{ $dayName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($periods as $periodNum => $times)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">
                            <div class="font-bold text-emerald-600 text-lg">{{ $periodNum }}</div>
                        </td>
                        @foreach($days as $dayKey => $dayName)
                        @php
                            $schedule = isset($schedules[$dayKey]) 
                                ? $schedules[$dayKey]->firstWhere('period_number', $periodNum) 
                                : null;
                        @endphp
                        <td class="px-2 py-2">
                            @if($schedule)
                            <div class="bg-emerald-50 rounded-xl p-2 flex items-center gap-2">
                                @if($schedule->start_time && $schedule->end_time)
                                <div class="bg-emerald-600 text-white rounded-lg px-2 py-1 text-center min-w-[50px]">
                                    <div class="text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</div>
                                    <div class="text-[10px] opacity-80">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                                </div>
                                @endif
                                <div class="flex-1">
                                    <div class="font-semibold text-emerald-700 text-sm">{{ $schedule->subject->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $schedule->classroom->grade->name }} - {{ $schedule->classroom->name }}</div>
                                </div>
                            </div>
                            @else
                            <div class="h-14 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300">
                                <i class="fas fa-minus"></i>
                            </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- إحصائيات --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-emerald-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $schedules->flatten()->count() }}</div>
                    <div class="text-sm text-gray-500">حصة أسبوعياً</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-blue-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $schedules->flatten()->unique('subject_id')->count() }}</div>
                    <div class="text-sm text-gray-500">مادة</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-purple-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $schedules->flatten()->unique('classroom_id')->count() }}</div>
                    <div class="text-sm text-gray-500">فصل</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-amber-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $schedules->keys()->count() }}</div>
                    <div class="text-sm text-gray-500">يوم عمل</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- توزيع الحصص على الأيام --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4">توزيع الحصص على الأيام</h3>
        <div class="flex gap-4 flex-wrap">
            @foreach($days as $dayKey => $dayName)
            @php
                $dayCount = isset($schedules[$dayKey]) ? $schedules[$dayKey]->count() : 0;
            @endphp
            <div class="flex-1 min-w-[100px]">
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-sm text-gray-600 mb-2">{{ $dayName }}</div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $dayCount }}</div>
                    <div class="text-xs text-gray-500">حصة</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    {{-- رسالة اختيار المعلم --}}
    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-chalkboard-teacher text-4xl text-emerald-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">اختر معلماً لعرض جدوله</h3>
        <p class="text-gray-500 mb-6">قم باختيار المعلم من القائمة أعلاه لعرض جدول حصصه الأسبوعي</p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
            @foreach($teachers->take(8) as $teacher)
            <a href="{{ route('admin.schedules.teachers', ['teacher_id' => $teacher->id]) }}" 
               class="p-4 bg-gray-50 rounded-xl hover:bg-emerald-50 hover:border-emerald-200 border border-gray-100 transition">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fas fa-user text-emerald-600"></i>
                </div>
                <div class="font-semibold text-gray-800 text-sm">{{ $teacher->user->name }}</div>
                <div class="text-xs text-gray-500">{{ $teacher->subjects->count() }} مادة</div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
