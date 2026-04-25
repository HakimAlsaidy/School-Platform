<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\p{Arabic}\s]+$/u', // Arabic names only
            ],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_date' => [
                'required',
                'date',
                'before:today',
                'after:' . now()->subYears(25)->format('Y-m-d'), // Max 25 years old
            ],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'guardian_id' => ['required', 'exists:guardians,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'medical_notes' => ['nullable', 'string', 'max:1000'],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2MB max
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم الطالب',
            'gender' => 'الجنس',
            'birth_date' => 'تاريخ الميلاد',
            'classroom_id' => 'الفصل',
            'guardian_id' => 'ولي الأمر',
            'address' => 'العنوان',
            'medical_notes' => 'الملاحظات الطبية',
            'photo' => 'الصورة',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الطالب مطلوب',
            'name.min' => 'اسم الطالب يجب أن يكون على الأقل 3 أحرف',
            'name.max' => 'اسم الطالب لا يمكن أن يتجاوز 255 حرف',
            'name.regex' => 'اسم الطالب يجب أن يحتوي على حروف عربية فقط',
            'gender.required' => 'الجنس مطلوب',
            'gender.in' => 'الجنس يجب أن يكون ذكر أو أنثى',
            'birth_date.required' => 'تاريخ الميلاد مطلوب',
            'birth_date.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم',
            'birth_date.after' => 'عمر الطالب لا يمكن أن يتجاوز 25 سنة',
            'classroom_id.required' => 'الفصل مطلوب',
            'classroom_id.exists' => 'الفصل المحدد غير موجود',
            'guardian_id.required' => 'ولي الأمر مطلوب',
            'guardian_id.exists' => 'ولي الأمر المحدد غير موجود',
            'photo.image' => 'الملف يجب أن يكون صورة',
            'photo.mimes' => 'الصورة يجب أن تكون من نوع: jpeg, png, jpg, webp',
            'photo.max' => 'حجم الصورة لا يمكن أن يتجاوز 2 ميجابايت',
            'photo.dimensions' => 'أبعاد الصورة غير صحيحة',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from name
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }
    }
}
