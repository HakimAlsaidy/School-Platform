@extends('layouts.dashboard')

@section('page-title', 'إدارة الفصول')
@section('page-description', 'قائمة الفصول الدراسية')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <select id="gradeFilter" onchange="filterByGrade()" class="px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
            <option value="">جميع الصفوف</option>
            @foreach($grades as $grade)
                <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                    {{ $grade->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button onclick="openCreateModal()" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة فصل جديد
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الفصل</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">الصف</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">السعة</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">عدد الطلاب</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">الإشغال</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($classrooms as $classroom)
                    @php
                        $occupancy = $classroom->capacity > 0 ? ($classroom->students_count / $classroom->capacity) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-door-open text-indigo-600"></i>
                                </div>
                                <span class="font-semibold text-gray-800">{{ $classroom->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $classroom->grade->name }}</td>
                        <td class="px-6 py-4 text-center">{{ $classroom->capacity }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                                {{ $classroom->students_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2">
                                    <div class="h-2 rounded-full
                                        @if($occupancy >= 90) bg-red-500
                                        @elseif($occupancy >= 70) bg-amber-500
                                        @else bg-green-500 @endif"
                                        style="width: {{ min($occupancy, 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ round($occupancy) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.classrooms.show', $classroom) }}" 
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="openEditModal({{ $classroom->id }}, '{{ $classroom->name }}', {{ $classroom->grade_id }}, {{ $classroom->capacity }})" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.classrooms.destroy', $classroom) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الفصل؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-door-open text-4xl mb-3 text-gray-300"></i>
                            <p>لا توجد فصول</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($classrooms->hasPages())
        <div class="p-4 border-t">
            {{ $classrooms->withQueryString()->links() }}
        </div>
    @endif
</div>

<!-- Modal للإضافة والتعديل -->
<div id="classroomModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all animate-modal">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">إضافة فصل جديد</h3>
                <button onclick="closeModal()" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            
            <!-- Form -->
            <form id="classroomForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الفصل <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="classroomName" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="مثال: أ">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الصف <span class="text-red-500">*</span></label>
                        <select name="grade_id" id="classroomGrade" required onchange="showGradeStudentCount()"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            <option value="">-- اختر الصف --</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}" data-students="{{ $grade->students_count ?? 0 }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        <div id="gradeStudentInfo" class="hidden mt-2 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                            <div class="flex items-center gap-2 text-indigo-700">
                                <i class="fas fa-users"></i>
                                <span>عدد الطلاب في هذا الصف: <strong id="gradeStudentCount">0</strong> طالب</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعة <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity" id="classroomCapacity" required min="1" value="30"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-3 mt-6 pt-6 border-t">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold">
                        <i class="fas fa-save ml-2"></i>
                        <span id="submitBtnText">حفظ الفصل</span>
                    </button>
                    <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
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
</style>

@push('scripts')
<script>
    function filterByGrade() {
        const gradeId = document.getElementById('gradeFilter').value;
        const url = new URL(window.location.href);
        if (gradeId) {
            url.searchParams.set('grade_id', gradeId);
        } else {
            url.searchParams.delete('grade_id');
        }
        window.location.href = url.toString();
    }
    
    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'إضافة فصل جديد';
        document.getElementById('submitBtnText').textContent = 'حفظ الفصل';
        document.getElementById('classroomForm').action = '{{ route("admin.classrooms.store") }}';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('classroomName').value = '';
        document.getElementById('classroomGrade').value = '';
        document.getElementById('classroomCapacity').value = '30';
        document.getElementById('classroomModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(id, name, gradeId, capacity) {
        document.getElementById('modalTitle').textContent = 'تعديل الفصل';
        document.getElementById('submitBtnText').textContent = 'حفظ التعديلات';
        document.getElementById('classroomForm').action = '/admin/classrooms/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('classroomName').value = name;
        document.getElementById('classroomGrade').value = gradeId;
        document.getElementById('classroomCapacity').value = capacity;
        document.getElementById('classroomModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal() {
        document.getElementById('classroomModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    
    // عرض عدد الطلاب عند اختيار الصف
    function showGradeStudentCount() {
        const select = document.getElementById('classroomGrade');
        const selectedOption = select.options[select.selectedIndex];
        const infoDiv = document.getElementById('gradeStudentInfo');
        const countSpan = document.getElementById('gradeStudentCount');
        
        if (select.value && selectedOption.dataset.students !== undefined) {
            countSpan.textContent = selectedOption.dataset.students;
            infoDiv.classList.remove('hidden');
        } else {
            infoDiv.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
