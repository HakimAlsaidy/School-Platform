@extends('layouts.dashboard')

@section('page-title', 'الرسوم الدراسية')
@section('page-description', 'إدارة الرسوم الدراسية والمصروفات')

@section('dashboard-content')
<div class="flex items-center justify-between mb-6">
    <button onclick="openFeeModal()" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إضافة رسم جديد
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($fees as $fee)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $fee->title }}</h3>
                            <span class="text-xs text-white/80">{{ $fee->type_label }}</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white">
                        {{ $fee->status_label }}
                    </span>
                </div>
            </div>
            
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($fee->amount, 2) }}</p>
                        <p class="text-xs text-gray-500">ر.س / {{ $fee->frequency_label }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-700">{{ $fee->student_fees_count }}</p>
                        <p class="text-xs text-gray-500">طالب مفروض عليه</p>
                    </div>
                </div>
                
                @if($fee->description)
                    <p class="text-sm text-gray-500 mb-3">{{ Str::limit($fee->description, 80) }}</p>
                @endif
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div class="flex items-center gap-2">
                        <button onclick="openAssignModal({{ $fee->id }}, '{{ addslashes($fee->title) }}')" 
                            class="px-3 py-2 text-xs bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition">
                            <i class="fas fa-user-plus ml-1"></i> فرض على طلاب
                        </button>
                        <form action="{{ route('admin.finance.fees.destroy', $fee) }}" method="POST" onsubmit="return confirm('حذف هذا الرسم؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="fas fa-money-bill-wave text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-600 mb-2">لا توجد رسوم</h3>
            <p class="text-gray-500">لم يتم إضافة أي رسوم دراسية بعد</p>
        </div>
    @endforelse
</div>

<!-- Modal إضافة رسم -->
<div id="feeModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeFeeModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">إضافة رسم دراسي</h3>
                <button onclick="closeFeeModal()" class="w-10 h-10 rounded-lg hover:bg-gray-100"><i class="fas fa-times text-gray-500"></i></button>
            </div>
            
            <form action="{{ route('admin.finance.fees.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="مثال: الرسوم الدراسية السنوية">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                        <select name="type" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                            <option value="tuition">رسوم دراسية</option>
                            <option value="books">كتب</option>
                            <option value="transport">نقل</option>
                            <option value="activities">أنشطة</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الدورية</label>
                        <select name="frequency" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                            <option value="one_time">مرة واحدة</option>
                            <option value="term">فصل</option>
                            <option value="semester">ترم</option>
                            <option value="yearly">سنوي</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    </div>
                    <div class="pt-6">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="is_installment" class="w-4 h-4 text-indigo-600 rounded">
                            أقساط
                        </label>
                    </div>
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-semibold">حفظ الرسم</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal فرض رسم -->
<div id="assignModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAssignModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">فرض الرسم على الطلاب</h3>
                <button onclick="closeAssignModal()" class="w-10 h-10 rounded-lg hover:bg-gray-100"><i class="fas fa-times text-gray-500"></i></button>
            </div>
            <form id="assignForm" action="" method="POST" class="space-y-4">
                @csrf
                <p id="assignFeeTitle" class="text-sm text-gray-600"></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق *</label>
                    <input type="date" name="due_date" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 font-semibold">فرض الرسم</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openFeeModal() {
        document.getElementById('feeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeFeeModal() {
        document.getElementById('feeModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    function openAssignModal(id, title) {
        document.getElementById('assignForm').action = '/admin/finance/fees/' + id + '/assign';
        document.getElementById('assignFeeTitle').textContent = 'فرض الرسم: ' + title + ' على جميع طلاب المدرسة (أو يمكنك تحديد الصف/الفصل)';
        document.getElementById('assignModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeFeeModal(); closeAssignModal(); } });
</script>
@endpush
@endsection
