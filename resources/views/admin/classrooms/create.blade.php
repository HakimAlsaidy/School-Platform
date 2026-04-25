@extends('layouts.dashboard')

@section('page-title', 'إضافة فصل جديد')
@section('page-description', 'إضافة فصل دراسي جديد')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.classrooms.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة الفصول
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-2xl">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات الفصل</h3>
    </div>
    
    <form action="{{ route('admin.classrooms.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم الفصل <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                    placeholder="مثال: أ">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الصف <span class="text-red-500">*</span></label>
                <select name="grade_id" id="grade_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('grade_id') border-red-500 @enderror">
                    <option value="">-- اختر الصف --</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                    @endforeach
                </select>
                @error('grade_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                {{-- معلومات الصف --}}
                <div id="grade-info" class="hidden mt-3 p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users text-indigo-600"></i>
                            <span class="text-sm text-gray-600">عدد الطلاب:</span>
                            <span id="students-count" class="font-bold text-indigo-600">0</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-door-open text-indigo-600"></i>
                            <span class="text-sm text-gray-600">عدد الفصول:</span>
                            <span id="classrooms-count" class="font-bold text-indigo-600">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">السعة <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', 30) }}" required min="1"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('capacity') border-red-500 @enderror">
                @error('capacity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المعلم المشرف</label>
                <select name="teacher_id"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- بدون مشرف --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>حفظ الفصل
            </button>
            <a href="{{ route('admin.classrooms.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gradeSelect = document.getElementById('grade_id');
    const gradeInfo = document.getElementById('grade-info');
    const studentsCount = document.getElementById('students-count');
    const classroomsCount = document.getElementById('classrooms-count');
    
    gradeSelect.addEventListener('change', async function() {
        const gradeId = this.value;
        
        if (!gradeId) {
            gradeInfo.classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(`/admin/classrooms/grade/${gradeId}/students-count`);
            const data = await response.json();
            
            studentsCount.textContent = data.students_count;
            classroomsCount.textContent = data.classrooms_count;
            gradeInfo.classList.remove('hidden');
        } catch (error) {
            console.error('Error fetching grade info:', error);
        }
    });
    
    // تشغيل عند التحميل إذا كان هناك قيمة محددة
    if (gradeSelect.value) {
        gradeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
