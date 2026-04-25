@extends('layouts.dashboard')

@section('page-title', 'تقرير الطلاب')
@section('page-description', 'تحليل شامل لبيانات الطلاب')

@section('dashboard-content')
<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.reports.students') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الصف</label>
            <select name="grade_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">كل الصفوف</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                        {{ $grade->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الفصل</label>
            <select name="classroom_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="">كل الفصول</option>
                @foreach($grades as $grade)
                    <optgroup label="{{ $grade->name }}">
                        @foreach($grade->classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-filter ml-2"></i>تصفية
            </button>
        </div>
        
        <div class="flex items-end">
            <a href="{{ route('admin.reports.students') }}" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition text-center">
                <i class="fas fa-undo ml-2"></i>إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">إجمالي الطلاب</p>
                <p class="text-2xl font-bold text-gray-800">{{ $students->total() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">متوسط الحضور</p>
                <p class="text-2xl font-bold text-gray-800">
                    @php
                        $avgAttendance = $students->avg('attendance_count') ?? 0;
                        $avgAbsent = $students->avg('absent_count') ?? 0;
                        $total = $avgAttendance + $avgAbsent;
                        $percentage = $total > 0 ? round(($avgAttendance / $total) * 100) : 0;
                    @endphp
                    {{ $percentage }}%
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-yellow-50 flex items-center justify-center">
                <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">متوسط الدرجات</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($students->avg('scores_avg_score') ?? 0, 1) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">طلاب بحاجة متابعة</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ $students->where('scores_avg_score', '<', 50)->count() }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Students Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">
            <i class="fas fa-table ml-2 text-indigo-600"></i>
            قائمة الطلاب
        </h3>
        <span class="text-sm text-gray-500">{{ $students->total() }} طالب</span>
    </div>
    
    @if($students->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">الطالب</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">الفصل</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">أيام الحضور</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">أيام الغياب</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">نسبة الحضور</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">متوسط الدرجات</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">الحالة</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($students as $student)
                        @php
                            $totalDays = $student->attendance_count + $student->absent_count;
                            $attendanceRate = $totalDays > 0 ? ($student->attendance_count / $totalDays) * 100 : 0;
                            $avgScore = $student->scores_avg_score ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $student->student_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">
                                {{ $student->classroom->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-green-600">{{ $student->attendance_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-red-600">{{ $student->absent_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($attendanceRate >= 90)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        {{ number_format($attendanceRate, 0) }}%
                                    </span>
                                @elseif($attendanceRate >= 75)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                        {{ number_format($attendanceRate, 0) }}%
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        {{ number_format($attendanceRate, 0) }}%
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($avgScore >= 80)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        {{ number_format($avgScore, 1) }}
                                    </span>
                                @elseif($avgScore >= 60)
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                        {{ number_format($avgScore, 1) }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                        {{ number_format($avgScore, 1) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($avgScore >= 60 && $attendanceRate >= 75)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                        <i class="fas fa-check ml-1"></i>جيد
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">
                                        <i class="fas fa-exclamation ml-1"></i>يحتاج متابعة
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.reports.student', $student) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                                    <i class="fas fa-eye ml-1"></i>التفاصيل
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100">
            {{ $students->withQueryString()->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500">لا يوجد طلاب مطابقين للبحث</p>
        </div>
    @endif
</div>
@endsection
