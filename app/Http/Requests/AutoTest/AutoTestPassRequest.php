<?php

namespace App\Http\Requests\AutoTest;

use Illuminate\Foundation\Http\FormRequest;

class AutoTestPassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'  => 'nullable|integer|exists:auto_test_passes,id',
            'try' => 'nullable|required_with:id|array',
            'try.*.id' => 'required|integer|exists:questions,id',
            'try.*.answers' => 'required|array'
        ];
    }
}
