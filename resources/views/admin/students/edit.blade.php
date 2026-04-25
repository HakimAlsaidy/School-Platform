@extends('layouts.dashboard')

@section('page-title', 'تعديل بيانات الطالب')
@section('page-description', 'تحديث بيانات ' . $student->name)

@section('dashboard-content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.students.update', $student) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الطالب <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">الجنس <span class="text-red-500">*</span></label>
                    <select id="gender" name="gender" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('gender') border-red-500 @enderror">
                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الميلاد <span class="text-red-500">*</span></label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('birth_date') border-red-500 @enderror">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Grade (Required) -->
                <div>
                    <label for="grade_id" class="block text-sm font-medium text-gray-700 mb-2">الصف <span class="text-red-500">*</span></label>
                    <select id="grade_id" name="grade_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('grade_id') border-red-500 @enderror"
                        onchange="loadClassroomsEdit(this.value)">
                        <option value="">اختر الصف</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id', $student->grade_id) == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('grade_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Classroom (Optional) -->
                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-2">الفصل <span class="text-gray-400 text-xs">(اختياري)</span></label>
                    <select id="classroom_id" name="classroom_id"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('classroom_id') border-red-500 @enderror">
                        <option value="">-- بدون فصل --</option>
                        @foreach($grades as $grade)
                            @if(old('grade_id', $student->grade_id) == $grade->id)
                                @foreach($grade->classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id', $student->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->name }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                    @error('classroom_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Guardian -->
                <div>
                    <label for="guardian_id" class="block text-sm font-medium text-gray-700 mb-2">ولي الأمر <span class="text-red-500">*</span></label>
                    <select id="guardian_id" name="guardian_id" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('guardian_id') border-red-500 @enderror">
                        @foreach($guardians as $guardian)
                            <option value="{{ $guardian->id }}" {{ old('guardian_id', $student->guardian_id) == $guardian->id ? 'selected' : '' }}>
                                {{ $guardian->user->name }} - {{ $guardian->user->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select id="is_active" name="is_active"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="1" {{ old('is_active', $student->is_active) ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ !old('is_active', $student->is_active) ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                
                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                    <textarea id="address" name="address" rows="2"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">{{ old('address', $student->address) }}</textarea>
                </div>
                
                <!-- Medical Notes -->
                <div class="md:col-span-2">
                    <label for="medical_notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات طبية</label>
                    <textarea id="medical_notes" name="medical_notes" rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">{{ old('medical_notes', $student->medical_notes) }}</textarea>
                </div>
                
                <!-- Photo -->
                <div class="md:col-span-2">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">صورة الطالب</label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">اتركه فارغاً للإبقاء على الصورة الحالية</p>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="{{ route('admin.students.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // بيانات الفصول لكل صف
    const gradeClassrooms = @json($grades->mapWithKeys(function($grade) {
        return [$grade->id => $grade->classrooms->map(function($c) {
            return ['id' => $c->id, 'name' => $c->name];
        })];
    }));

    // الفصل الحالي للطالب
    const currentClassroomId = {{ $student->classroom_id ?? 'null' }};

    // تحميل الفصول عند اختيار الصف
    function loadClassroomsEdit(gradeId) {
        const classroomSelect = document.getElementById('classroom_id');
        classroomSelect.innerHTML = '<option value="">-- بدون فصل --</option>';
        
        if (gradeId && gradeClassrooms[gradeId]) {
            gradeClassrooms[gradeId].forEach(function(classroom) {
                const option = document.createElement('option');
                option.value = classroom.id;
                option.textContent = classroom.name;
                if (classroom.id == currentClassroomId) {
                    option.selected = true;
                }
                classroomSelect.appendChild(option);
            });
        }
    }
</script>
@endpush
@endsection
