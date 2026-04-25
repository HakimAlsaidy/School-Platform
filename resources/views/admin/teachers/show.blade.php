@extends('layouts.dashboard')

@section('title', 'تفاصيل المعلم')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تفاصيل المعلم</h1>
            <p class="text-gray-600 mt-1">عرض معلومات: {{ $teacher->user->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <a href="{{ route('admin.teachers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- بطاقة المعلومات الشخصية -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold">
                    {{ mb_substr($teacher->user->name, 0, 1) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-800">{{ $teacher->user->name }}</h2>
                <p class="text-gray-500">{{ $teacher->specialization ?? 'معلم' }}</p>
                
                <div class="mt-4">
                    @if($teacher->user->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            <i class="fas fa-check-circle ml-1"></i>
                            نشط
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                            <i class="fas fa-times-circle ml-1"></i>
                            غير نشط
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-6 border-t pt-4 space-y-3">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-envelope w-8 text-indigo-500"></i>
                    <span class="text-sm">{{ $teacher->user->email }}</span>
                </div>
                @if($teacher->phone)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone w-8 text-indigo-500"></i>
                        <span class="text-sm">{{ $teacher->phone }}</span>
                    </div>
                @endif
                @if($teacher->hire_date)
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-calendar w-8 text-indigo-500"></i>
                        <span class="text-sm">تاريخ التعيين: {{ $teacher->hire_date->format('Y/m/d') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- المعلومات التفصيلية -->
    <div class="lg:col-span-2 space-y-6">
        <!-- المؤهلات -->
        @if($teacher->qualifications)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-graduation-cap text-indigo-500 ml-2"></i>
                    المؤهلات
                </h3>
                <p class="text-gray-600">{{ $teacher->qualifications }}</p>
            </div>
        @endif

        <!-- المواد الدراسية -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-book text-green-500 ml-2"></i>
                المواد الدراسية ({{ $teacher->subjects->count() }})
            </h3>
            @if($teacher->subjects->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($teacher->subjects as $subject)
                        <span class="px-4 py-2 rounded-lg text-white text-sm"
                            style="background-color: {{ $subject->color ?? '#6366f1' }}">
                            {{ $subject->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لم يتم تعيين مواد بعد</p>
            @endif
        </div>

        <!-- الفصول الدراسية -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chalkboard text-purple-500 ml-2"></i>
                الفصول الدراسية ({{ $teacher->classrooms->count() }})
            </h3>
            @if($teacher->classrooms->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($teacher->classrooms as $classroom)
                        <div class="border border-gray-200 rounded-lg p-3 text-center hover:bg-gray-50 transition">
                            <div class="text-lg font-bold text-gray-800">{{ $classroom->name }}</div>
                            <div class="text-sm text-gray-500">{{ $classroom->grade->name }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">لم يتم تعيين فصول بعد</p>
            @endif
        </div>

        <!-- الإحصائيات -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                <div class="text-3xl font-bold">{{ $teacher->scores->count() }}</div>
                <div class="text-sm opacity-90">درجات مسجلة</div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                <div class="text-3xl font-bold">{{ $teacher->assignments->count() }}</div>
                <div class="text-sm opacity-90">واجبات</div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
                <div class="text-3xl font-bold">{{ $teacher->behaviors->count() }}</div>
                <div class="text-sm opacity-90">ملاحظات سلوكية</div>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
                <div class="text-3xl font-bold">{{ $teacher->schedules->count() }}</div>
                <div class="text-sm opacity-90">حصص أسبوعية</div>
            </div>
        </div>
    </div>
</div>
@endsection
