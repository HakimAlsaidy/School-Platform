@extends('layouts.dashboard')

@section('page-title', $student->name)
@section('page-description', 'متابعة مستوى الطالب')

@section('dashboard-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Student Info Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
            <div class="gradient-bg p-6 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=120&background=ffffff&color=6366f1" 
                     alt="{{ $student->name }}" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-white/30">
                <h3 class="text-xl font-bold text-white">{{ $student->name }}</h3>
                <p class="text-indigo-200">{{ $student->student_id }}</p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-door-open text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الفصل</p>
                        <p class="font-semibold">{{ $student->classroom->full_name ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-birthday-cake text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">تاريخ الميلاد</p>
                        <p class="font-semibold">{{ $student->birth_date->format('Y/m/d') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="p-4 border-t space-y-2">
                <a href="{{ route('parent.students.attendance', $student) }}" class="block w-full py-2 px-4 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition text-center">
                    <i class="fas fa-clipboard-check ml-1"></i> سجل الحضور
                </a>
                <a href="{{ route('parent.students.scores', $student) }}" class="block w-full py-2 px-4 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition text-center">
                    <i class="fas fa-chart-line ml-1"></i> الدرجات
                </a>
                <a href="{{ route('parent.students.behaviors', $student) }}" class="block w-full py-2 px-4 bg-purple-50 text-purple-700 rounded-xl hover:bg-purple-100 transition text-center">
                    <i class="fas fa-award ml-1"></i> السلوك
                </a>
                <a href="{{ route('parent.students.schedule', $student) }}" class="block w-full py-2 px-4 bg-amber-50 text-amber-700 rounded-xl hover:bg-amber-100 transition text-center">
                    <i class="fas fa-calendar-alt ml-1"></i> الجدول الدراسي
                </a>
            </div>
        </div>
    </div>
    
    <!-- Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $attendanceStats['present'] }}</p>
                <p class="text-sm text-gray-500">حضور</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $attendanceStats['absent'] }}</p>
                <p class="text-sm text-gray-500">غياب</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $attendanceStats['late'] }}</p>
                <p class="text-sm text-gray-500">تأخير</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ round($student->average_score) }}</p>
                <p class="text-sm text-gray-500">المعدل</p>
            </div>
        </div>
        
        <!-- Scores by Subject -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800">
                    <i class="fas fa-chart-bar text-indigo-500 ml-2"></i>
                    الدرجات حسب المادة
                </h4>
            </div>
            <div class="p-4">
                @forelse($scoresBySubject as $subjectData)
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-800">{{ $subjectData['subject'] }}</span>
                            <span class="text-sm font-bold text-{{ $subjectData['average'] >= 60 ? 'green' : 'red' }}-600">
                                {{ $subjectData['average'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500
                                @if($subjectData['average'] >= 90) bg-green-500
                                @elseif($subjectData['average'] >= 70) bg-blue-500
                                @elseif($subjectData['average'] >= 60) bg-amber-500
                                @else bg-red-500 @endif"
                                style="width: {{ min($subjectData['average'], 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">لا توجد درجات مسجلة</p>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Attendance -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-gray-800">
                        <i class="fas fa-clipboard-check text-indigo-500 ml-2"></i>
                        سجل الحضور الأخير
                    </h4>
                    <a href="{{ route('parent.students.attendance', $student) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap gap-2">
                    @forelse($student->attendances->take(21) as $attendance)
                        <div class="w-10 h-10 rounded-lg flex flex-col items-center justify-center text-xs font-medium
                            @if($attendance->status == 'present') bg-green-100 text-green-700
                            @elseif($attendance->status == 'absent') bg-red-100 text-red-700
                            @elseif($attendance->status == 'late') bg-amber-100 text-amber-700
                            @else bg-blue-100 text-blue-700 @endif"
                            title="{{ $attendance->status_label }} - {{ $attendance->date->format('Y/m/d') }}">
                            <span>{{ $attendance->date->format('d') }}</span>
                            <span class="text-[10px]">{{ $attendance->date->format('m') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">لا يوجد سجل حضور</p>
                    @endforelse
                </div>
                <div class="flex items-center gap-4 mt-4 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-100 rounded"></span> حاضر</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-100 rounded"></span> غائب</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-amber-100 rounded"></span> متأخر</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-100 rounded"></span> معذور</span>
                </div>
            </div>
        </div>
        
        <!-- Recent Behaviors -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-gray-800">
                        <i class="fas fa-award text-indigo-500 ml-2"></i>
                        سجل السلوك
                    </h4>
                    <a href="{{ route('parent.students.behaviors', $student) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        عرض الكل
                    </a>
                </div>
            </div>
            <div class="divide-y">
                @forelse($student->behaviors->take(5) as $behavior)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ $behavior->type == 'positive' ? 'bg-green-100' : 'bg-red-100' }}">
                            <i class="fas fa-{{ $behavior->type == 'positive' ? 'thumbs-up' : 'thumbs-down' }} 
                                {{ $behavior->type == 'positive' ? 'text-green-600' : 'text-red-600' }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $behavior->title }}</p>
                            <p class="text-sm text-gray-500">{{ $behavior->teacher->user->name ?? '-' }} - {{ $behavior->date->format('Y/m/d') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $behavior->type == 'positive' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $behavior->points > 0 ? '+' : '' }}{{ $behavior->points }}
                        </span>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        لا يوجد سجل سلوك
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
