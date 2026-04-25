@extends('layouts.dashboard')

@section('page-title', 'إضافة صف جديد')
@section('page-description', 'إضافة صف دراسي جديد')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.grades.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة الصفوف
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-2xl">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات الصف</h3>
    </div>
    
    <form action="{{ route('admin.grades.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">اسم الصف <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                placeholder="مثال: الصف الأول">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب</label>
            <input type="number" name="order" value="{{ old('order', 1) }}" min="1"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-gray-500 mt-1">ترتيب الصف في القائمة</p>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
            <textarea name="description" rows="3"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                placeholder="وصف اختياري للصف...">{{ old('description') }}</textarea>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>حفظ الصف
            </button>
            <a href="{{ route('admin.grades.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
