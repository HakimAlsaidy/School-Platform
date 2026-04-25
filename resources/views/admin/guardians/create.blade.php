@extends('layouts.dashboard')

@section('page-title', 'إضافة ولي أمر جديد')
@section('page-description', 'إضافة ولي أمر للمدرسة')

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.guardians.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة أولياء الأمور
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات ولي الأمر</h3>
    </div>
    
    <form action="{{ route('admin.guardians.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="05xxxxxxxx"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور <span class="text-red-500">*</span></label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">صلة القرابة</label>
            <select name="relationship"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                <option value="father" {{ old('relationship') == 'father' ? 'selected' : '' }}>الأب</option>
                <option value="mother" {{ old('relationship') == 'mother' ? 'selected' : '' }}>الأم</option>
                <option value="other" {{ old('relationship') == 'other' ? 'selected' : '' }}>آخر</option>
            </select>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المهنة</label>
                <input type="text" name="occupation" value="{{ old('occupation') }}"
                    placeholder="مثال: معلم، مهندس، طبيب..."
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">هاتف الطوارئ</label>
                <input type="text" name="emergency_phone" value="{{ old('emergency_phone') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
            <textarea name="address" rows="2"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">ربط الأبناء (الطلاب)</label>
            <select name="students[]" multiple
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" style="min-height: 150px;">
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ in_array($student->id, old('students', [])) ? 'selected' : '' }}>
                        {{ $student->name }} - {{ $student->classroom->full_name ?? 'بدون فصل' }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">اضغط Ctrl للاختيار المتعدد</p>
        </div>
        
        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                <span class="text-gray-700">حساب نشط</span>
            </label>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>حفظ ولي الأمر
            </button>
            <a href="{{ route('admin.guardians.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
