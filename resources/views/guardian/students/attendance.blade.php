@extends('layouts.dashboard')

@section('page-title', 'سجل حضور ' . $student->name)
@section('page-description', 'سجل الحضور والغياب الكامل')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('parent.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لصفحة الطالب
    </a>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-check text-green-600"></i>
        </div>
        <p class="text-2xl font-bold text-green-600">{{ $stats['present'] }}</p>
        <p class="text-sm text-gray-500">حضور</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-times text-red-600"></i>
        </div>
        <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
        <p class="text-sm text-gray-500">غياب</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-clock text-amber-600"></i>
        </div>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['late'] }}</p>
        <p class="text-sm text-gray-500">تأخير</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
            <i class="fas fa-file-medical text-blue-600"></i>
        </div>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['excused'] }}</p>
        <p class="text-sm text-gray-500">معذور</p>
    </div>
</div>

<!-- Attendance Rate -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h4 class="font-bold text-gray-800">نسبة الحضور الإجمالية</h4>
        <span class="text-2xl font-bold text-{{ $attendanceRate >= 90 ? 'green' : ($attendanceRate >= 70 ? 'amber' : 'red') }}-600">
            {{ round($attendanceRate) }}%
        </span>
    </div>
    <div class="w-full bg-gray-100 rounded-full h-4">
        <div class="h-4 rounded-full transition-all duration-500
            @if($attendanceRate >= 90) bg-green-500
            @elseif($attendanceRate >= 70) bg-amber-500
            @else bg-red-500 @endif"
            style="width: {{ $attendanceRate }}%"></div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('parent.students.attendance', $student) }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
            <input type="date" name="from" value="{{ request('from') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
            <input type="date" name="to" value="{{ request('to') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">الكل</option>
                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>معذور</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
            <i class="fas fa-filter ml-2"></i>تصفية
        </button>
    </form>
</div>

<!-- Attendance Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">التاريخ</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">اليوم</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">ملاحظات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $attendance->date->format('Y/m/d') }}</td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $attendance->date->translatedFormat('l') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($attendance->status == 'present') bg-green-100 text-green-700
                                @elseif($attendance->status == 'absent') bg-red-100 text-red-700
                                @elseif($attendance->status == 'late') bg-amber-100 text-amber-700
                                @else bg-blue-100 text-blue-700 @endif">
                                {{ $attendance->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $attendance->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clipboard-check text-4xl mb-3 text-gray-300"></i>
                            <p>لا يوجد سجل حضور</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($attendances->hasPages())
        <div class="p-4 border-t">
            {{ $attendances->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
