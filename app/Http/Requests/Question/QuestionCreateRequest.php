<?php

namespace App\Http\Requests\Question;

use App\Enums\QuestionTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionCreateRequest extends FormRequest
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
            'text' => 'required|string',
            'answers' => 'required|array',
            'asnwers.*.text' => 'required|string',
            'asnwers.*.is_correct' => 'required|boolean',
            'enable_synonyms' => 'required|boolean',
            'type' => [Rule::enum(QuestionTypes::class), 'required'],
        ];
    }
}
