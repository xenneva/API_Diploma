<?php

namespace App\Http\Controllers\API;

use App\Enums\QuestionLevels;
use App\Enums\QuestionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\AutoTest\AutoTestPassRequest;
use App\Http\Resources\AutoTestResource;
use App\Http\Resources\AutoTestResultResource;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AutoTestController extends Controller
{
    const START_LEVEL = QuestionLevels::B_FIRST;
    const MAX_ANSWERS = 11;

    public function process(AutoTestPassRequest $request) 
    {
        $data = $request->validated();

        if (!isset($data['id']) || !$data['id']) {
            $user = auth('sanctum')->user();
            $id = DB::table('auto_test_passes')->insertGetId(['user_id' => $user->id]);

            $previous_level = $level = DB::table('auto_test_passes')
                ->where('user_id', $user->id)
                ->whereNotNull('result')
                ->orderByDesc('id')
                ->first()?->result;

            $level = $previous_level ?? self::START_LEVEL->value;
            $questions = self::getQuestions($level);

            return AutoTestResource::make(['id' => $id, 'questions' => $questions]);
        } else {
            $correct_percent = self::processAnswers($data);

            $previous_try  = DB::table('auto_test_passes_iterations')
                ->where('auto_test_pass_id', $data['id'])->orderByDesc('id')->first();

            $previous_level = $previous_try ? $previous_try->intermediate_result : null;

            if (!$previous_level) {
                $previous_level = self::START_LEVEL->value;
                $next_level = self::calculateNextLevel($correct_percent, $previous_level);

                DB::table('auto_test_passes_iterations')
                    ->insert(['auto_test_pass_id' => $data['id'], 'intermediate_result' => $next_level]);

                $questions = self::getQuestions($next_level);

                return AutoTestResource::make(['id' => $data['id'], 'questions' => $questions]);
            } else {
                $next_level = self::calculateNextLevel($correct_percent, $previous_level);
                $tries_count = DB::table('auto_test_passes_iterations')->where('auto_test_pass_id', $data['id'])->count();

                if ($next_level == $previous_level || $tries_count >= 3) {
                    DB::table('auto_test_passes')
                        ->where(['id' => $data['id']])
                        ->update(['result' => $next_level]);

                    return AutoTestResultResource::make(['id' => $data['id'], 'level' => QuestionLevels::toLine($next_level)]);
                } else {
                    DB::table('auto_test_passes_iterations')
                        ->insert(['auto_test_pass_id' => $data['id'], 'intermediate_result' => $next_level]);

                    $questions = self::getQuestions($next_level);

                    return AutoTestResource::make(['id' => $data['id'], 'questions' => $questions]);
                }
            }
        }
    }

    private function getQuestions(int $level): Collection
    {
        return Question::query()
            ->where('level', $level)
            ->get()->shuffle()->take(10);
    }

    private function processAnswers(array $data): int
    {
        $questions = Question::query()->get();

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

        $score = (float)($correctAnswers/count($data['try']))*100;

        return $score;
    }

    private function calculateNextLevel(int $result, int $previous_level): int
    {
        if ($result < 40) {
            return max($previous_level - 1, 1);
        } else if ($result >= 40 && $result < 80) {
            return $previous_level;
        } else {
            return min($previous_level + 1, 6);
        }
    }
}