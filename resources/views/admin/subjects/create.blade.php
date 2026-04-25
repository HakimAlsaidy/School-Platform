@extends('layouts.dashboard')

@section('page-title', 'إضافة مادة جديدة')
@section('page-description', 'إضافة مادة دراسية جديدة')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.subjects.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة المواد
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-2xl">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات المادة</h3>
    </div>
    
    <form action="{{ route('admin.subjects.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم المادة <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                    placeholder="مثال: الرياضيات">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">رمز المادة <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('code') border-red-500 @enderror"
                    placeholder="مثال: MATH101">
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">لون المادة</label>
            <div class="flex items-center gap-4">
                <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                    class="w-12 h-12 border border-gray-200 rounded-xl cursor-pointer">
                <span class="text-sm text-gray-500">اختر لوناً مميزاً للمادة</span>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                placeholder="وصف اختياري للمادة...">{{ old('description') }}</textarea>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>حفظ المادة
            </button>
            <a href="{{ route('admin.subjects.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
