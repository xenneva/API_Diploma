<?php

namespace App\Http\Requests\Question;

use Illuminate\Foundation\Http\FormRequest;

class QuestionUpdateRequest extends QuestionCreateRequest
{
    public function rules(): array
    {
        return [
            'text' => 'required|string',
            'answer' => 'required|string',
        ];
    }
}
