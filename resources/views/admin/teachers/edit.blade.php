@extends('layouts.dashboard')

@section('page-title', 'تعديل بيانات المعلم')
@section('page-description', 'تعديل بيانات: ' . $teacher->user->name)

@section('dashboard-content')
<div class="mb-6">
    <a href="{{ route('admin.teachers.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة لقائمة المعلمين
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">معلومات المعلم</h3>
    </div>
    
    <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" class="p-6" id="teacherForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $teacher->user->name) }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone', $teacher->user->phone) }}" required placeholder="05xxxxxxxx"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة</label>
            <input type="password" name="password" placeholder="اتركه فارغاً للحفاظ على كلمة المرور الحالية"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">التخصص</label>
            <input type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500"
                placeholder="مثال: رياضيات">
        </div>
        
        @php
            // تجميع المواد حسب الصف من بيانات المعلم الحالية
            $teacherAssignments = [];
            foreach ($teacher->subjects as $subject) {
                // نفترض أن كل مادة مرتبطة بصف معين
                // نجمع المواد حسب الصفوف
            }
            // طريقة بديلة: نجمع المواد الحالية للمعلم
            $teacherSubjectIds = $teacher->subjects->pluck('id')->toArray();
        @endphp
        
        <!-- تعيين الصفوف والمواد - تصميم مبسط -->
        <div class="mb-6" x-data="teacherAssignment()">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-chalkboard-teacher text-indigo-500 ml-1"></i>
                تعيين الصفوف والمواد
            </label>
            <p class="text-xs text-gray-500 mb-4">اختر الصف ثم حدد المواد التي سيُدرّسها المعلم</p>
            
            <!-- قائمة التعيينات -->
            <div class="space-y-4">
                <template x-for="(assignment, index) in assignments" :key="index">
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border border-indigo-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-indigo-700">
                                <i class="fas fa-graduation-cap ml-1"></i>
                                تعيين #<span x-text="index + 1"></span>
                            </span>
                            <button type="button" @click="removeAssignment(index)" 
                                    class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- اختيار الصف -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">الصف الدراسي <span class="text-red-500">*</span></label>
                                <select x-model="assignment.grade_id" 
                                        @change="loadSubjects(index)"
                                        class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                                    <option value="">-- اختر الصف --</option>
                                    @foreach($grades as $grade)
                                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" :name="'assignments[' + index + '][grade_id]'" :value="assignment.grade_id">
                            </div>
                            
                            <!-- اختيار المواد (متعدد) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">المواد الدراسية <span class="text-red-500">*</span></label>
                                <div x-show="assignment.grade_id" class="space-y-2">
                                    <div class="max-h-40 overflow-y-auto bg-white rounded-lg border border-gray-200 p-2">
                                        @foreach($subjects as $subject)
                                            <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-indigo-50 transition">
                                                <input type="checkbox" 
                                                       :name="'assignments[' + index + '][subjects][]'"
                                                       value="{{ $subject->id }}"
                                                       x-model="assignment.subjects"
                                                       class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                                                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $subject->color ?? '#6366f1' }}"></span>
                                                <span class="text-sm text-gray-700">{{ $subject->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-400">يمكنك اختيار أكثر من مادة</p>
                                </div>
                                <div x-show="!assignment.grade_id" class="text-sm text-gray-400 py-2">
                                    <i class="fas fa-info-circle ml-1"></i>
                                    اختر الصف أولاً
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <!-- زر إضافة تعيين جديد -->
            <button type="button" @click="addAssignment()" 
                    class="mt-4 w-full py-3 border-2 border-dashed border-indigo-300 rounded-xl text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i>
                إضافة صف آخر
            </button>
            
            <!-- ملخص التعيينات -->
            <div x-show="assignments.some(a => a.grade_id && a.subjects.length > 0)" 
                 class="mt-4 bg-white rounded-xl p-4 border border-gray-200">
                <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-green-500"></i>
                    ملخص التعيينات
                </h5>
                <div class="space-y-2">
                    <template x-for="(assignment, index) in assignments.filter(a => a.grade_id && a.subjects.length > 0)" :key="index">
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-indigo-400"></i>
                                <span class="font-medium text-gray-700" x-text="getGradeName(assignment.grade_id)"></span>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <template x-for="subjectId in assignment.subjects" :key="subjectId">
                                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full" x-text="getSubjectName(subjectId)"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $teacher->user->is_active) ? 'checked' : '' }}
                    class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                <span class="text-gray-700">حساب نشط</span>
            </label>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-save ml-2"></i>حفظ التغييرات
            </button>
            <a href="{{ route('admin.teachers.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // بيانات الصفوف والمواد
    const gradesData = @json($grades->pluck('name', 'id'));
    const subjectsData = @json($subjects->pluck('name', 'id'));
    
    // المواد الحالية للمعلم
    const currentSubjects = @json($teacher->subjects->pluck('id')->map(fn($id) => (string)$id)->toArray());
    
    // Alpine.js component للتعيينات
    function teacherAssignment() {
        return {
            assignments: currentSubjects.length > 0 
                ? [{ grade_id: '{{ $grades->first()?->id ?? "" }}', subjects: currentSubjects }]
                : [{ grade_id: '', subjects: [] }],
            
            addAssignment() {
                this.assignments.push({ grade_id: '', subjects: [] });
            },
            
            removeAssignment(index) {
                if (this.assignments.length > 1) {
                    this.assignments.splice(index, 1);
                } else {
                    this.assignments[0] = { grade_id: '', subjects: [] };
                }
            },
            
            loadSubjects(index) {
                // مسح المواد المختارة عند تغيير الصف
                this.assignments[index].subjects = [];
            },
            
            getGradeName(gradeId) {
                return gradesData[gradeId] || '';
            },
            
            getSubjectName(subjectId) {
                return subjectsData[subjectId] || '';
            }
        }
    }
    
    // التحقق قبل الإرسال
    document.getElementById('teacherForm').addEventListener('submit', function(e) {
        let hasAssignment = false;
        let valid = true;
        
        // التحقق من كل Assignment wrapper
        document.querySelectorAll('select').forEach(select => {
            if (select.getAttribute('x-model') === 'assignment.grade_id') {
                const gradeId = select.value;
                if (gradeId) {
                    hasAssignment = true;
                    // البحث عن checkboxes المحددة في نفس التعيين
                    const container = select.closest('.bg-gradient-to-br');
                    const checkedSubjects = container.querySelectorAll('input[type="checkbox"]:checked');
                    if (checkedSubjects.length === 0) {
                        valid = false;
                    }
                }
            }
        });
        
        if (!hasAssignment) {
            e.preventDefault();
            alert('يجب تحديد صف واحد على الأقل مع المواد');
            return;
        }
        
        if (!valid) {
            e.preventDefault();
            alert('يجب اختيار مادة واحدة على الأقل لكل صف محدد');
        }
    });
</script>
@endpush
@endsection
