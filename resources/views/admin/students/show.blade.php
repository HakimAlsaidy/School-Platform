@extends('layouts.dashboard')

@section('page-title', $student->name)
@section('page-description', 'عرض تفاصيل الطالب')

@section('dashboard-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Student Info Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="gradient-bg p-6 text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=120&background=ffffff&color=6366f1" 
                     alt="{{ $student->name }}" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-white/30">
                <h3 class="text-xl font-bold text-white">{{ $student->name }}</h3>
                <p class="text-indigo-200">{{ $student->student_id }}</p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-venus-mars text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الجنس</p>
                        <p class="font-semibold">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
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
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-door-open text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">الفصل</p>
                        <p class="font-semibold">{{ $student->classroom->full_name ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ولي الأمر</p>
                        <p class="font-semibold">{{ $student->guardian->user->name ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="pt-4 border-t flex gap-2">
                    <a href="{{ route('admin.students.edit', $student) }}" class="flex-1 px-4 py-2 bg-amber-100 text-amber-700 rounded-xl hover:bg-amber-200 transition text-center">
                        <i class="fas fa-edit ml-1"></i> تعديل
                    </a>
                    <a href="{{ route('admin.reports.student', $student) }}" class="flex-1 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl hover:bg-indigo-200 transition text-center">
                        <i class="fas fa-file-alt ml-1"></i> التقرير
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats & Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-green-600">{{ round($student->attendance_rate) }}%</p>
                <p class="text-sm text-gray-500">نسبة الحضور</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ round($student->average_score) }}</p>
                <p class="text-sm text-gray-500">معدل الدرجات</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-purple-600">{{ $student->behaviors->where('type', 'positive')->count() }}</p>
                <p class="text-sm text-gray-500">سلوك إيجابي</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-100 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $student->behaviors->where('type', 'negative')->count() }}</p>
                <p class="text-sm text-gray-500">سلوك سلبي</p>
            </div>
        </div>
        
        <!-- Recent Attendance -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800">
                    <i class="fas fa-clipboard-check text-indigo-500 ml-2"></i>
                    سجل الحضور الأخير
                </h4>
            </div>
            <div class="p-4">
                <div class="flex flex-wrap gap-2">
                    @forelse($student->attendances->take(14) as $attendance)
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xs font-medium
                            @if($attendance->status == 'present') bg-green-100 text-green-700
                            @elseif($attendance->status == 'absent') bg-red-100 text-red-700
                            @elseif($attendance->status == 'late') bg-amber-100 text-amber-700
                            @else bg-blue-100 text-blue-700 @endif"
                            title="{{ $attendance->date->format('Y/m/d') }}">
                            {{ $attendance->date->format('d') }}
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">لا يوجد سجل حضور</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Recent Scores -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800">
                    <i class="fas fa-star text-indigo-500 ml-2"></i>
                    آخر الدرجات
                </h4>
            </div>
            <div class="divide-y">
                @forelse($student->scores->take(5) as $score)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $score->subject->name }}</p>
                            <p class="text-sm text-gray-500">{{ $score->exam_type_label }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-lg font-bold text-{{ $score->percentage >= 60 ? 'green' : 'red' }}-600">
                                {{ $score->score }}/{{ $score->max_score }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $score->grade }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500">
                        لا توجد درجات مسجلة
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Recent Behaviors -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h4 class="font-bold text-gray-800">
                    <i class="fas fa-award text-indigo-500 ml-2"></i>
                    سجل السلوك
                </h4>
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
