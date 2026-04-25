@extends('layouts.dashboard')

@section('page-title', 'إضافة إعلان جديد')
@section('page-description', 'نشر إعلان للمستخدمين')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.announcements.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة الإعلانات
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات الإعلان</h3>
    </div>
    
    <form action="{{ route('admin.announcements.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الإعلان <span class="text-red-500">*</span></label>
            <div class="voice-input-wrapper">
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                    placeholder="أدخل عنوان الإعلان">
                <x-voice-input target="title" />
            </div>
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الإعلان <span class="text-red-500">*</span></label>
            <div class="voice-input-wrapper items-start">
                <textarea id="announcement_content" name="content" rows="6" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('content') border-red-500 @enderror"
                    placeholder="اكتب محتوى الإعلان هنا...">{{ old('content') }}</textarea>
                <x-voice-input target="announcement_content" />
            </div>
            @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفئة المستهدفة <span class="text-red-500">*</span></label>
                <select name="target" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="all" {{ old('target') == 'all' ? 'selected' : '' }}>الجميع</option>
                    <option value="teachers" {{ old('target') == 'teachers' ? 'selected' : '' }}>المعلمين فقط</option>
                    <option value="parents" {{ old('target') == 'parents' ? 'selected' : '' }}>أولياء الأمور فقط</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ انتهاء الإعلان</label>
                <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">اتركه فارغاً إذا كان الإعلان دائماً</p>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }}
                    class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                <span class="text-gray-700">تثبيت الإعلان في الأعلى</span>
            </label>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-bullhorn ml-2"></i>نشر الإعلان
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
