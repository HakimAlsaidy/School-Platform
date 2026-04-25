@extends('layouts.dashboard')

@section('page-title', 'لوحة التحكم')
@section('page-description', 'نظرة عامة على النظام المدرسي')

@section('dashboard-content')
<!-- Pending Registration Alert -->
@if(isset($pendingUsersCount) && $pendingUsersCount > 0)
<div class="mb-6 p-4 bg-gradient-to-l from-orange-50 to-amber-50 border border-orange-200 rounded-2xl">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-clock text-2xl text-orange-600 animate-pulse"></i>
            </div>
            <div>
                <h4 class="font-bold text-orange-800">طلبات تسجيل جديدة!</h4>
                <p class="text-sm text-orange-600">يوجد {{ $pendingUsersCount }} طلب تسجيل بانتظار الموافقة</p>
            </div>
        </div>
        <a href="{{ route('admin.pending-users') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-600 text-white rounded-xl font-semibold hover:bg-orange-700 transition shadow-lg shadow-orange-200">
            <i class="fas fa-eye"></i>
            عرض الطلبات
            <span class="bg-white text-orange-600 px-2 py-0.5 rounded-lg text-sm font-bold">{{ $pendingUsersCount }}</span>
        </a>
    </div>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['students'] }}</p>
                <p class="text-sm text-gray-500">طالب</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['teachers'] }}</p>
                <p class="text-sm text-gray-500">معلم</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-2xl text-purple-600"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['guardians'] }}</p>
                <p class="text-sm text-gray-500">ولي أمر</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-door-open text-2xl text-amber-600"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['classrooms'] }}</p>
                <p class="text-sm text-gray-500">فصل</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-layer-group text-2xl text-indigo-600"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['grades'] }}</p>
                <p class="text-sm text-gray-500">صف دراسي</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-rose-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-book text-2xl text-rose-600"></i>
            </div>
            <div>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['subjects'] }}</p>
                <p class="text-sm text-gray-500">مادة</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Today's Attendance -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-clipboard-check text-indigo-500 ml-2"></i>
                    حضور اليوم
                </h3>
                <span class="text-sm text-gray-500">{{ now()->locale('ar')->translatedFormat('d F Y') }}</span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-4 gap-4 text-center">
                <div class="p-4 bg-green-50 rounded-xl">
                    <p class="text-2xl font-bold text-green-600">{{ $todayAttendance['present'] ?? 0 }}</p>
                    <p class="text-sm text-green-700">حاضر</p>
                </div>
                <div class="p-4 bg-red-50 rounded-xl">
                    <p class="text-2xl font-bold text-red-600">{{ $todayAttendance['absent'] ?? 0 }}</p>
                    <p class="text-sm text-red-700">غائب</p>
                </div>
                <div class="p-4 bg-amber-50 rounded-xl">
                    <p class="text-2xl font-bold text-amber-600">{{ $todayAttendance['late'] ?? 0 }}</p>
                    <p class="text-sm text-amber-700">متأخر</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl">
                    <p class="text-2xl font-bold text-blue-600">{{ $todayAttendance['excused'] ?? 0 }}</p>
                    <p class="text-sm text-blue-700">معذور</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Events -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-calendar-alt text-indigo-500 ml-2"></i>
                الأحداث القادمة
            </h3>
        </div>
        <div class="p-6">
            @forelse($upcomingEvents as $event)
                <div class="flex items-start gap-4 mb-4 last:mb-0">
                    <div class="w-12 h-12 bg-{{ $event->type_color }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar text-{{ $event->type_color }}-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $event->title }}</p>
                        <p class="text-sm text-gray-500">{{ $event->start_date->locale('ar')->translatedFormat('d F Y - H:i') }}</p>
                        <span class="inline-block px-2 py-0.5 bg-{{ $event->type_color }}-100 text-{{ $event->type_color }}-700 text-xs rounded-full mt-1">
                            {{ $event->type_label }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                    <p>لا توجد أحداث قادمة</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Students -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-user-graduate text-indigo-500 ml-2"></i>
                    أحدث الطلاب المسجلين
                </h3>
                <a href="{{ route('admin.students.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">عرض الكل</a>
            </div>
        </div>
        <div class="divide-y">
            @forelse($recentStudents as $student)
                <div class="p-4 flex items-center gap-4 hover:bg-gray-50">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff" 
                         alt="{{ $student->name }}" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $student->name }}</p>
                        <p class="text-sm text-gray-500">{{ $student->classroom->full_name ?? '-' }}</p>
                    </div>
                    <a href="{{ route('admin.students.show', $student) }}" class="text-indigo-600 hover:text-indigo-700">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                    <p>لا يوجد طلاب مسجلين</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Announcements -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-bullhorn text-indigo-500 ml-2"></i>
                    آخر الإعلانات
                </h3>
                <a href="{{ route('admin.announcements.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">عرض الكل</a>
            </div>
        </div>
        <div class="divide-y">
            @forelse($announcements as $announcement)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start gap-3">
                        @if($announcement->is_pinned)
                            <i class="fas fa-thumbtack text-amber-500 mt-1"></i>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $announcement->title }}</p>
                            <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($announcement->content, 100) }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span><i class="fas fa-user ml-1"></i>{{ $announcement->author->name }}</span>
                                <span><i class="fas fa-clock ml-1"></i>{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bullhorn text-4xl mb-3 text-gray-300"></i>
                    <p>لا توجد إعلانات</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
