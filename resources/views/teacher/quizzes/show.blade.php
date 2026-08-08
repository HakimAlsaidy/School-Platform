@extends('layouts.dashboard')

@section('page-title', $quiz->title)
@section('page-description', __('تفاصيل الاختبار'))

@section('dashboard-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- التفاصيل -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">معلومات الاختبار</h3>
                <span class="px-2 py-1 text-xs rounded-full {{ $quiz->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $quiz->is_published ? 'منشور' : 'مسودة' }}
                </span>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">المادة</span><span class="font-semibold">{{ $quiz->subject->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الفصل</span><span class="font-semibold">{{ $quiz->classroom->full_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">عدد الأسئلة</span><span class="font-semibold text-indigo-600">{{ $quiz->questions->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المدة</span><span class="font-semibold">{{ $quiz->duration_minutes }} دقيقة</span></div>
                <div class="flex justify-between"><span class="text-gray-500">مجموع الدرجات</span><span class="font-semibold">{{ $quiz->total_points }}</span></div>
                @if($quiz->description)
                    <p class="pt-3 border-t text-gray-600">{{ $quiz->description }}</p>
                @endif
            </div>
            <div class="mt-4 pt-4 border-t flex items-center gap-2">
                <form action="{{ route('teacher.quizzes.toggle', $quiz) }}" method="POST" class="flex-1">
                    @csrf
                    <button class="w-full px-4 py-2 text-sm rounded-lg {{ $quiz->is_published ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-600 text-white hover:bg-green-700' }}">
                        <i class="fas {{ $quiz->is_published ? 'fa-eye-slash' : 'fa-eye' }} ml-1"></i>
                        {{ $quiz->is_published ? 'إلغاء النشر' : 'نشر الاختبار' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- استيراد من البنك -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-3">استيراد من بنك الأسئلة</h3>
            <form action="{{ route('teacher.quizzes.import', $quiz) }}" method="POST" class="space-y-3">
                @csrf
                <select name="question_ids[]" multiple class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm" style="min-height:120px">
                    @foreach($bankQuestions as $q)
                        <option value="{{ $q->id }}">{{ Str::limit($q->question, 50) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">استيراد المحدد</button>
            </form>
        </div>
    </div>

    <!-- الأسئلة -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">أسئلة الاختبار</h3>
                <span class="text-sm text-gray-500">{{ $quiz->questions->count() }} سؤال</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($quiz->questions as $question)
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">{{ $loop->iteration }}</span>
                                    <span class="px-2 py-1 text-xs bg-gray-50 rounded-full">{{ $question->type_label }}</span>
                                    <span class="px-2 py-1 text-xs bg-green-50 text-green-600 rounded-full">{{ $question->points }} درجة</span>
                                </div>
                                <p class="font-semibold text-gray-800 mb-2">{{ $question->question }}</p>
                                @if($question->options)
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($question->options as $option)
                                            <div class="p-2 bg-gray-50 rounded-lg text-sm {{ $option == $question->correct_answer ? 'border border-green-300 bg-green-50 text-green-700' : '' }}">
                                                {{ $option }} @if($option == $question->correct_answer)<i class="fas fa-check ml-1"></i>@endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-green-600"><i class="fas fa-check ml-1"></i>{{ $question->correct_answer }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        <i class="fas fa-question-circle text-5xl text-gray-300 mb-3 block"></i>
                        لا توجد أسئلة بعد
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
