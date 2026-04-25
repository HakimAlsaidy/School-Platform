@extends('layouts.dashboard')

@section('page-title', 'أبنائي')
@section('page-description', 'متابعة المستوى الدراسي لأبنائك')

@section('dashboard-content')
<div class="space-y-6">
    @if($students->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-graduate text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">لا يوجد طلاب مسجلين</h3>
            <p class="text-gray-500">لم يتم ربط أي طالب بحسابك حتى الآن</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($students as $student)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Student Header -->
                    <div class="gradient-bg p-6 text-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=100&background=ffffff&color=6366f1" 
                             alt="{{ $student->name }}" 
                             class="w-20 h-20 rounded-full mx-auto mb-3 border-4 border-white/30">
                        <h3 class="text-lg font-bold text-white">{{ $student->name }}</h3>
                        <p class="text-indigo-200 text-sm">{{ $student->student_id }}</p>
                    </div>
                    
                    <!-- Student Info -->
                    <div class="p-5">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-layer-group text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">المرحلة</p>
                                    <p class="font-semibold text-gray-800">{{ $student->classroom->grade->name ?? '-' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-door-open text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">الفصل</p>
                                    <p class="font-semibold text-gray-800">{{ $student->classroom->name ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Button -->
                        <a href="{{ route('parent.students.show', $student) }}" 
                           class="block w-full py-3 px-4 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition text-center font-medium">
                            <i class="fas fa-eye ml-1"></i> عرض التفاصيل
                        </a>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="border-t bg-gray-50 p-4">
                        <div class="flex justify-around text-center">
                            <a href="{{ route('parent.students.attendance', $student) }}" class="hover:text-green-600 transition">
                                <i class="fas fa-clipboard-check text-green-500 block mb-1"></i>
                                <span class="text-xs text-gray-600">الحضور</span>
                            </a>
                            <a href="{{ route('parent.students.scores', $student) }}" class="hover:text-blue-600 transition">
                                <i class="fas fa-chart-line text-blue-500 block mb-1"></i>
                                <span class="text-xs text-gray-600">الدرجات</span>
                            </a>
                            <a href="{{ route('parent.students.behaviors', $student) }}" class="hover:text-purple-600 transition">
                                <i class="fas fa-award text-purple-500 block mb-1"></i>
                                <span class="text-xs text-gray-600">السلوك</span>
                            </a>
                            <a href="{{ route('parent.students.schedule', $student) }}" class="hover:text-amber-600 transition">
                                <i class="fas fa-calendar-alt text-amber-500 block mb-1"></i>
                                <span class="text-xs text-gray-600">الجدول</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
