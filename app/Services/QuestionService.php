<?php

namespace App\Services;

use App\Models\Question;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
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
        /** @var Question $question */
        $question = Question::query()->create($data);

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
        if ($question->enable_synonyms && !DB::table('synonyms')->where('word', $question->answer)->exists()) {
            $client = new Client([
                'headers' => ['X-Api-Key' => self::DICTIONARY_API_KEY],
                'verify' => false
            ]);

            $response = $client->get(self::DICTIONARY_URL . $question->answer);

            if ($response->getBody()) {
                $data = json_decode($response->getBody(), true);

                DB::table('synonyms')->insert(['word' => $question->answer, 'synonyms' => json_encode($data['synonyms'])]);
            }
        }
    }

    public function delete(Question $question): bool
    {
        return $question->delete();
    }
}