@extends('layouts.dashboard')

@section('title', 'تعديل الفصل')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">تعديل الفصل الدراسي</h1>
            <p class="text-gray-600 mt-1">تعديل بيانات: {{ $classroom->grade->name }} - {{ $classroom->name }}</p>
        </div>
        <a href="{{ route('admin.classrooms.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع للقائمة
        </a>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.classrooms.update', $classroom) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الصف الدراسي <span class="text-red-500">*</span></label>
                    <select name="grade_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('grade_id') border-red-500 @enderror">
                        <option value="">اختر الصف</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', $classroom->grade_id) == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('grade_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الفصل <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $classroom->name) }}" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="مثال: أ، ب، ج">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعة القصوى</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $classroom->capacity) }}" min="1"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="30">
                    <p class="text-gray-500 text-sm mt-1">العدد الأقصى للطلاب في الفصل</p>
                </div>
            </div>

            <!-- معلومات حالية -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-2">معلومات الفصل الحالية</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">عدد الطلاب:</span>
                        <span class="font-medium text-gray-800">{{ $classroom->students->count() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">المقاعد المتاحة:</span>
                        <span class="font-medium text-gray-800">{{ $classroom->capacity - $classroom->students->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.classrooms.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
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
