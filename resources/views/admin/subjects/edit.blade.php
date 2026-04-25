@extends('layouts.dashboard')

@section('title', 'تعديل المادة')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تعديل المادة الدراسية</h1>
            <p class="text-gray-600 mt-1">تعديل بيانات: {{ $subject->name }}</p>
        </div>
        <a href="{{ route('admin.subjects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع للقائمة
        </a>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم المادة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $subject->name) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="مثال: الرياضيات">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رمز المادة <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $subject->code) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('code') border-red-500 @enderror"
                        placeholder="مثال: MATH101">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اللون</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $subject->color ?? '#6366f1') }}"
                            class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                        <span class="text-gray-500 text-sm">اختر لوناً مميزاً للمادة</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="وصف اختياري للمادة...">{{ old('description', $subject->description) }}</textarea>
                </div>
            </div>

            <!-- معاينة -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-2">معاينة</h4>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-lg text-white text-sm font-medium" 
                        id="preview-badge"
                        style="background-color: {{ $subject->color ?? '#6366f1' }}">
                        {{ $subject->name }}
                    </span>
                    <span class="text-gray-500 text-sm">({{ $subject->code }})</span>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.subjects.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // تحديث المعاينة عند تغيير اللون أو الاسم
    document.querySelector('input[name="color"]').addEventListener('input', function(e) {
        document.getElementById('preview-badge').style.backgroundColor = e.target.value;
    });
    
    document.querySelector('input[name="name"]').addEventListener('input', function(e) {
        document.getElementById('preview-badge').textContent = e.target.value || 'اسم المادة';
    });
</script>
@endpush
@endsection
