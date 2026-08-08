@extends('layouts.dashboard')

@section('page-title', 'الاختبارات الإلكترونية')
@section('page-description', 'إدارة الاختبارات والتصحيح الآلي')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('teacher.quizzes.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i> إنشاء اختبار
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($quizzes as $quiz)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white">{{ $quiz->title }}</h3>
                            <p class="text-xs text-white/80">{{ $quiz->subject->name }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $quiz->is_published ? 'bg-green-400 text-white' : 'bg-gray-500 text-white' }}">
                        {{ $quiz->is_published ? 'منشور' : 'مسودة' }}
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-3 gap-3 text-center mb-4">
                    <div class="p-2 bg-indigo-50 rounded-xl">
                        <p class="font-bold text-indigo-600 text-lg">{{ $quiz->questions->count() }}</p>
                        <p class="text-xs text-indigo-700">سؤال</p>
                    </div>
                    <div class="p-2 bg-amber-50 rounded-xl">
                        <p class="font-bold text-amber-600 text-lg">{{ $quiz->duration_minutes }}</p>
                        <p class="text-xs text-amber-700">دقيقة</p>
                    </div>
                    <div class="p-2 bg-green-50 rounded-xl">
                        <p class="font-bold text-green-600 text-lg">{{ $quiz->total_points }}</p>
                        <p class="text-xs text-green-700">نقطة</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        <i class="fas fa-eye ml-1"></i> عرض التفاصيل
                    </a>
                    <form action="{{ route('teacher.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('حذف الاختبار؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد اختبارات</h3>
            <a href="{{ route('teacher.quizzes.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus ml-2"></i> إنشاء اختبار
            </a>
        </div>
    @endforelse
</div>
@endsection
