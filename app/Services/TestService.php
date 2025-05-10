<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TestService
{
    const MAX_ANSWERS = 11;

    public function index(): Collection
    {
        return Test::query()
        ->orderBy('created_at', 'desc') // Сортируем по времени прохождения (created_at)
        ->get();
    }

    public function create(array $data): ?Test
    {
        /** @var Test $test */
        $test = Test::query()->create($data);

        return $test;
    }

    public function update(Test $test, array $data): bool
    {
        return $test->update($data);
    }

    public function delete(Test $test): bool
    {
        return $test->delete();
    }

    public function addQuestion(Test $test, Question $question): bool
    {
        $test->questions()->attach($question->id);

        return true;
    }

    public function removeQuestion(Test $test, Question $question): bool
    {
        $test->questions()->detach([$question->id]);

        return true;
    }

    public function pass(Test $test, array $data): bool
    {
        $questions = $test->questions;

        $correctAnswers = 0;

        foreach ($data['answers'] as $answerData) {
            $id = $answerData['id'];

            $question = $questions->first(function (Question $question) use ($id) {
                return $question->id === $id;
            });

            if (!$question) {
                continue;
            }

            $answers = [$question->answer];

            if ($question->enable_synonyms) {
                $synonyms_data = DB::table('synonyms')->where('word', $question->answer)->first();
                
                foreach (json_decode($synonyms_data->synonyms) as $synonym) {
                    $answers[] = $synonym;

                    if (count($answers) > self::MAX_ANSWERS) {
                        break;
                    }
                }
            }

            if (in_array($answerData['answer'], $answers)) {
                $correctAnswers++;
            }
        }

        $score = (float)($correctAnswers/count($questions))*100;
        
        /** @var User $user */
        $user = auth('sanctum')->user();
        
        $user->tests()->attach($test->id, ['score' => $score, 'created_at' => now()->timezone(4)]);

        return true;
    }
}