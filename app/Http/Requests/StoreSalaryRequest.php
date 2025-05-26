<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1), // Cho phép tạo trước 1 năm
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => 'Vui lòng chọn tháng.',
            'month.between' => 'Tháng không hợp lệ.',
            'year.required' => 'Vui lòng nhập năm.',
            'year.min' => 'Năm không hợp lệ.',
            'year.max' => 'Năm không hợp lệ.',
        ];
    }
}