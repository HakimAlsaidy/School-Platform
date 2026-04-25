@extends('layouts.dashboard')

@section('page-title', 'الجدول الدراسي')
@section('page-description', 'إدارة جداول الفصول والمعلمين')

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    {{-- العنوان --}}
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <i class="fas fa-calendar-alt text-4xl text-white"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">الجدول الدراسي</h1>
        <p class="text-gray-500">اختر نوع الجدول الذي تريد عرضه أو تعديله</p>
    </div>
    
    {{-- القالبين --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- جداول الفصول --}}
        <a href="{{ route('admin.schedules.classrooms') }}" 
           class="group bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-indigo-300 hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-indigo-100 group-hover:bg-indigo-200 rounded-2xl flex items-center justify-center mb-6 transition">
                    <i class="fas fa-door-open text-4xl text-indigo-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition">جداول الفصول</h2>
                <p class="text-gray-500 mb-4">إدارة الجدول الدراسي لكل فصل</p>
                <div class="flex items-center gap-4 text-sm">
                    <div class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full">
                        <i class="fas fa-building ml-1"></i>
                        {{ $classroomsCount }} فصل
                    </div>
                    <div class="px-3 py-1 bg-green-50 text-green-600 rounded-full">
                        <i class="fas fa-calendar-check ml-1"></i>
                        {{ $schedulesCount }} حصة
                    </div>
                </div>
                <div class="mt-6 text-indigo-600 opacity-0 group-hover:opacity-100 transition">
                    <i class="fas fa-arrow-left ml-2"></i>
                    عرض جداول الفصول
                </div>
            </div>
        </a>
        
        {{-- جداول المعلمين --}}
        <a href="{{ route('admin.schedules.teachers') }}" 
           class="group bg-white rounded-2xl p-8 border-2 border-gray-100 hover:border-emerald-300 hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center text-center">
                <div class="w-24 h-24 bg-emerald-100 group-hover:bg-emerald-200 rounded-2xl flex items-center justify-center mb-6 transition">
                    <i class="fas fa-chalkboard-teacher text-4xl text-emerald-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-emerald-600 transition">جداول المعلمين</h2>
                <p class="text-gray-500 mb-4">عرض جدول كل معلم</p>
                <div class="flex items-center gap-4 text-sm">
                    <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full">
                        <i class="fas fa-user-tie ml-1"></i>
                        {{ $teachersCount }} معلم
                    </div>
                </div>
                <div class="mt-6 text-emerald-600 opacity-0 group-hover:opacity-100 transition">
                    <i class="fas fa-arrow-left ml-2"></i>
                    عرض جداول المعلمين
                </div>
            </div>
        </a>
    </div>
    
    {{-- معلومات سريعة --}}
    <div class="mt-10 bg-gradient-to-l from-gray-50 to-white rounded-2xl p-6 border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">
            <i class="fas fa-lightbulb text-amber-500 ml-2"></i>
            نصائح سريعة
        </h3>
        <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <span><strong>جداول الفصول:</strong> لإضافة وتعديل الحصص لكل فصل دراسي</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <span><strong>جداول المعلمين:</strong> لعرض جدول كل معلم ومعرفة حصصه الأسبوعية</span>
            </li>
            <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <span><strong>نسخ الجدول:</strong> يمكنك نسخ جدول فصل كامل لفصل آخر لتوفير الوقت</span>
            </li>
        </ul>
    </div>
</div>
@endsection
