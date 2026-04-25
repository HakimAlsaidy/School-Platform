<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^(05|06|07)[0-9]{8}$/', // Saudi phone format
            ],
            'specialization' => ['required', 'string', 'max:255'],
            'hire_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after:' . now()->subYears(50)->format('Y-m-d'),
            ],
            'qualifications' => ['nullable', 'string', 'max:2000'],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['exists:subjects,id'],
            'classrooms' => ['nullable', 'array'],
            'classrooms.*' => ['exists:classrooms,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم المعلم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'phone' => 'رقم الهاتف',
            'specialization' => 'التخصص',
            'hire_date' => 'تاريخ التعيين',
            'qualifications' => 'المؤهلات',
            'subjects' => 'المواد',
            'classrooms' => 'الفصول',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المعلم مطلوب',
            'name.min' => 'اسم المعلم يجب أن يكون على الأقل 3 أحرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وصغير ورقم ورمز خاص',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.regex' => 'رقم الهاتف يجب أن يكون بالصيغة الصحيحة (05xxxxxxxx)',
            'specialization.required' => 'التخصص مطلوب',
            'hire_date.required' => 'تاريخ التعيين مطلوب',
            'hire_date.before_or_equal' => 'تاريخ التعيين لا يمكن أن يكون في المستقبل',
            'subjects.required' => 'يجب اختيار مادة واحدة على الأقل',
            'subjects.min' => 'يجب اختيار مادة واحدة على الأقل',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }
        
        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }
    }
}
