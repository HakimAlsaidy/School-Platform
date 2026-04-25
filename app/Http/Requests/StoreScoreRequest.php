<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScoreRequest extends FormRequest
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
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_type' => [
                'required',
                Rule::in(['quiz', 'midterm', 'final', 'homework', 'participation']),
            ],
            'score' => [
                'required',
                'numeric',
                'min:0',
                'lte:max_score', // Score must be less than or equal to max_score
            ],
            'max_score' => [
                'required',
                'numeric',
                'min:1',
                'max:1000',
            ],
            'exam_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'semester' => [
                'required',
                'string',
                Rule::in(['first', 'second', 'summer']),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'الطالب',
            'subject_id' => 'المادة',
            'exam_type' => 'نوع الاختبار',
            'score' => 'الدرجة',
            'max_score' => 'الدرجة الكاملة',
            'exam_date' => 'تاريخ الاختبار',
            'semester' => 'الفصل الدراسي',
            'notes' => 'الملاحظات',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'الطالب مطلوب',
            'student_id.exists' => 'الطالب المحدد غير موجود',
            'subject_id.required' => 'المادة مطلوبة',
            'subject_id.exists' => 'المادة المحددة غير موجودة',
            'exam_type.required' => 'نوع الاختبار مطلوب',
            'exam_type.in' => 'نوع الاختبار غير صحيح',
            'score.required' => 'الدرجة مطلوبة',
            'score.numeric' => 'الدرجة يجب أن تكون رقماً',
            'score.min' => 'الدرجة لا يمكن أن تكون سالبة',
            'score.lte' => 'الدرجة لا يمكن أن تتجاوز الدرجة الكاملة',
            'max_score.required' => 'الدرجة الكاملة مطلوبة',
            'max_score.min' => 'الدرجة الكاملة يجب أن تكون 1 على الأقل',
            'exam_date.required' => 'تاريخ الاختبار مطلوب',
            'exam_date.before_or_equal' => 'تاريخ الاختبار لا يمكن أن يكون في المستقبل',
            'semester.required' => 'الفصل الدراسي مطلوب',
            'semester.in' => 'الفصل الدراسي غير صحيح',
        ];
    }
}
