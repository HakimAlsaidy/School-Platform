@extends('layouts.dashboard')

@section('page-title', 'جدولي الدراسي')
@section('page-description', 'عرض جدول الحصص الأسبوعي')

@section('dashboard-content')
<div class="space-y-6">
    {{-- الإحصائيات --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-indigo-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_periods'] }}</div>
                    <div class="text-sm text-gray-500">حصة أسبوعياً</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-green-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['subjects'] }}</div>
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
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['classrooms'] }}</div>
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
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['days_count'] }}</div>
                    <div class="text-sm text-gray-500">يوم عمل</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- جدول الحصص --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gradient-to-l from-indigo-600 to-indigo-700 text-white">
            <h3 class="font-bold text-lg">
                <i class="fas fa-calendar-alt ml-2"></i>
                جدول الحصص الأسبوعي
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 w-24">الحصة</th>
                        @foreach($days as $dayKey => $dayName)
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600 {{ strtolower(now()->format('l')) == $dayKey ? 'bg-indigo-50 text-indigo-600' : '' }}">
                            {{ $dayName }}
                            @if(strtolower(now()->format('l')) == $dayKey)
                            <span class="block text-xs text-indigo-500">اليوم</span>
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
                            $isToday = strtolower(now()->format('l')) == $dayKey;
                        @endphp
                        <td class="px-2 py-2 {{ $isToday ? 'bg-indigo-50/50' : '' }}">
                            @if($schedule)
                            <div class="bg-gradient-to-br {{ $isToday ? 'from-indigo-500 to-indigo-600 text-white' : 'from-gray-50 to-gray-100' }} rounded-xl p-2 flex items-center gap-2">
                                @if($schedule->start_time && $schedule->end_time)
                                <div class="{{ $isToday ? 'bg-white/20' : 'bg-indigo-600' }} text-white rounded-lg px-2 py-1 text-center min-w-[50px]">
                                    <div class="text-xs font-bold">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</div>
                                    <div class="text-[10px] opacity-80">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                                </div>
                                @endif
                                <div class="flex-1">
                                    <div class="font-semibold {{ $isToday ? 'text-white' : 'text-indigo-700' }} text-sm">{{ $schedule->subject->name }}</div>
                                    <div class="text-xs {{ $isToday ? 'text-indigo-100' : 'text-gray-500' }}">
                                        {{ $schedule->classroom->grade->name }} - {{ $schedule->classroom->name }}
                                    </div>
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
    
    {{-- توزيع الحصص على الأيام --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-bar text-indigo-600 ml-2"></i>
            توزيع الحصص على الأيام
        </h3>
        <div class="flex gap-4 flex-wrap">
            @foreach($days as $dayKey => $dayName)
            @php
                $dayCount = isset($schedules[$dayKey]) ? $schedules[$dayKey]->count() : 0;
                $isToday = strtolower(now()->format('l')) == $dayKey;
            @endphp
            <div class="flex-1 min-w-[100px]">
                <div class="text-center p-4 {{ $isToday ? 'bg-indigo-100 border-2 border-indigo-300' : 'bg-gray-50' }} rounded-xl">
                    <div class="text-sm {{ $isToday ? 'text-indigo-700 font-bold' : 'text-gray-600' }} mb-2">{{ $dayName }}</div>
                    <div class="text-2xl font-bold {{ $isToday ? 'text-indigo-600' : 'text-gray-800' }}">{{ $dayCount }}</div>
                    <div class="text-xs {{ $isToday ? 'text-indigo-600' : 'text-gray-500' }}">حصة</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
