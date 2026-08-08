@extends('layouts.dashboard')

@section('page-title', 'إنشاء اختبار إلكتروني')
@section('page-description', 'أنشئ اختباراً جديداً لطلابك')

@section('dashboard-content')
<form action="{{ route('teacher.quizzes.store') }}" method="POST" class="space-y-6">
    @csrf
    
    <!-- المعلومات الأساسية -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-cog text-indigo-500"></i> معلومات الاختبار
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الاختبار *</label>
                <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="مثال: اختبار الفصل الأول - الرياضيات">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المادة *</label>
                <select name="subject_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر المادة</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفصل *</label>
                <select name="classroom_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر الفصل</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المدة (دقيقة) *</label>
                <input type="number" name="duration_minutes" min="1" max="300" value="30" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مجموع الدرجات *</label>
                <input type="number" name="total_points" min="1" value="100" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="وصف اختياري"></textarea>
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_published" class="w-4 h-4 text-indigo-600 rounded"> نشر فوراً
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="allow_retake" class="w-4 h-4 text-indigo-600 rounded"> السماح بإعادة المحاولة
                </label>
            </div>
        </div>
    </div>

    <!-- الأسئلة -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list-ol text-indigo-500"></i> الأسئلة
            </h3>
            <button type="button" onclick="addQuestion()" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-plus ml-1"></i> إضافة سؤال
            </button>
        </div>
        <p class="text-xs text-gray-500 mb-4">أضف الأسئلة يدوياً أو استوردها من بنك الأسئلة.</p>
        <div id="questionsContainer" class="space-y-4">
            <!-- سيتم إضافة الأسئلة هنا ديناميكياً -->
        </div>
    </div>

    <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-bold text-lg">
        <i class="fas fa-save ml-2"></i> حفظ الاختبار
    </button>
</form>

<template id="questionTemplate">
    <div class="question-block border border-gray-200 rounded-xl p-4 bg-gray-50">
        <div class="flex items-center justify-between mb-3">
            <span class="question-number font-bold text-indigo-600">سؤال 1</span>
            <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
        </div>
        <div class="space-y-3">
            <select name="questions[INDEX][type]" class="q-type w-full px-4 py-2 border border-gray-200 rounded-xl bg-white text-sm" onchange="toggleQuestionOptions(this)">
                <option value="multiple_choice">اختيار من متعدد</option>
                <option value="true_false">صح أو خطأ</option>
                <option value="short_answer">إجابة قصيرة</option>
                <option value="essay">مقالي</option>
            </select>
            <textarea name="questions[INDEX][question]" required rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="نص السؤال"></textarea>
            <div class="q-options grid grid-cols-2 gap-2">
                <input type="text" name="questions[INDEX][options][]" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm" placeholder="الخيار 1">
                <input type="text" name="questions[INDEX][options][]" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm" placeholder="الخيار 2">
                <input type="text" name="questions[INDEX][options][]" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm" placeholder="الخيار 3">
                <input type="text" name="questions[INDEX][options][]" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm" placeholder="الخيار 4">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="questions[INDEX][correct_answer]" required class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm" placeholder="الإجابة الصحيحة">
                <input type="number" name="questions[INDEX][points]" min="1" value="1" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm" placeholder="الدرجة">
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let questionIndex = 0;
function addQuestion() {
    const container = document.getElementById('questionsContainer');
    const template = document.getElementById('questionTemplate');
    const clone = template.content.cloneNode(true);
    const html = clone.querySelector('.question-block').outerHTML
        .replace(/INDEX/g, questionIndex)
        .replace('سؤال 1', 'سؤال ' + (questionIndex + 1));
    container.insertAdjacentHTML('beforeend', html);
    questionIndex++;
}
function removeQuestion(btn) {
    const block = btn.closest('.question-block');
    block.remove();
    // إعادة ترقيم الأسئلة
    document.querySelectorAll('.question-block').forEach((q, i) => {
        q.querySelector('.question-number').textContent = 'سؤال ' + (i + 1);
    });
}
function toggleQuestionOptions(select) {
    const block = select.closest('.question-block');
    const options = block.querySelector('.q-options');
    options.style.display = select.value === 'multiple_choice' ? 'grid' : 'none';
}
// إضافة سؤال افتراضي عند التحميل
addQuestion();
</script>
@endpush
@endsection
