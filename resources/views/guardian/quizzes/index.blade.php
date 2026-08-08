@extends('layouts.dashboard')

@section('page-title', 'الاختبارات الإلكترونية')
@section('page-description', 'الاختبارات المتاحة للطالب')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="text-lg font-bold text-gray-800">الاختبارات المتاحة لـ {{ $student->name }}</h3>
        <p class="text-sm text-gray-500">{{ $student->classroom->full_name }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($quizzes as $quiz)
        @php
            $attempt = $attempts->get($quiz->id);
            $completed = $attempt && $attempt->status == 'submitted';
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white">{{ $quiz->title }}</h3>
                        <p class="text-xs text-white/80">{{ $quiz->subject->name }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 gap-3 text-center mb-4">
                    <div class="p-2 bg-indigo-50 rounded-xl">
                        <p class="font-bold text-indigo-600">{{ $quiz->questions->count() }}</p>
                        <p class="text-xs text-indigo-700">سؤال</p>
                    </div>
                    <div class="p-2 bg-amber-50 rounded-xl">
                        <p class="font-bold text-amber-600">{{ $quiz->duration_minutes }}</p>
                        <p class="text-xs text-amber-700">دقيقة</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    @if($completed && !$quiz->allow_retake)
                        <span class="px-4 py-2 text-sm bg-green-50 text-green-600 rounded-lg">
                            <i class="fas fa-check ml-1"></i> تم الأداء
                        </span>
                        <a href="{{ route('parent.quizzes.results', [$student->id, $attempt->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                            عرض النتيجة <i class="fas fa-arrow-left mr-1"></i>
                        </a>
                    @else
                        <a href="{{ route('parent.quizzes.show', [$student->id, $quiz->id]) }}" class="px-5 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-play ml-1"></i> بدء الاختبار
                        </a>
                        @if($completed)
                            <span class="text-xs text-green-600"><i class="fas fa-check ml-1"></i> إعادة</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">لا توجد اختبارات متاحة حالياً</p>
        </div>
    @endforelse
</div>
@endsection
