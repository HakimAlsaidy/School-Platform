@extends('layouts.dashboard')

@section('page-title', 'لوحة التحكم')
@section('page-description', 'متابعة أبنائك ومستواهم الدراسي')

@section('dashboard-content')
<!-- Stats Cards - Responsive -->
<div class="grid grid-cols-3 gap-3 sm:gap-6 mb-6 sm:mb-8">
    <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center sm:gap-4 text-center sm:text-right">
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-2 sm:mb-0">
                <i class="fas fa-child text-lg sm:text-2xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['children'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500">الأبناء</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center sm:gap-4 text-center sm:text-right">
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-green-100 rounded-xl flex items-center justify-center mb-2 sm:mb-0">
                <i class="fas fa-clipboard-check text-lg sm:text-2xl text-green-600"></i>
            </div>
            <div>
                <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ round($stats['average_attendance'] ?? 0) }}%</p>
                <p class="text-xs sm:text-sm text-gray-500">الحضور</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center sm:gap-4 text-center sm:text-right">
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-2 sm:mb-0">
                <i class="fas fa-chart-line text-lg sm:text-2xl text-purple-600"></i>
            </div>
            <div>
                <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ round($stats['average_score'] ?? 0) }}</p>
                <p class="text-xs sm:text-sm text-gray-500">المعدل</p>
            </div>
        </div>
    </div>
</div>

<!-- Children Overview - Responsive -->
<div class="mb-6 sm:mb-8">
    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4">
        <i class="fas fa-users text-indigo-500 ml-2"></i>
        أبنائي
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($students as $student)
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                <div class="gradient-bg p-4 sm:p-6 text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=80&background=ffffff&color=6366f1" 
                         alt="{{ $student->name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full mx-auto mb-2 sm:mb-3 border-4 border-white/30">
                    <h4 class="text-base sm:text-lg font-bold text-white">{{ $student->name }}</h4>
                    <p class="text-indigo-200 text-xs sm:text-sm">{{ $student->classroom->full_name ?? '-' }}</p>
                </div>
                
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-3 sm:mb-4">
                        <div class="text-center p-2 sm:p-3 bg-green-50 rounded-xl">
                            <p class="text-lg sm:text-xl font-bold text-green-600">{{ round($student->attendance_rate) }}%</p>
                            <p class="text-xs text-green-700">الحضور</p>
                        </div>
                        <div class="text-center p-2 sm:p-3 bg-blue-50 rounded-xl">
                            <p class="text-lg sm:text-xl font-bold text-blue-600">{{ round($student->average_score) }}</p>
                            <p class="text-xs text-blue-700">المعدل</p>
                        </div>
                    </div>
                    
                    <!-- Recent Attendance - Hide on very small screens -->
                    <div class="mb-3 sm:mb-4 hidden sm:block">
                        <p class="text-xs text-gray-500 mb-2">آخر الحضور</p>
                        <div class="flex gap-1">
                            @foreach($student->attendances as $attendance)
                                <div class="w-6 h-6 rounded flex items-center justify-center text-xs
                                    @if($attendance->status == 'present') bg-green-100 text-green-700
                                    @elseif($attendance->status == 'absent') bg-red-100 text-red-700
                                    @elseif($attendance->status == 'late') bg-amber-100 text-amber-700
                                    @else bg-blue-100 text-blue-700 @endif"
                                    title="{{ $attendance->status_label }}">
                                    {{ $attendance->date->format('d') }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <a href="{{ route('parent.students.show', $student) }}" 
                       class="block w-full py-2 sm:py-2.5 text-center bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition text-sm sm:text-base">
                        <i class="fas fa-eye ml-1"></i>
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
                <i class="fas fa-child text-4xl sm:text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg sm:text-xl font-bold text-gray-600 mb-2">لا يوجد أبناء مسجلين</h3>
                <p class="text-sm text-gray-500">يرجى التواصل مع إدارة المدرسة لإضافة أبنائك</p>
            </div>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    <!-- Announcements -->
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-800">
                <i class="fas fa-bullhorn text-indigo-500 ml-2"></i>
                الإعلانات
            </h3>
        </div>
        <div class="divide-y">
            @forelse($announcements as $announcement)
                <div class="p-3 sm:p-4 hover:bg-gray-50">
                    <div class="flex items-start gap-2 sm:gap-3">
                        @if($announcement->is_pinned)
                            <i class="fas fa-thumbtack text-amber-500 mt-1"></i>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ $announcement->title }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 line-clamp-2">{{ Str::limit($announcement->content, 80) }}</p>
                            <p class="text-xs text-gray-400 mt-1 sm:mt-2">{{ $announcement->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 sm:p-8 text-center text-gray-500">
                    <i class="fas fa-bullhorn text-3xl sm:text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm sm:text-base">لا توجد إعلانات</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Unread Messages -->
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold text-gray-800">
                    <i class="fas fa-envelope text-indigo-500 ml-2"></i>
                    رسائل جديدة
                </h3>
                <a href="{{ route('messages.inbox') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-700">عرض الكل</a>
            </div>
        </div>
        <div class="divide-y">
            @forelse($unreadMessages as $message)
                <a href="{{ route('messages.show', $message) }}" class="block p-3 sm:p-4 hover:bg-gray-50">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <img src="{{ $message->sender->avatar_url }}" alt="" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate text-sm sm:text-base">{{ $message->sender->name }}</p>
                            <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $message->subject }}</p>
                        </div>
                        <span class="text-xs text-gray-400 hidden sm:block">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <div class="p-6 sm:p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl sm:text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm sm:text-base">لا توجد رسائل جديدة</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
