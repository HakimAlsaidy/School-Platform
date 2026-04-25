@extends('layouts.dashboard')

@section('page-title', 'إدارة الطلاب')
@section('page-description', 'عرض وإدارة جميع الطلاب حسب الصفوف')

@section('dashboard-content')
<div class="space-y-6">
    {{-- الإحصائيات --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</div>
                    <div class="text-sm text-gray-500">إجمالي الطلاب</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-male text-blue-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $maleStudents }}</div>
                    <div class="text-sm text-gray-500">طلاب (ذكور)</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-female text-pink-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $femaleStudents }}</div>
                    <div class="text-sm text-gray-500">طالبات (إناث)</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-amber-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $studentsWithoutClassroom }}</div>
                    <div class="text-sm text-gray-500">بدون فصل</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- معلومات وسلة المحذوفات --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.students.trash') }}" class="text-red-600 hover:text-red-800 text-sm">
            <i class="fas fa-trash ml-1"></i>
            سلة المحذوفات
        </a>
        <div class="text-sm text-gray-500 bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-200">
            <i class="fas fa-info-circle text-indigo-600 ml-1"></i>
            اضغط على الصف لعرض الطلاب وإضافة طلاب جدد
        </div>
    </div>
    
    {{-- قوالب الصفوف --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($grades as $grade)
            @php
                $colors = ['indigo', 'emerald', 'blue', 'purple', 'pink', 'teal', 'amber', 'rose', 'cyan'];
                $color = $colors[$loop->index % count($colors)];
            @endphp
            <a href="{{ route('admin.students.grade', $grade) }}" 
               class="block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-indigo-200 transition-all group">
                <div class="bg-gradient-to-l from-indigo-500 to-indigo-600 p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold">{{ $grade->name }}</h3>
                            <p class="text-indigo-100 text-sm mt-1">{{ $grade->classrooms_count }} فصل</p>
                        </div>
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-gray-600">عدد الطلاب</span>
                        <span class="text-2xl font-bold text-indigo-600">{{ $grade->students_count }}</span>
                    </div>
                    
                    {{-- الفصول --}}
                    @if($grade->classrooms->count() > 0)
                        <div class="space-y-2">
                            @foreach($grade->classrooms->take(3) as $classroom)
                                <div class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2">
                                    <span class="text-gray-700">{{ $classroom->name }}</span>
                                    <span class="text-indigo-600 font-medium">{{ $classroom->students_count }} طالب</span>
                                </div>
                            @endforeach
                            @if($grade->classrooms->count() > 3)
                                <div class="text-xs text-gray-400 text-center pt-1">
                                    +{{ $grade->classrooms->count() - 3 }} فصول أخرى
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center text-gray-400 text-sm py-4">
                            <i class="fas fa-door-closed mb-1"></i>
                            <p>لا توجد فصول</p>
                        </div>
                    @endif
                </div>
                <div class="px-4 pb-4">
                    <div class="py-2 px-4 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-medium group-hover:bg-indigo-200 transition flex items-center justify-center gap-2">
                        <i class="fas fa-users"></i>
                        عرض وإدارة الطلاب
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-gray-100">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">لا توجد صفوف دراسية</h3>
                <p class="text-gray-400 mb-4">قم بإضافة الصفوف الدراسية أولاً</p>
                <a href="{{ route('admin.grades.index') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition inline-flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة صف دراسي
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
