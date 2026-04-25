@extends('layouts.dashboard')

@section('page-title', 'إدارة الصفوف')
@section('page-description', 'قائمة الصفوف الدراسية')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <button onclick="openGradeCreateModal()" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة صف جديد
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($grades as $grade)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-6 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-graduation-cap text-3xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-white">{{ $grade->name }}</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-3 bg-indigo-50 rounded-xl">
                        <p class="text-xl font-bold text-indigo-600">{{ $grade->classrooms_count }}</p>
                        <p class="text-xs text-indigo-700">فصل</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <p class="text-xl font-bold text-green-600">{{ $grade->students_count }}</p>
                        <p class="text-xs text-green-700">طالب</p>
                    </div>
                </div>
                
                @if($grade->description)
                    <p class="text-sm text-gray-500 mb-4">{{ Str::limit($grade->description, 60) }}</p>
                @endif
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="flex items-center gap-2">
                        <button onclick="openGradeEditModal({{ $grade->id }}, '{{ addslashes($grade->name) }}', {{ $grade->order ?? 1 }}, '{{ addslashes($grade->description ?? '') }}', {{ json_encode($grade->relationLoaded('subjects') ? $grade->subjects->pluck('id') : []) }})" 
                           class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.grades.destroy', $grade) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الصف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('admin.classrooms.index', ['grade_id' => $grade->id]) }}" 
                       class="text-sm text-indigo-600 hover:text-indigo-700">
                        عرض الفصول <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-graduation-cap text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد صفوف</h3>
            <p class="text-gray-500 mb-4">لم يتم إضافة أي صفوف دراسية بعد</p>
            <button onclick="openGradeCreateModal()" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-plus ml-2"></i>إضافة صف
            </button>
        </div>
    @endforelse
</div>

<!-- Modal للإضافة والتعديل -->
<div id="gradeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeGradeModal()"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all animate-modal max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 id="gradeModalTitle" class="text-xl font-bold text-gray-800">إضافة صف جديد</h3>
                <button onclick="closeGradeModal()" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <!-- Form -->
            <form id="gradeForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="_method" id="gradeFormMethod" value="POST">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الصف <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="gradeName" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="مثال: الصف الأول">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب</label>
                        <input type="number" name="order" id="gradeOrder" value="1" min="1"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        <p class="text-xs text-gray-500 mt-1">ترتيب الصف في القائمة</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" id="gradeDescription" rows="2"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="وصف اختياري للصف..."></textarea>
                    </div>
                    
                    <!-- المواد الدراسية -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-book text-indigo-500 ml-1"></i>
                            المواد الدراسية
                        </label>
                        <p class="text-xs text-gray-500 mb-3">اختر المواد التي سيتم تدريسها في هذا الصف</p>
                        <div id="subjectsContainer" class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                            @forelse($subjects as $subject)
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg hover:bg-indigo-50 cursor-pointer transition border border-gray-100 subject-checkbox">
                                    <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" 
                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">{{ $subject->name }}</span>
                                </label>
                            @empty
                                <div class="col-span-2 text-center py-4 text-gray-500 text-sm">
                                    <i class="fas fa-info-circle ml-1"></i>
                                    لا توجد مواد دراسية. 
                                    <a href="{{ route('admin.subjects.index') }}" class="text-indigo-600 hover:underline">إضافة مواد</a>
                                </div>
                            @endforelse
                        </div>
                        <div id="selectedSubjectsCount" class="text-xs text-gray-500 mt-2">
                            <span id="subjectsCount">0</span> مواد محددة
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-3 mt-6 pt-6 border-t sticky bottom-0 bg-white">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold">
                        <i class="fas fa-save ml-2"></i>
                        <span id="gradeSubmitBtnText">حفظ الصف</span>
                    </button>
                    <button type="button" onclick="closeGradeModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .animate-modal {
        animation: modalIn 0.2s ease-out;
    }
    .subject-checkbox input:checked + span {
        color: #4338ca;
        font-weight: 600;
    }
    .subject-checkbox:has(input:checked) {
        background-color: #eef2ff;
        border-color: #818cf8;
    }
</style>

@push('scripts')
<script>
    // تحديث عداد المواد المحددة
    function updateSubjectsCount() {
        const count = document.querySelectorAll('#subjectsContainer input[type="checkbox"]:checked').length;
        document.getElementById('subjectsCount').textContent = count;
    }
    
    // إضافة مستمع للتغييرات
    document.querySelectorAll('#subjectsContainer input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateSubjectsCount);
    });

    function openGradeCreateModal() {
        document.getElementById('gradeModalTitle').textContent = 'إضافة صف جديد';
        document.getElementById('gradeSubmitBtnText').textContent = 'حفظ الصف';
        document.getElementById('gradeForm').action = '{{ route("admin.grades.store") }}';
        document.getElementById('gradeFormMethod').value = 'POST';
        document.getElementById('gradeName').value = '';
        document.getElementById('gradeOrder').value = '1';
        document.getElementById('gradeDescription').value = '';
        // إلغاء تحديد جميع المواد
        document.querySelectorAll('#subjectsContainer input[type="checkbox"]').forEach(cb => cb.checked = false);
        updateSubjectsCount();
        document.getElementById('gradeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function openGradeEditModal(id, name, order, description, subjectIds = []) {
        document.getElementById('gradeModalTitle').textContent = 'تعديل الصف';
        document.getElementById('gradeSubmitBtnText').textContent = 'حفظ التعديلات';
        document.getElementById('gradeForm').action = '/admin/grades/' + id;
        document.getElementById('gradeFormMethod').value = 'PUT';
        document.getElementById('gradeName').value = name;
        document.getElementById('gradeOrder').value = order;
        document.getElementById('gradeDescription').value = description;
        
        // تحديد المواد المرتبطة
        document.querySelectorAll('#subjectsContainer input[type="checkbox"]').forEach(cb => {
            cb.checked = subjectIds.includes(parseInt(cb.value));
        });
        updateSubjectsCount();
        
        document.getElementById('gradeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeGradeModal() {
        document.getElementById('gradeModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGradeModal();
        }
    });
</script>
@endpush
@endsection
