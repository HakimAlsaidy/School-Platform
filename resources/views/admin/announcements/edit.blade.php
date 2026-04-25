@extends('layouts.dashboard')

@section('title', 'تعديل الإعلان')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تعديل الإعلان</h1>
            <p class="text-gray-600 mt-1">تعديل: {{ $announcement->title }}</p>
        </div>
        <a href="{{ route('admin.announcements.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع للقائمة
        </a>
    </div>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الإعلان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('title') border-red-500 @enderror"
                        placeholder="أدخل عنوان الإعلان">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الإعلان <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('content') border-red-500 @enderror"
                        placeholder="اكتب محتوى الإعلان هنا...">{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الفئة المستهدفة <span class="text-red-500">*</span></label>
                        <select name="target" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="all" {{ old('target', $announcement->target) == 'all' ? 'selected' : '' }}>الجميع</option>
                            <option value="teachers" {{ old('target', $announcement->target) == 'teachers' ? 'selected' : '' }}>المعلمون فقط</option>
                            <option value="parents" {{ old('target', $announcement->target) == 'parents' ? 'selected' : '' }}>أولياء الأمور فقط</option>
                            <option value="students" {{ old('target', $announcement->target) == 'students' ? 'selected' : '' }}>الطلاب فقط</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ انتهاء الصلاحية</label>
                        <input type="datetime-local" name="expires_at" 
                            value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <p class="text-gray-500 text-sm mt-1">اتركه فارغاً ليبقى الإعلان نشطاً دائماً</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_pinned" value="1"
                            {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }}
                            class="w-5 h-5 rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-gray-700">
                            <i class="fas fa-thumbtack text-yellow-500 ml-1"></i>
                            تثبيت الإعلان في الأعلى
                        </span>
                    </label>
                </div>
            </div>

            <!-- معلومات الإعلان -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-2">معلومات الإعلان</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">تاريخ الإنشاء:</span>
                        <span class="font-medium text-gray-800">{{ $announcement->created_at->format('Y/m/d H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">آخر تحديث:</span>
                        <span class="font-medium text-gray-800">{{ $announcement->updated_at->format('Y/m/d H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.announcements.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
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
@endsection
