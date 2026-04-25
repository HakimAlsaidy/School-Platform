@extends('layouts.dashboard')

@section('page-title', 'جدول ' . $student->name . ' الدراسي')
@section('page-description', 'الجدول الدراسي الأسبوعي')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('parent.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لصفحة الطالب
    </a>
</div>

<!-- Schedule Info -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-door-open text-indigo-600"></i>
        </div>
        <div>
            <h3 class="font-bold text-gray-800">{{ $student->classroom->full_name ?? 'لم يتم تحديد الفصل' }}</h3>
            <p class="text-sm text-gray-500">جدول الحصص الأسبوعي</p>
        </div>
    </div>
</div>

@php
    $todayName = strtolower(now()->format('l'));
@endphp

<!-- Weekly Schedule -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[800px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 w-24">الحصة</th>
                    @foreach($days as $dayKey => $dayName)
                        <th class="px-4 py-3 text-center text-sm font-semibold 
                            {{ $todayName == $dayKey ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600' }}">
                            {{ $dayName }}
                            @if($todayName == $dayKey)
                                <span class="block text-xs font-normal text-indigo-500">اليوم</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($periods as $periodNum => $times)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">
                            <div class="font-bold text-indigo-600 text-lg">{{ $periodNum }}</div>
                        </td>
                        @foreach($days as $dayKey => $dayName)
                            @php
                                $schedule = isset($schedules[$dayKey]) 
                                    ? $schedules[$dayKey]->firstWhere('period_number', $periodNum) 
                                    : null;
                            @endphp
                            <td class="px-2 py-2 {{ $todayName == $dayKey ? 'bg-indigo-50/50' : '' }}">
                                @if($schedule)
                                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-2 flex items-center gap-2">
                                        @if($schedule->start_time && $schedule->end_time)
                                        <div class="bg-indigo-600 text-white rounded-lg px-2 py-1 text-center min-w-[50px]">
                                            <div class="text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</div>
                                            <div class="text-[10px] opacity-80">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                                        </div>
                                        @endif
                                        <div class="flex-1">
                                            <div class="font-semibold text-indigo-700 text-sm">{{ $schedule->subject->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $schedule->teacher->user->name ?? '' }}</div>
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

<!-- Today's Schedule Details -->
<div class="mt-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-clock text-indigo-500 ml-2"></i>
        جدول اليوم ({{ $days[$todayName] ?? 'الجمعة/السبت' }})
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
            $todaySchedules = isset($schedules[$todayName]) ? $schedules[$todayName]->sortBy('period_number') : collect();
        @endphp
        
        @forelse($todaySchedules as $schedule)
            <div class="bg-white rounded-xl p-4 border border-gray-100 card-hover">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <span class="text-lg font-bold text-indigo-600">{{ $schedule->period_number }}</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $schedule->subject->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $schedule->teacher->user->name ?? '' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium text-indigo-600">
                            {{ $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '' }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-gray-50 rounded-xl p-6 text-center text-gray-500">
                <i class="fas fa-calendar-day text-3xl mb-2 text-gray-300"></i>
                <p>لا توجد حصص اليوم</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
