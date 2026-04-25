@extends('layouts.dashboard')

@section('page-title', 'تقرير الحضور والغياب')
@section('page-description', 'إحصائيات شاملة عن حضور وغياب الطلاب')

@section('dashboard-content')
<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.reports.attendance') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الفصل</label>
            <select name="classroom_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">كل الفصول</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->grade->name }} - {{ $classroom->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
            <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">الكل</option>
                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>معذور</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-filter ml-2"></i>تصفية
            </button>
        </div>
    </form>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-50 rounded-2xl p-6 text-center border border-green-100">
        <p class="text-3xl font-bold text-green-600">{{ $stats['present'] }}</p>
        <p class="text-green-700">حاضر</p>
        <p class="text-sm text-green-600">{{ $stats['total'] > 0 ? round(($stats['present'] / $stats['total']) * 100) : 0 }}%</p>
    </div>
    <div class="bg-red-50 rounded-2xl p-6 text-center border border-red-100">
        <p class="text-3xl font-bold text-red-600">{{ $stats['absent'] }}</p>
        <p class="text-red-700">غائب</p>
        <p class="text-sm text-red-600">{{ $stats['total'] > 0 ? round(($stats['absent'] / $stats['total']) * 100) : 0 }}%</p>
    </div>
    <div class="bg-amber-50 rounded-2xl p-6 text-center border border-amber-100">
        <p class="text-3xl font-bold text-amber-600">{{ $stats['late'] }}</p>
        <p class="text-amber-700">متأخر</p>
        <p class="text-sm text-amber-600">{{ $stats['total'] > 0 ? round(($stats['late'] / $stats['total']) * 100) : 0 }}%</p>
    </div>
    <div class="bg-blue-50 rounded-2xl p-6 text-center border border-blue-100">
        <p class="text-3xl font-bold text-blue-600">{{ $stats['excused'] }}</p>
        <p class="text-blue-700">معذور</p>
        <p class="text-sm text-blue-600">{{ $stats['total'] > 0 ? round(($stats['excused'] / $stats['total']) * 100) : 0 }}%</p>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الطالب</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الفصل</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">التاريخ</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">ملاحظات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $attendance->student->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $attendance->student->classroom->full_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $attendance->date->format('Y/m/d') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm bg-{{ $attendance->status_color }}-100 text-{{ $attendance->status_color }}-700">
                                {{ $attendance->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $attendance->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clipboard text-4xl mb-3 text-gray-300"></i>
                            <p>لا توجد سجلات حضور</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($attendances->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
@endsection
