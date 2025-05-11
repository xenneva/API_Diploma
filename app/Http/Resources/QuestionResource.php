<?php

namespace App\Http\Resources;

use App\Enums\QuestionTypes;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'type' => $this->type,
            'choices' => $this->when($this->type != QuestionTypes::SIMPLE->value, $this->answers->map(fn (Answer $answer) => $answer->answer)),
            'answers' => $this->when(request()->routeIs('questions.*'), $this->answers),
        ];
    }
}
