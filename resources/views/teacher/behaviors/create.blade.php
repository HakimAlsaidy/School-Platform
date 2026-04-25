@extends('layouts.dashboard')

@section('page-title', 'تسجيل سلوك جديد')
@section('page-description', 'إضافة سلوك للطالب')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('teacher.behaviors.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لسجل السلوك
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات السلوك</h3>
    </div>
    
    <form action="{{ route('teacher.behaviors.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفصل <span class="text-red-500">*</span></label>
                <select name="classroom_id" id="classroom_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                    onchange="loadStudents()">
                    <option value="">-- اختر الفصل --</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                            {{ $classroom->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الطالب <span class="text-red-500">*</span></label>
                <select name="student_id" id="student_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- اختر الطالب --</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع السلوك <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="positive" class="peer hidden" {{ old('type', 'positive') == 'positive' ? 'checked' : '' }}>
                        <div class="p-4 border-2 rounded-xl text-center transition
                            peer-checked:border-green-500 peer-checked:bg-green-50
                            hover:border-green-300">
                            <i class="fas fa-thumbs-up text-2xl text-green-600 mb-2"></i>
                            <p class="font-semibold text-green-700">إيجابي</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="negative" class="peer hidden" {{ old('type') == 'negative' ? 'checked' : '' }}>
                        <div class="p-4 border-2 rounded-xl text-center transition
                            peer-checked:border-red-500 peer-checked:bg-red-50
                            hover:border-red-300">
                            <i class="fas fa-thumbs-down text-2xl text-red-600 mb-2"></i>
                            <p class="font-semibold text-red-700">سلبي</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">النقاط <span class="text-red-500">*</span></label>
                <input type="number" name="points" value="{{ old('points', 1) }}" min="1" max="100" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">سيتم إضافة أو خصم النقاط حسب نوع السلوك</p>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">عنوان السلوك <span class="text-red-500">*</span></label>
            <div class="voice-input-wrapper">
                <input type="text" id="behavior_title" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                    placeholder="مثال: مشاركة فعالة في الحصة">
                <x-voice-input target="behavior_title" />
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">وصف السلوك</label>
            <div class="voice-input-wrapper items-start">
                <textarea id="behavior_description" name="description" rows="3"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                    placeholder="وصف تفصيلي للسلوك...">{{ old('description') }}</textarea>
                <x-voice-input target="behavior_description" />
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المادة (اختياري)</label>
                <select name="subject_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- بدون مادة --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>حفظ السلوك
            </button>
            <a href="{{ route('teacher.behaviors.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const students = @json($students ?? []);
    
    function loadStudents() {
        const classroomId = document.getElementById('classroom_id').value;
        const studentSelect = document.getElementById('student_id');
        
        studentSelect.innerHTML = '<option value="">-- اختر الطالب --</option>';
        
        if (classroomId && students[classroomId]) {
            students[classroomId].forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.name;
                studentSelect.appendChild(option);
            });
        }
    }
    
    // Load students on page load if classroom is selected
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('classroom_id').value) {
            loadStudents();
        }
    });
</script>
@endpush
@endsection
