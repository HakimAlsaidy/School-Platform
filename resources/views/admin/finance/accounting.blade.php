@extends('layouts.dashboard')

@section('page-title', 'المصروفات والإيرادات')
@section('page-description', 'التتبع المالي للمدرسة')

@section('dashboard-content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-arrow-down text-green-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">إجمالي الإيرادات</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($totalIncome, 2) }} ر.س</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-arrow-up text-red-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">إجمالي المصروفات</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($totalExpense, 2) }} ر.س</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-indigo-600"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">صافي الربح</p>
                <p class="text-2xl font-bold text-indigo-600">{{ number_format($net, 2) }} ر.س</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- الإيرادات -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">الإيرادات</h3>
            <button onclick="openIncomeModal()" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus ml-1"></i> إضافة
            </button>
        </div>
        <div class="space-y-3">
            @forelse($incomes as $income)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $income->title }}</p>
                        <p class="text-xs text-gray-500">{{ $income->category_label }} - {{ $income->income_date->format('Y/m/d') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-green-600">+{{ number_format($income->amount, 2) }}</span>
                        <form action="{{ route('admin.finance.incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('حذف؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">لا توجد إيرادات</p>
            @endforelse
        </div>
    </div>

    <!-- المصروفات -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">المصروفات</h3>
            <button onclick="openExpenseModal()" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus ml-1"></i> إضافة
            </button>
        </div>
        <div class="space-y-3">
            @forelse($expenses as $expense)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $expense->title }}</p>
                        <p class="text-xs text-gray-500">{{ $expense->category_label }} - {{ $expense->expense_date->format('Y/m/d') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-red-600">-{{ number_format($expense->amount, 2) }}</span>
                        <form action="{{ route('admin.finance.expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('حذف؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 py-4">لا توجد مصروفات</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal إيراد -->
<div id="incomeModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeIncomeModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4">إضافة إيراد</h3>
            <form action="{{ route('admin.finance.incomes.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="العنوان">
                <input type="number" name="amount" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="المبلغ">
                <div class="grid grid-cols-2 gap-4">
                    <select name="category" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="tuition">رسوم دراسية</option>
                        <option value="donation">تبرع</option>
                        <option value="funding">تمويل</option>
                        <option value="rental">إيجار</option>
                        <option value="other">أخرى</option>
                    </select>
                    <input type="date" name="income_date" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-xl">حفظ الإيراد</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal مصروف -->
<div id="expenseModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeExpenseModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4">إضافة مصروف</h3>
            <form action="{{ route('admin.finance.expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="title" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="العنوان">
                <input type="number" name="amount" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="المبلغ">
                <div class="grid grid-cols-2 gap-4">
                    <select name="category" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                        <option value="salaries">رواتب</option>
                        <option value="utilities">مرافق</option>
                        <option value="supplies">لوازم</option>
                        <option value="maintenance">صيانة</option>
                        <option value="transport">نقل</option>
                        <option value="activities">أنشطة</option>
                        <option value="other">أخرى</option>
                    </select>
                    <input type="date" name="expense_date" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white rounded-xl">حفظ المصروف</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openIncomeModal(){document.getElementById('incomeModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeIncomeModal(){document.getElementById('incomeModal').classList.add('hidden');document.body.style.overflow='';}
function openExpenseModal(){document.getElementById('expenseModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeExpenseModal(){document.getElementById('expenseModal').classList.add('hidden');document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeIncomeModal();closeExpenseModal();}});
</script>
@endpush
@endsection
