<?php

namespace App\Services;

use App\Enums\QuestionTypes;
use App\Models\Answer;
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
        $user = auth('sanctum')->user();
        $my_level = request()->query('my_level');

        $previous_level = DB::table('auto_test_passes')
                ->where('user_id', $user->id)
                ->whereNotNull('result')
                ->orderByDesc('id')
                ->first()?->result;

        if ($my_level && $my_level === 'true' && $previous_level) {
            return Test::query()
                ->where('level', $previous_level)
                ->orderBy('created_at', 'desc') // Сортируем по времени прохождения (created_at)
                ->get();
        }

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

        foreach ($data['try'] as $answerData) {
            $id = $answerData['id'];

            $question = $questions->first(function (Question $question) use ($id) {
                return $question->id === $id;
            });

            if (!$question) {
                continue;
            }

            $answers = $question->answers()->where('is_correct', true)->get()->map(function (Answer $answer) {
                return $answer->answer;
            })->toArray();

            if ($question->enable_synonyms && $question->type === QuestionTypes::SIMPLE->value) {
                $synonyms_data = DB::table('synonyms')->where('word', $question->answer)->first();
                
                foreach (json_decode($synonyms_data->synonyms) as $synonym) {
                    $answers[] = $synonym;

                    if (count($answers) > self::MAX_ANSWERS) {
                        break;
                    }
                }
            }

            $correct_answer = true;

            foreach ($answerData['answers'] as $answer) {
                switch ($question->type) {
                    case QuestionTypes::SIMPLE->value:
                    case QuestionTypes::CHOICE->value:
                        if (in_array($answer, $answers)) {
                            break;
                        } else {
                            $correct_answer = false;
                        }
                        break;
                    case QuestionTypes::MULTY_CHOICE->value:
                        if (in_array($answer, $answers)) {
                            $key = array_search($answer, $answers);
                            unset($answers[$key]);
                        } else {
                            $correct_answer = false;
                        }
                        break;
                }
            }

            if ($question->type == QuestionTypes::MULTY_CHOICE->value && !empty($answers)) {
                $correct_answer = false;
            }

            if ($correct_answer) {
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