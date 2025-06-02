<?php

namespace App\Http\Resources;

use App\Enums\QuestionTypes;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutoTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'questions' => QuestionResource::collection($this['questions']),
        ];
    }
}
