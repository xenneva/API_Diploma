<?php

namespace App\Services;

use App\Enums\QuestionTypes;
use App\Models\Question;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QuestionService
{
    const DICTIONARY_URL     = 'http://api.api-ninjas.com/v1/thesaurus?word=';
    const DICTIONARY_API_KEY = 'mio0xWCQL5+mXXpWIzizgQ==EJePyLPzgo1GFfeb';

    public function index(): Collection
    {
        return Question::query()
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): ?Question
    {
        $answers = $data['answers'];

        /** @var Question $question */
        $question = Question::query()->create(Arr::except($data, 'answers'));

        foreach ($answers as $answer) {
            $question->answers()->create(['answer' => $answer['text'], 'is_correct' => $answer['is_correct']]);
        }

        self::processSynonyms($question);

        return $question;
    }

    public function update(Question $question, array $data): bool
    {
        $result = $question->update($data);
        $question->refresh();

        self::processSynonyms($question);

        return $result;
    }

    private function processSynonyms(Question $question): void
    {
        $synonyms_enabled = $question->enable_synonyms && $question->type === QuestionTypes::SIMPLE->value;
        $answer = $synonyms_enabled ? $question->answers->first()->answer : '';

        if ($question->enable_synonyms && !DB::table('synonyms')->where('word', $answer)->exists()) {
            $client = new Client([
                'headers' => ['X-Api-Key' => self::DICTIONARY_API_KEY],
                'verify' => false
            ]);

            $response = $client->get(self::DICTIONARY_URL . $answer);

            if ($response->getBody()) {
                $data = json_decode($response->getBody(), true);

                DB::table('synonyms')->insert(['word' => $answer, 'synonyms' => json_encode($data['synonyms'])]);
            }
        }
    }

    public function delete(Question $question): bool
    {
        return $question->delete();
    }
}