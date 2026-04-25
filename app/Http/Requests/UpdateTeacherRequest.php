<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
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
        $teacherId = $this->route('teacher')->user_id ?? null;

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
                Rule::unique('users', 'email')->ignore($teacherId),
                'max:255',
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^(05|06|07)[0-9]{8}$/',
            ],
            'specialization' => ['nullable', 'string', 'max:255'],
            'hire_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'qualifications' => ['nullable', 'string', 'max:2000'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'classrooms' => ['nullable', 'array'],
            'classrooms.*' => ['exists:classrooms,id'],
            'is_active' => ['nullable', 'boolean'],
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
            'is_active' => 'الحالة',
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
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وصغير ورقم ورمز خاص',
            'phone.regex' => 'رقم الهاتف يجب أن يكون بالصيغة الصحيحة (05xxxxxxxx)',
            'hire_date.before_or_equal' => 'تاريخ التعيين لا يمكن أن يكون في المستقبل',
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

        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }

        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
