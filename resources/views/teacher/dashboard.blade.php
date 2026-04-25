@extends('layouts.dashboard')

@section('page-title', 'لوحة التحكم')
@section('page-description', 'مرحباً بك في لوحة تحكم المعلم')

@section('dashboard-content')
<!-- Stats Cards - Responsive -->
<div class="stats-grid-responsive mb-6 sm:mb-8">
    <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-door-open text-xl sm:text-2xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['classrooms'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500">فصل</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-book text-xl sm:text-2xl text-green-600"></i>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['subjects'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500">مادة</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-graduate text-xl sm:text-2xl text-purple-600"></i>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['students'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500">طالب</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-tasks text-xl sm:text-2xl text-amber-600"></i>
            </div>
            <div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $stats['assignments'] }}</p>
                <p class="text-xs sm:text-sm text-gray-500">واجب</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions - Mobile First -->
<div class="mb-6 sm:mb-8 lg:hidden">
    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-4">إجراءات سريعة</h3>
    <div class="grid grid-cols-4 gap-2 sm:gap-3">
        <a href="{{ route('teacher.attendance.index') }}" class="bg-green-50 hover:bg-green-100 rounded-xl p-3 text-center transition">
            <i class="fas fa-clipboard-check text-xl sm:text-2xl text-green-600 mb-1"></i>
            <p class="text-xs font-medium text-green-800">الحضور</p>
        </a>
        <a href="{{ route('teacher.scores.create') }}" class="bg-blue-50 hover:bg-blue-100 rounded-xl p-3 text-center transition">
            <i class="fas fa-star text-xl sm:text-2xl text-blue-600 mb-1"></i>
            <p class="text-xs font-medium text-blue-800">الدرجات</p>
        </a>
        <a href="{{ route('teacher.assignments.create') }}" class="bg-amber-50 hover:bg-amber-100 rounded-xl p-3 text-center transition">
            <i class="fas fa-plus text-xl sm:text-2xl text-amber-600 mb-1"></i>
            <p class="text-xs font-medium text-amber-800">واجب</p>
        </a>
        <a href="{{ route('teacher.behaviors.create') }}" class="bg-purple-50 hover:bg-purple-100 rounded-xl p-3 text-center transition">
            <i class="fas fa-award text-xl sm:text-2xl text-purple-600 mb-1"></i>
            <p class="text-xs font-medium text-purple-800">سلوك</p>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Today's Schedule -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-clock text-indigo-500 ml-2"></i>
                جدول اليوم
            </h3>
        </div>
        <div class="p-6">
            @forelse($todaySchedules as $schedule)
                <div class="flex items-center gap-4 mb-4 last:mb-0 p-4 bg-gray-50 rounded-xl">
                    <div class="text-center">
                        <p class="text-lg font-bold text-indigo-600">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $schedule->subject->name }}</p>
                        <p class="text-sm text-gray-500">{{ $schedule->classroom->full_name }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-day text-4xl mb-3 text-gray-300"></i>
                    <p>لا توجد حصص اليوم</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Upcoming Assignments -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-tasks text-indigo-500 ml-2"></i>
                    الواجبات القادمة
                </h3>
                <a href="{{ route('teacher.assignments.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">عرض الكل</a>
            </div>
        </div>
        <div class="divide-y">
            @forelse($upcomingAssignments as $assignment)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $assignment->title }}</p>
                            <p class="text-sm text-gray-500">{{ $assignment->subject->name }} - {{ $assignment->classroom->full_name }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium text-{{ $assignment->due_date->isToday() ? 'red' : 'gray' }}-600">
                                {{ $assignment->due_date->diffForHumans() }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $assignment->submission_count }} تسليم</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-clipboard-check text-4xl mb-3 text-gray-300"></i>
                    <p>لا توجد واجبات قادمة</p>
                </div>
            @endforelse
        </div>
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
                    <p class="font-semibold text-gray-800 text-sm sm:text-base">{{ $announcement->title }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 line-clamp-2">{{ Str::limit($announcement->content, 100) }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $announcement->created_at->diffForHumans() }}</p>
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
                    <div class="flex items-center gap-3">
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

<!-- Quick Actions - Desktop Only -->
<div class="mt-6 sm:mt-8 hidden lg:block">
    <h3 class="text-lg font-bold text-gray-800 mb-4">إجراءات سريعة</h3>
    <div class="grid grid-cols-4 gap-4">
        <a href="{{ route('teacher.attendance.index') }}" class="bg-green-50 hover:bg-green-100 rounded-xl p-4 text-center transition">
            <i class="fas fa-clipboard-check text-3xl text-green-600 mb-2"></i>
            <p class="font-semibold text-green-800">تسجيل الحضور</p>
        </a>
        <a href="{{ route('teacher.scores.create') }}" class="bg-blue-50 hover:bg-blue-100 rounded-xl p-4 text-center transition">
            <i class="fas fa-star text-3xl text-blue-600 mb-2"></i>
            <p class="font-semibold text-blue-800">إضافة درجات</p>
        </a>
        <a href="{{ route('teacher.assignments.create') }}" class="bg-amber-50 hover:bg-amber-100 rounded-xl p-4 text-center transition">
            <i class="fas fa-plus text-3xl text-amber-600 mb-2"></i>
            <p class="font-semibold text-amber-800">إنشاء واجب</p>
        </a>
        <a href="{{ route('teacher.behaviors.create') }}" class="bg-purple-50 hover:bg-purple-100 rounded-xl p-4 text-center transition">
            <i class="fas fa-award text-3xl text-purple-600 mb-2"></i>
            <p class="font-semibold text-purple-800">تسجيل سلوك</p>
        </a>
    </div>
</div>
@endsection
