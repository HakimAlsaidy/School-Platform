@extends('layouts.superadmin')

@section('title', 'تعديل ' . $school->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('superadmin.schools.index') }}" 
           class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تعديل المدرسة</h1>
            <p class="text-gray-600 mt-1">{{ $school->name }}</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('superadmin.schools.update', $school) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- معلومات المدرسة الأساسية -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">
                <i class="fas fa-info-circle ml-2 text-indigo-600"></i>
                معلومات المدرسة الأساسية
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم المدرسة <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">النطاق الفرعي</label>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">.{{ config('app.domain', 'schoolpla.test') }}</span>
                        <input type="text" value="{{ $school->subdomain }}" 
                               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 font-mono"
                               disabled>
                    </div>
                    <p class="text-gray-500 text-sm mt-1">لا يمكن تغيير النطاق الفرعي</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">شعار المدرسة</label>
                    <div class="flex items-center gap-4">
                        @if($school->logo)
                            <img src="{{ $school->logo_url }}" alt="" class="w-16 h-16 rounded-xl object-cover">
                        @endif
                        <input type="file" name="logo" accept="image/*"
                               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('logo') border-red-500 @enderror">
                    </div>
                    @error('logo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع المدرسة <span class="text-red-500">*</span></label>
                    <select name="type" 
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('type') border-red-500 @enderror"
                            required>
                        <option value="public" {{ old('type', $school->type) == 'public' ? 'selected' : '' }}>حكومية</option>
                        <option value="private" {{ old('type', $school->type) == 'private' ? 'selected' : '' }}>أهلية</option>
                        <option value="international" {{ old('type', $school->type) == 'international' ? 'selected' : '' }}>عالمية</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المرحلة التعليمية <span class="text-red-500">*</span></label>
                    <select name="level" 
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('level') border-red-500 @enderror"
                            required>
                        <option value="elementary" {{ old('level', $school->level) == 'elementary' ? 'selected' : '' }}>ابتدائي</option>
                        <option value="middle" {{ old('level', $school->level) == 'middle' ? 'selected' : '' }}>متوسط</option>
                        <option value="high" {{ old('level', $school->level) == 'high' ? 'selected' : '' }}>ثانوي</option>
                        <option value="all" {{ old('level', $school->level) == 'all' ? 'selected' : '' }}>جميع المراحل</option>
                    </select>
                    @error('level')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- معلومات التواصل -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">
                <i class="fas fa-phone ml-2 text-indigo-600"></i>
                معلومات التواصل
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone) }}" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('phone') border-red-500 @enderror"
                           required
                           dir="ltr">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $school->email) }}" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('email') border-red-500 @enderror"
                           dir="ltr">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                    <textarea name="address" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none resize-none @error('address') border-red-500 @enderror">{{ old('address', $school->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- إعدادات الاشتراك -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">
                <i class="fas fa-cog ml-2 text-indigo-600"></i>
                إعدادات الاشتراك
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للطلاب</label>
                    <input type="number" name="max_students" value="{{ old('max_students', $school->max_students) }}" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('max_students') border-red-500 @enderror"
                           placeholder="اتركه فارغاً لعدد غير محدود"
                           min="1">
                    @error('max_students')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للمعلمين</label>
                    <input type="number" name="max_teachers" value="{{ old('max_teachers', $school->max_teachers) }}" 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none @error('max_teachers') border-red-500 @enderror"
                           placeholder="اتركه فارغاً لعدد غير محدود"
                           min="1">
                    @error('max_teachers')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $school->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-gray-700">المدرسة مفعّلة</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $school->is_verified) ? 'checked' : '' }}
                               class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-gray-700">المدرسة موثّقة</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <form action="{{ route('superadmin.schools.destroy', $school) }}" method="POST" 
                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المدرسة؟ هذا الإجراء لا يمكن التراجع عنه.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2.5 border border-red-200 text-red-600 rounded-xl hover:bg-red-50 transition">
                    <i class="fas fa-trash-alt ml-2"></i>
                    حذف المدرسة
                </button>
            </form>

            <div class="flex items-center gap-4">
                <a href="{{ route('superadmin.schools.index') }}" 
                   class="px-6 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-8 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium hover:opacity-90 transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
