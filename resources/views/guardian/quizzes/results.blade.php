@extends('layouts.dashboard')

@section('page-title', 'نتيجة الاختبار')
@section('page-description', $attempt->quiz->title)

@section('dashboard-content')
@php
    $percentage = $attempt->max_score > 0 ? round(($attempt->score / $attempt->max_score) * 100) : 0;
    $passed = $percentage >= 50;
@endphp

<!-- ملخص النتيجة -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-center">
        <div class="text-center">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-3 {{ $passed ? 'bg-green-100' : 'bg-red-100' }}">
                <span class="text-3xl font-bold {{ $passed ? 'text-green-600' : 'text-red-600' }}">{{ $percentage }}%</span>
            </div>
            <p class="font-bold {{ $passed ? 'text-green-600' : 'text-red-600' }}">{{ $passed ? 'ناجح' : 'غير ناجح' }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-star text-indigo-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">الدرجة</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $attempt->score }} / {{ $attempt->max_score }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-amber-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">تاريخ الأداء</p>
                <p class="text-2xl font-bold text-gray-800">{{ $attempt->submitted_at->format('Y/m/d') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- تفاصيل الإجابات -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">مراجعة الإجابات</h3>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($attempt->quiz->questions as $question)
            @php
                $answer = $attempt->answers[$question->id] ?? null;
                $isCorrect = $answer['correct'] ?? false;
            @endphp
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-8 h-8 {{ $isCorrect ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full flex items-center justify-center">
                                <i class="fas {{ $isCorrect ? 'fa-check' : ($answer ? 'fa-times' : 'fa-minus') }}"></i>
                            </span>
                            <span class="px-2 py-1 text-xs bg-gray-50 rounded-full">{{ $question->type_label }}</span>
                            <span class="px-2 py-1 text-xs bg-green-50 text-green-600 rounded-full">{{ $question->points }} درجة</span>
                        </div>
                        <p class="font-semibold text-gray-800 mb-3">{{ $question->question }}</p>
                        
                        <div class="space-y-2 text-sm">
                            @if($question->type == 'essay')
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-gray-500 mb-1">إجابتك:</p>
                                    <p class="text-gray-800">{{ $answer['answer'] ?? 'لم يقدم إجابة' }}</p>
                                </div>
                                <p class="text-xs text-amber-600"><i class="fas fa-info-circle ml-1"></i> هذا السؤال يصحح يدوياً من قبل المعلم</p>
                            @else
                                @if(!empty($question->options))
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($question->options as $option)
                                            <div class="p-2 rounded-lg 
                                                @if($option == $question->correct_answer) bg-green-50 border border-green-300 text-green-700
                                                @elseif($option == ($answer['answer'] ?? '')) bg-red-50 border border-red-300 text-red-600
                                                @else bg-gray-50 text-gray-600 @endif">
                                                {{ $option }}
                                                @if($option == $question->correct_answer)<i class="fas fa-check ml-1"></i>@endif
                                                @if($option == ($answer['answer'] ?? '') && $option != $question->correct_answer)<i class="fas fa-times ml-1"></i>@endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="p-3 {{ $isCorrect ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }} rounded-lg">
                                            <p class="text-xs text-gray-500 mb-1">إجابتك</p>
                                            {{ $answer['answer'] ?? 'لم يقدم إجابة' }}
                                        </div>
                                        <div class="p-3 bg-green-50 text-green-700 rounded-lg">
                                            <p class="text-xs text-gray-500 mb-1">الإجابة الصحيحة</p>
                                            {{ $question->correct_answer }}
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <span class="font-bold {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                        @if($isCorrect)+@endif {{ $isCorrect ? $question->points : 0 }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('parent.quizzes.index', $student->id) }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
        <i class="fas fa-arrow-right ml-2"></i> العودة للاختبارات
    </a>
</div>
@endsection
