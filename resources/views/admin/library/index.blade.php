@extends('layouts.dashboard')

@section('page-title', 'المكتبة')
@section('page-description', 'إدارة الكتب والإعارات')

@section('dashboard-content')
<button onclick="openBookModal()" class="mb-6 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
    <i class="fas fa-plus"></i> إضافة كتاب
</button>

<!-- الكتب -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
    @forelse($books as $book)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5 text-center">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mx-auto">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
                <h3 class="mt-3 font-bold text-white">{{ $book->title }}</h3>
                <p class="text-xs text-white/80">{{ $book->author ?? 'غير محدد' }}</p>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between text-sm mb-3">
                    <span class="text-gray-500">{{ $book->category ?? 'عام' }}</span>
                    <span class="font-semibold {{ $book->is_available ? 'text-green-600' : 'text-red-600' }}">
                        {{ $book->available_copies }}/{{ $book->total_copies }} متاح
                    </span>
                </div>
                <div class="flex items-center justify-between pt-3 border-t">
                    <button onclick="openLoanModal({{ $book->id }}, '{{ addslashes($book->title) }}')" 
                        class="text-sm text-indigo-600 hover:text-indigo-700" @if(!$book->is_available) disabled @endif>
                        <i class="fas fa-hand-holding ml-1"></i> إعارة
                    </button>
                    <form action="{{ route('admin.library.books.destroy', $book) }}" method="POST" onsubmit="return confirm('حذف الكتاب؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">لا توجد كتب في المكتبة</p>
        </div>
    @endforelse
</div>

<!-- الإعارات -->
<h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
    <i class="fas fa-hand-holding text-indigo-500"></i> الإعارات الحالية
</h3>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الكتاب</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الطالب</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">تاريخ الإعارة</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">تاريخ الاستحقاق</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">الحالة</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600">إجراء</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($loans as $loan)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $loan->book->title }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $loan->student->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $loan->loan_date->format('Y/m/d') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $loan->due_date->format('Y/m/d') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($loan->status == 'borrowed') bg-blue-100 text-blue-700
                            @elseif($loan->status == 'overdue') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $loan->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.library.loans.return', $loan) }}" method="POST">
                            @csrf
                            <button class="px-3 py-1 text-xs bg-green-50 text-green-600 rounded-lg hover:bg-green-100">إعادة</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">لا توجد إعارات حالية</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal إضافة كتاب -->
<div id="bookModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeBookModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4">إضافة كتاب</h3>
            <form action="{{ route('admin.library.books.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="عنوان الكتاب *">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="author" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="المؤلف">
                    <input type="text" name="isbn" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="ISBN">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="category" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="التصنيف">
                    <input type="number" name="total_copies" min="1" value="1" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="عدد النسخ">
                </div>
                <input type="text" name="shelf_location" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="موقع الرف">
                <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الوصف"></textarea>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">حفظ الكتاب</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal إعارة -->
<div id="loanModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeLoanModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4">إعارة كتاب</h3>
            <form id="loanForm" action="{{ route('admin.library.loans.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="book_id" id="loanBookId">
                <p id="loanBookTitle" class="text-sm text-gray-600"></p>
                <select name="student_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر الطالب</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="due_date" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">إعارة</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openBookModal(){document.getElementById('bookModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeBookModal(){document.getElementById('bookModal').classList.add('hidden');document.body.style.overflow='';}
function openLoanModal(id,title){document.getElementById('loanBookId').value=id;document.getElementById('loanBookTitle').textContent='الكتاب: '+title;document.getElementById('loanModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeLoanModal(){document.getElementById('loanModal').classList.add('hidden');document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeBookModal();closeLoanModal();}});
</script>
@endpush
@endsection
