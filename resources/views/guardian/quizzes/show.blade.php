@extends('layouts.dashboard')

@section('page-title', $quiz->title)
@section('page-description', __('أداء الاختبار'))

@section('dashboard-content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- الشريط الجانبي للاختبار -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-gray-800 mb-4">{{ $quiz->title }}</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">المادة</span><span class="font-semibold">{{ $quiz->subject->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">عدد الأسئلة</span><span class="font-semibold text-indigo-600">{{ $quiz->questions->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الدرجة الكلية</span><span class="font-semibold">{{ $quiz->total_points }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">الوقت</span><span class="font-semibold">{{ $quiz->duration_minutes }} دقيقة</span></div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">الوقت المتبقي</span>
                    <span id="countdown" class="font-bold text-red-600"></span>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t">
                <div id="progressBar" class="w-full bg-gray-100 rounded-full h-2 mb-2">
                    <div id="progressFill" class="bg-indigo-600 h-2 rounded-full transition-all" style="width:0%"></div>
                </div>
                <p id="answeredCount" class="text-xs text-gray-500 text-center">0 / {{ $quiz->questions->count() }} تمت الإجابة</p>
            </div>
        </div>
    </div>

    <!-- الأسئلة -->
    <div class="lg:col-span-3">
        <form id="quizForm" action="{{ route('parent.quizzes.submit', [$student->id, $quiz->id]) }}" method="POST">
            @csrf
            <div class="space-y-6">
                @foreach($quiz->questions as $question)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 question-card" data-index="{{ $loop->index }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">{{ $loop->iteration }}</span>
                                <span class="px-2 py-1 text-xs bg-gray-50 rounded-full">{{ $question->type_label }}</span>
                            </div>
                            <span class="px-2 py-1 text-xs bg-green-50 text-green-600 rounded-full">{{ $question->points }} درجة</span>
                        </div>
                        
                        <p class="font-semibold text-gray-800 mb-4">{{ $question->question }}</p>
                        
                        <input type="hidden" name="answers[{{ $question->id }}]" id="answer-{{ $question->id }}">
                        
                        @if($question->type == 'multiple_choice' && $question->options)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($question->options as $option)
                                    <label class="option-card flex items-center gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer border-2 border-transparent hover:border-indigo-300 transition" 
                                           onclick="selectOption('{{ $question->id }}', this, '{{ addslashes($option) }}')">
                                        <input type="radio" name="mc_{{ $question->id }}" value="{{ $option }}" class="hidden" onchange="markAnswered('{{ $question->id }}')">
                                        <span class="option-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                            <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full hidden"></span>
                                        </span>
                                        <span class="text-sm text-gray-700">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->type == 'true_false')
                            <div class="grid grid-cols-2 gap-3">
                                <label class="option-card flex items-center gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer border-2 border-transparent hover:border-indigo-300 transition"
                                       onclick="selectOption('{{ $question->id }}', this, 'صح')">
                                    <input type="radio" name="mc_{{ $question->id }}" value="صح" class="hidden" onchange="markAnswered('{{ $question->id }}')">
                                    <span class="option-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                        <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full hidden"></span>
                                    </span>
                                    <span class="text-sm text-gray-700"><i class="fas fa-check-circle text-green-500 ml-1"></i> صح</span>
                                </label>
                                <label class="option-card flex items-center gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer border-2 border-transparent hover:border-indigo-300 transition"
                                       onclick="selectOption('{{ $question->id }}', this, 'خطأ')">
                                    <input type="radio" name="mc_{{ $question->id }}" value="خطأ" class="hidden" onchange="markAnswered('{{ $question->id }}')">
                                    <span class="option-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0">
                                        <span class="w-2.5 h-2.5 bg-indigo-600 rounded-full hidden"></span>
                                    </span>
                                    <span class="text-sm text-gray-700"><i class="fas fa-times-circle text-red-500 ml-1"></i> خطأ</span>
                                </label>
                            </div>
                        @else
                            <textarea class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                                      oninput="document.getElementById('answer-{{ $question->id }}').value=this.value; markAnswered('{{ $question->id }}')"
                                      placeholder="اكتب إجابتك هنا..."></textarea>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 flex items-center justify-between">
                <button type="submit" onclick="return confirmSubmit()" 
                    class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-paper-plane ml-2"></i> تسليم الاختبار
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const totalQuestions = {{ $quiz->questions->count() }};
const durationMinutes = {{ $quiz->duration_minutes }};
const answered = new Set();

function markAnswered(questionId) {
    answered.add(questionId);
    updateProgress();
}

function selectOption(questionId, el, value) {
    const card = el.closest('.option-card');
    card.parentElement.querySelectorAll('.option-card').forEach(c => {
        c.classList.remove('border-indigo-500', 'bg-indigo-50');
        c.querySelector('.option-radio span').classList.add('hidden');
    });
    card.classList.add('border-indigo-500', 'bg-indigo-50');
    card.querySelector('.option-radio span').classList.remove('hidden');
    document.getElementById('answer-' + questionId).value = value;
    markAnswered(questionId);
}

function updateProgress() {
    const count = answered.size;
    document.getElementById('answeredCount').textContent = count + ' / ' + totalQuestions + ' تمت الإجابة';
    document.getElementById('progressFill').style.width = (count / totalQuestions * 100) + '%';
}

function confirmSubmit() {
    const count = answered.size;
    if (count < totalQuestions) {
        return confirm('لديك ' + (totalQuestions - count) + ' سؤال بدون إجابة. هل تريد التسليم الآن؟');
    }
    return confirm('هل أنت متأكد من تسليم الاختبار؟');
}

// العد التنازلي
let remaining = durationMinutes * 60;
function updateCountdown() {
    const min = Math.floor(remaining / 60);
    const sec = remaining % 60;
    document.getElementById('countdown').textContent = min + ':' + (sec < 10 ? '0' : '') + sec;
    if (remaining <= 0) {
        document.getElementById('quizForm').submit();
    } else {
        remaining--;
        setTimeout(updateCountdown, 1000);
    }
}
updateCountdown();
</script>
@endpush
@endsection
