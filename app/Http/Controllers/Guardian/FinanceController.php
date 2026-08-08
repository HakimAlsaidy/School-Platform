<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    public function fees()
    {
        $studentIds = Auth::user()->guardian->students()->pluck('id');
        
        $studentFees = StudentFee::with(['fee', 'student.classroom.grade'])
            ->whereIn('student_id', $studentIds)
            ->latest()
            ->get();

        $totalDue = $studentFees->sum('remaining_amount');

        return view('guardian.finance.fees', compact('studentFees', 'totalDue'));
    }

    public function payments()
    {
        $studentIds = Auth::user()->guardian->students()->pluck('id');
        
        $payments = Payment::with(['student.classroom.grade', 'studentFee.fee'])
            ->whereIn('student_id', $studentIds)
            ->latest()
            ->paginate(20);

        return view('guardian.finance.payments', compact('payments'));
    }

    // دفع رسوم (محاكاة الدفع الإلكتروني)
    public function pay(StudentFee $studentFee, Request $request)
    {
        // التحقق من أن الطالب يتبع ولي الأمر
        $ownsStudent = Auth::user()->guardian->students()
            ->whereKey($studentFee->student_id)
            ->exists();
        abort_unless($ownsStudent, 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $studentFee->remaining_amount],
            'method' => ['required', 'in:card,online,wallet'],
        ]);

        $payment = Payment::create([
            'school_id' => $studentFee->school_id,
            'student_fee_id' => $studentFee->id,
            'student_id' => $studentFee->student_id,
            'user_id' => auth()->id(),
            'payment_ref' => 'PAY-ONLINE-' . strtoupper(Str::random(8)),
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'status' => 'completed',
            'payment_date' => now()->toDateString(),
            'notes' => 'دفع إلكتروني بواسطة ولي الأمر',
        ]);

        // تحديث الرسم
        $studentFee->amount_paid += $validated['amount'];
        if ($studentFee->amount_paid >= $studentFee->amount_due) {
            $studentFee->status = 'paid';
        } else {
            $studentFee->status = 'partial';
        }
        $studentFee->save();

        return back()->with('success', 'تمت عملية الدفع بنجاح. رقم العملية: ' . $payment->payment_ref);
    }
}
