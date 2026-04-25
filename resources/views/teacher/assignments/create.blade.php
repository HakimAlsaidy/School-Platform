@extends('layouts.dashboard')

@section('page-title', 'إنشاء واجب جديد')
@section('page-description', 'إضافة واجب للطلاب')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('teacher.assignments.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة الواجبات
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات الواجب</h3>
    </div>
    
    <form action="{{ route('teacher.assignments.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الواجب <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                    placeholder="أدخل عنوان الواجب">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ التسليم <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('due_date') border-red-500 @enderror">
                @error('due_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفصل <span class="text-red-500">*</span></label>
                <select name="classroom_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('classroom_id') border-red-500 @enderror">
                    <option value="">-- اختر الفصل --</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                            {{ $classroom->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('classroom_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المادة <span class="text-red-500">*</span></label>
                <select name="subject_id" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('subject_id') border-red-500 @enderror">
                    <option value="">-- اختر المادة --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">وصف الواجب <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                placeholder="اكتب تفاصيل الواجب هنا...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الدرجة الكاملة</label>
                <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="1"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">مرفق (اختياري)</label>
                <input type="file" name="attachment"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">PDF, Word, صورة - الحد الأقصى 10 ميجا</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>إنشاء الواجب
            </button>
            <a href="{{ route('teacher.assignments.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
