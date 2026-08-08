@extends('layouts.dashboard')

@section('page-title', 'بنك الأسئلة')
@section('page-description', 'إدارة الأسئلة والاختبارات')

@section('dashboard-content')
<button onclick="openQuestionModal()" class="mb-6 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
    <i class="fas fa-plus"></i> إضافة سؤال
</button>

<!-- فلاتر -->
<form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <select name="subject_id" class="px-4 py-3 border border-gray-200 rounded-xl bg-white" onchange="this.form.submit()">
        <option value="">كل المواد</option>
        @foreach($subjects as $subject)
            <option value="{{ $subject->id }}" @if(request('subject_id')==$subject->id) selected @endif>{{ $subject->name }}</option>
        @endforeach
    </select>
    <select name="grade_id" class="px-4 py-3 border border-gray-200 rounded-xl bg-white" onchange="this.form.submit()">
        <option value="">كل الصفوف</option>
        @foreach($grades as $grade)
            <option value="{{ $grade->id }}" @if(request('grade_id')==$grade->id) selected @endif>{{ $grade->name }}</option>
        @endforeach
    </select>
    <select name="type" class="px-4 py-3 border border-gray-200 rounded-xl bg-white" onchange="this.form.submit()">
        <option value="">كل الأنواع</option>
        @foreach(\App\Models\QuestionBank::$typeLabels as $key=>$label)
            <option value="{{ $key }}" @if(request('type')==$key) selected @endif>{{ $label }}</option>
        @endforeach
    </select>
    <div></div>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-100">
        @forelse($questions as $question)
            <div class="p-5 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 mb-1">{{ $question->question }}</p>
                        <div class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full">{{ $question->subject->name }}</span>
                            <span class="px-2 py-1 bg-gray-50 rounded-full">{{ $question->type_label }}</span>
                            <span class="px-2 py-1 bg-amber-50 text-amber-600 rounded-full">{{ $question->difficulty_label }}</span>
                            <span class="px-2 py-1 bg-green-50 text-green-600 rounded-full">{{ $question->points }} نقطة</span>
                        </div>
                        @if($question->options)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                @foreach($question->options as $option)
                                    <div class="p-2 bg-gray-50 rounded-lg text-sm {{ $option == $question->correct_answer ? 'border border-green-300 bg-green-50 text-green-700' : 'text-gray-600' }}">
                                        {{ $option }}
                                        @if($option == $question->correct_answer)<i class="fas fa-check ml-1"></i>@endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-sm text-green-600"><i class="fas fa-check ml-1"></i>الإجابة: {{ $question->correct_answer }}</p>
                        @endif
                    </div>
                    <form action="{{ route('teacher.question-bank.destroy', $question) }}" method="POST" onsubmit="return confirm('حذف السؤال؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-question-circle text-5xl text-gray-300 mb-3 block"></i>
                لا توجد أسئلة في البنك
            </div>
        @endforelse
    </div>
    <div class="p-4 border-t border-gray-100">{{ $questions->links() }}</div>
</div>

<!-- Modal إضافة سؤال -->
<div id="questionModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeQuestionModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4">إضافة سؤال</h3>
            <form action="{{ route('teacher.question-bank.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <select name="subject_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="">المادة *</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    <select name="grade_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="">الصف (اختياري)</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>
                <select name="type" id="qType" class="w-full px-4 py-3 border border-gray-200 rounded-xl" onchange="toggleOptions()">
                    <option value="multiple_choice">اختيار من متعدد</option>
                    <option value="true_false">صح أو خطأ</option>
                    <option value="short_answer">إجابة قصيرة</option>
                    <option value="essay">مقالي</option>
                </select>
                <textarea name="question" required rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="نص السؤال"></textarea>
                <div id="optionsContainer">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخيارات</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="options[]" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الخيار 1">
                        <input type="text" name="options[]" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الخيار 2">
                        <input type="text" name="options[]" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الخيار 3">
                        <input type="text" name="options[]" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الخيار 4">
                    </div>
                </div>
                <input type="text" name="correct_answer" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الإجابة الصحيحة">
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="points" min="1" max="100" value="1" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <select name="difficulty" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="easy">سهل</option>
                        <option value="medium" selected>متوسط</option>
                        <option value="hard">صعب</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">حفظ السؤال</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openQuestionModal(){document.getElementById('questionModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeQuestionModal(){document.getElementById('questionModal').classList.add('hidden');document.body.style.overflow='';}
function toggleOptions(){const t=document.getElementById('qType').value;document.getElementById('optionsContainer').style.display=t==='multiple_choice'?'block':'none';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeQuestionModal();});
</script>
@endpush
@endsection
