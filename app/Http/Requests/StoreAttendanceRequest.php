<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:' . now()->subMonths(3)->format('Y-m-d'), // Max 3 months back
            ],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:students,id'],
            'attendance.*.status' => [
                'required',
                Rule::in(['present', 'absent', 'late', 'excused']),
            ],
            'attendance.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'classroom_id' => 'الفصل',
            'date' => 'التاريخ',
            'attendance' => 'الحضور',
            'attendance.*.student_id' => 'الطالب',
            'attendance.*.status' => 'الحالة',
            'attendance.*.notes' => 'الملاحظات',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'classroom_id.required' => 'الفصل مطلوب',
            'classroom_id.exists' => 'الفصل المحدد غير موجود',
            'date.required' => 'التاريخ مطلوب',
            'date.before_or_equal' => 'لا يمكن تسجيل حضور لتاريخ مستقبلي',
            'date.after' => 'لا يمكن تسجيل حضور لأكثر من 3 أشهر سابقة',
            'attendance.required' => 'بيانات الحضور مطلوبة',
            'attendance.*.status.required' => 'حالة الحضور مطلوبة لكل طالب',
            'attendance.*.status.in' => 'حالة الحضور غير صحيحة',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure date is in proper format
        if ($this->has('date')) {
            $this->merge([
                'date' => date('Y-m-d', strtotime($this->date)),
            ]);
        }
    }
}
