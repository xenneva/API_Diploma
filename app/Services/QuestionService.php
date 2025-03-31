<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

class QuestionService
{
    public function index(): Collection
    {
        return Question::query()
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): ?Question
    {
        /** @var Question $question */
        $question = Question::query()->create($data);

        return $question;
    }

    public function update(Question $question, array $data): bool
    {
        return $question->update($data);
    }

    public function delete(Question $question): bool
    {
        return $question->delete();
    }
}