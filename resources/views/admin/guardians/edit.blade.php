@extends('layouts.dashboard')

@section('title', 'تعديل بيانات ولي الأمر')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تعديل بيانات ولي الأمر</h1>
            <p class="text-gray-600 mt-1">تعديل بيانات: {{ $guardian->user->name }}</p>
        </div>
        <a href="{{ route('admin.guardians.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع للقائمة
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <form action="{{ route('admin.guardians.update', $guardian) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- البيانات الشخصية -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">
                <i class="fas fa-user text-indigo-500 ml-2"></i>
                البيانات الشخصية
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $guardian->user->name) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone', $guardian->user->phone) }}" required placeholder="05xxxxxxxx"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">صلة القرابة</label>
                    <select name="relationship" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">اختر صلة القرابة</option>
                        <option value="father" {{ old('relationship', $guardian->relationship) == 'father' ? 'selected' : '' }}>أب</option>
                        <option value="mother" {{ old('relationship', $guardian->relationship) == 'mother' ? 'selected' : '' }}>أم</option>
                        <option value="brother" {{ old('relationship', $guardian->relationship) == 'brother' ? 'selected' : '' }}>أخ</option>
                        <option value="sister" {{ old('relationship', $guardian->relationship) == 'sister' ? 'selected' : '' }}>أخت</option>
                        <option value="uncle" {{ old('relationship', $guardian->relationship) == 'uncle' ? 'selected' : '' }}>عم/خال</option>
                        <option value="aunt" {{ old('relationship', $guardian->relationship) == 'aunt' ? 'selected' : '' }}>عمة/خالة</option>
                        <option value="grandfather" {{ old('relationship', $guardian->relationship) == 'grandfather' ? 'selected' : '' }}>جد</option>
                        <option value="grandmother" {{ old('relationship', $guardian->relationship) == 'grandmother' ? 'selected' : '' }}>جدة</option>
                        <option value="other" {{ old('relationship', $guardian->relationship) == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة</label>
                    <input type="password" name="password" placeholder="اتركه فارغاً للحفاظ على كلمة المرور الحالية"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المهنة</label>
                    <input type="text" name="occupation" value="{{ old('occupation', $guardian->occupation) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- بيانات التواصل -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">
                <i class="fas fa-map-marker-alt text-green-500 ml-2"></i>
                بيانات التواصل
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                    <input type="text" name="address" value="{{ old('address', $guardian->address) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الطوارئ</label>
                    <input type="tel" name="emergency_phone" value="{{ old('emergency_phone', $guardian->emergency_phone) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- حالة الحساب -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 pb-2 border-b">
                <i class="fas fa-cog text-gray-500 ml-2"></i>
                إعدادات الحساب
            </h3>
            
            <label class="flex items-center space-x-2 space-x-reverse">
                <input type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $guardian->user->is_active) ? 'checked' : '' }}
                    class="rounded text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700">الحساب نشط</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.guardians.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
