<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Fee;
use App\Models\Income;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    // ==================== الرسوم الدراسية ====================
    public function fees()
    {
        $fees = Fee::withCount('studentFees')->latest()->get();
        return view('admin.finance.fees', compact('fees'));
    }

    public function feesStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:tuition,books,transport,activities,other'],
            'frequency' => ['required', 'in:one_time,term,semester,yearly'],
            'status' => ['required', 'in:draft,active,inactive'],
            'due_date' => ['nullable', 'date'],
            'is_installment' => ['nullable'],
            'installments_count' => ['nullable', 'integer', 'min:1'],
        ]);

        $fee = Fee::create([
            'school_id' => auth()->user()->school_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'type' => $validated['type'],
            'frequency' => $validated['frequency'],
            'status' => $validated['status'],
            'due_date' => $validated['due_date'] ?? null,
            'is_installment' => $request->has('is_installment'),
            'installments_count' => $validated['installments_count'] ?? null,
        ]);

        ActivityLog::log('create_fee', "إضافة رسم دراسي: {$fee->title}", $fee);

        return back()->with('success', 'تمت إضافة الرسم الدراسي بنجاح.');
    }

    public function feesUpdate(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:tuition,books,transport,activities,other'],
            'frequency' => ['required', 'in:one_time,term,semester,yearly'],
            'status' => ['required', 'in:draft,active,inactive'],
            'due_date' => ['nullable', 'date'],
        ]);

        $fee->update($validated);

        ActivityLog::log('update_fee', "تحديث الرسم الدراسي: {$fee->title}", $fee);

        return back()->with('success', 'تم تحديث الرسم الدراسي بنجاح.');
    }

    public function feesDestroy(Fee $fee)
    {
        $title = $fee->title;
        $fee->delete();
        ActivityLog::log('delete_fee', "حذف الرسم الدراسي: {$title}");
        return back()->with('success', 'تم حذف الرسم الدراسي بنجاح.');
    }

    // فرض رسم على طلاب صف/فصل
    public function assignFee(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'grade_id' => ['nullable', 'exists:grades,id'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'student_ids' => ['nullable', 'array'],
            'due_date' => ['required', 'date'],
        ]);

        $query = Student::query();
        if (!empty($validated['grade_id'])) {
            $query->where('grade_id', $validated['grade_id']);
        }
        if (!empty($validated['classroom_id'])) {
            $query->where('classroom_id', $validated['classroom_id']);
        }
        if (!empty($validated['student_ids'])) {
            $query->whereIn('id', $validated['student_ids']);
        }

        $students = $query->where('is_active', true)->get();
        $count = 0;
        foreach ($students as $student) {
            StudentFee::firstOrCreate([
                'school_id' => auth()->user()->school_id,
                'fee_id' => $fee->id,
                'student_id' => $student->id,
            ], [
                'amount_due' => $fee->amount,
                'amount_paid' => 0,
                'status' => 'pending',
                'due_date' => $validated['due_date'],
            ]);
            $count++;
        }

        ActivityLog::log('assign_fee', "فرض رسم ({$fee->title}) على {$count} طالب", $fee);

        return back()->with('success', "تم فرض الرسم على {$count} طالب بنجاح.");
    }

    // ==================== المدفوعات ====================
    public function payments()
    {
        $payments = Payment::with(['student', 'studentFee.fee', 'user'])
            ->latest()
            ->paginate(15);
        return view('admin.finance.payments', compact('payments'));
    }

    public function paymentsStore(Request $request)
    {
        $validated = $request->validate([
            'student_fee_id' => ['required', 'exists:student_fees,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,card,transfer,online,wallet'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $studentFee = StudentFee::findOrFail($validated['student_fee_id']);

        $payment = Payment::create([
            'school_id' => auth()->user()->school_id,
            'student_fee_id' => $studentFee->id,
            'student_id' => $studentFee->student_id,
            'user_id' => auth()->id(),
            'payment_ref' => 'PAY-' . strtoupper(Str::random(8)),
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'status' => 'completed',
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // تحديث حالة الرسم
        $studentFee->amount_paid += $validated['amount'];
        if ($studentFee->amount_paid >= $studentFee->amount_due) {
            $studentFee->status = 'paid';
        } elseif ($studentFee->amount_paid > 0) {
            $studentFee->status = 'partial';
        }
        $studentFee->save();

        ActivityLog::log('record_payment', "تسجيل دفعة {$validated['amount']} للطالب", $payment);

        return back()->with('success', 'تم تسجيل الدفعة بنجاح.');
    }

    // ==================== المصروفات والإيرادات ====================
    public function accounting()
    {
        $totalIncome = Income::sum('amount');
        $totalExpense = Expense::sum('amount');
        $net = $totalIncome - $totalExpense;

        $incomes = Income::with('recordedBy')->latest()->take(10)->get();
        $expenses = Expense::with('recordedBy')->latest()->take(10)->get();

        return view('admin.finance.accounting', compact('totalIncome', 'totalExpense', 'net', 'incomes', 'expenses'));
    }

    public function expenseStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'in:salaries,utilities,supplies,maintenance,transport,activities,other'],
            'expense_date' => ['required', 'date'],
        ]);

        $expense = Expense::create([
            'school_id' => auth()->user()->school_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'expense_date' => $validated['expense_date'],
            'recorded_by' => auth()->id(),
        ]);

        ActivityLog::log('create_expense', "تسجيل مصروف: {$expense->title} ({$expense->amount})", $expense);

        return back()->with('success', 'تم تسجيل المصروف بنجاح.');
    }

    public function incomeStore(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'in:tuition,donation,funding,rental,other'],
            'income_date' => ['required', 'date'],
        ]);

        $income = Income::create([
            'school_id' => auth()->user()->school_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'income_date' => $validated['income_date'],
            'recorded_by' => auth()->id(),
        ]);

        ActivityLog::log('create_income', "تسجيل إيراد: {$income->title} ({$income->amount})", $income);

        return back()->with('success', 'تم تسجيل الإيراد بنجاح.');
    }

    public function expenseDestroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'تم حذف المصروف.');
    }

    public function incomeDestroy(Income $income)
    {
        $income->delete();
        return back()->with('success', 'تم حذف الإيراد.');
    }
}
