@extends('layouts.dashboard')

@section('page-title', 'تقرير الطالب: ' . $student->name)
@section('page-description', 'تقرير شامل عن أداء الطالب')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.reports.students') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لتقارير الطلاب
    </a>
</div>

<!-- بطاقة معلومات الطالب -->
<div class="bg-gradient-to-l from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 mb-6 text-white">
    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center text-3xl">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-2">{{ $student->name }}</h2>
            <div class="flex flex-wrap gap-4 text-white/90 text-sm">
                <span><i class="fas fa-id-card ml-1"></i>{{ $student->student_number ?? 'غير محدد' }}</span>
                @if($student->classroom)
                    <span><i class="fas fa-school ml-1"></i>{{ $student->classroom->full_name }}</span>
                @endif
                @if($student->guardian)
                    <span><i class="fas fa-user ml-1"></i>ولي الأمر: {{ $student->guardian->user->name ?? 'غير محدد' }}</span>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.students.edit', $student) }}" 
               class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                <i class="fas fa-edit ml-1"></i>تعديل
            </a>
        </div>
    </div>
</div>

<!-- الإحصائيات السريعة -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <!-- الحضور الكلي -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                <i class="fas fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">إجمالي الحضور</p>
                <p class="text-xl font-bold text-gray-800">{{ $attendanceStats['total'] }}</p>
            </div>
        </div>
    </div>

    <!-- أيام الحضور -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">حاضر</p>
                <p class="text-xl font-bold text-green-600">{{ $attendanceStats['present'] }}</p>
            </div>
        </div>
    </div>

    <!-- أيام الغياب -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">غائب</p>
                <p class="text-xl font-bold text-red-600">{{ $attendanceStats['absent'] }}</p>
            </div>
        </div>
    </div>

    <!-- التأخير -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500">متأخر</p>
                <p class="text-xl font-bold text-yellow-600">{{ $attendanceStats['late'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- الدرجات حسب المادة -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                <i class="fas fa-chart-bar"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">الدرجات حسب المادة</h3>
        </div>
        <div class="p-4">
            @if($scoresBySubject->count() > 0)
                <div class="space-y-4">
                    @foreach($scoresBySubject as $subject)
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-medium text-gray-700">{{ $subject['subject'] }}</span>
                                    <span class="text-sm font-bold {{ $subject['average'] >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $subject['average'] }}%
                                    </span>
                                </div>
                                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $subject['average'] >= 50 ? 'bg-green-500' : 'bg-red-500' }} rounded-full transition-all"
                                         style="width: {{ min($subject['average'], 100) }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ $subject['count'] }} درجة مسجلة</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-clipboard-list text-4xl mb-2 opacity-50"></i>
                    <p>لا توجد درجات مسجلة</p>
                </div>
            @endif
        </div>
    </div>

    <!-- إحصائيات السلوك -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">إحصائيات السلوك</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-3 gap-4 text-center mb-6">
                <div class="p-4 bg-green-50 rounded-xl">
                    <p class="text-3xl font-bold text-green-600">{{ $behaviorStats['positive'] }}</p>
                    <p class="text-xs text-green-700 mt-1">سلوك إيجابي</p>
                </div>
                <div class="p-4 bg-red-50 rounded-xl">
                    <p class="text-3xl font-bold text-red-600">{{ $behaviorStats['negative'] }}</p>
                    <p class="text-xs text-red-700 mt-1">سلوك سلبي</p>
                </div>
                <div class="p-4 bg-indigo-50 rounded-xl">
                    <p class="text-3xl font-bold text-indigo-600">{{ $behaviorStats['total_points'] }}</p>
                    <p class="text-xs text-indigo-700 mt-1">إجمالي النقاط</p>
                </div>
            </div>

            @if($student->behaviors->count() > 0)
                <h4 class="font-medium text-gray-700 mb-3">آخر سجلات السلوك:</h4>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($student->behaviors->take(5) as $behavior)
                        <div class="flex items-center gap-3 p-2 rounded-lg {{ $behavior->type == 'positive' ? 'bg-green-50' : 'bg-red-50' }}">
                            <span class="{{ $behavior->type == 'positive' ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas {{ $behavior->type == 'positive' ? 'fa-thumbs-up' : 'fa-thumbs-down' }}"></i>
                            </span>
                            <div class="flex-1 text-sm">
                                <span class="font-medium">{{ $behavior->title }}</span>
                                <span class="text-gray-500 text-xs mr-2">({{ $behavior->points > 0 ? '+' : '' }}{{ $behavior->points }} نقطة)</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ $behavior->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-clipboard-check text-4xl mb-2 opacity-50"></i>
                    <p>لا توجد سجلات سلوك</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- نسبة الحضور -->
<div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-chart-pie text-indigo-600 ml-2"></i>
        نسبة الحضور
    </h3>
    @php
        $attendancePercent = $attendanceStats['total'] > 0 
            ? round(($attendanceStats['present'] / $attendanceStats['total']) * 100, 1) 
            : 0;
    @endphp
    <div class="flex items-center gap-6">
        <div class="relative w-32 h-32">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                <path class="text-gray-200"
                    stroke="currentColor"
                    stroke-width="3"
                    fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                <path class="{{ $attendancePercent >= 75 ? 'text-green-500' : ($attendancePercent >= 50 ? 'text-yellow-500' : 'text-red-500') }}"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linecap="round"
                    fill="none"
                    stroke-dasharray="{{ $attendancePercent }}, 100"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
            </svg>
            <span class="absolute inset-0 flex items-center justify-center text-2xl font-bold {{ $attendancePercent >= 75 ? 'text-green-600' : ($attendancePercent >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $attendancePercent }}%
            </span>
        </div>
        <div>
            <p class="text-gray-600 mb-2">
                @if($attendancePercent >= 90)
                    <span class="text-green-600 font-bold">ممتاز! 🌟</span> نسبة حضور مرتفعة جداً
                @elseif($attendancePercent >= 75)
                    <span class="text-green-600 font-bold">جيد 👍</span> نسبة حضور جيدة
                @elseif($attendancePercent >= 50)
                    <span class="text-yellow-600 font-bold">متوسط ⚠️</span> يحتاج لتحسين
                @else
                    <span class="text-red-600 font-bold">ضعيف 🔴</span> نسبة حضور منخفضة
                @endif
            </p>
            <div class="text-sm text-gray-500">
                <p>• حضر {{ $attendanceStats['present'] }} يوم من أصل {{ $attendanceStats['total'] }}</p>
                <p>• غاب {{ $attendanceStats['absent'] }} يوم</p>
                <p>• تأخر {{ $attendanceStats['late'] }} مرة</p>
            </div>
        </div>
    </div>
</div>
@endsection
