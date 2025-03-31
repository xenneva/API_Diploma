<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Question\QuestionCreateRequest;
use App\Http\Requests\Question\QuestionUpdateRequest;
use App\Http\Resources\QuestionResource;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\JsonResponse;

class QuestionController extends Controller
{
    private QuestionService $service;

    public function __construct()
    {
        $this->service = new QuestionService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $questions = $this->service->index();

        return new SuccessResponse(
            data: ['data' => QuestionResource::collection($questions)]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionCreateRequest $request): JsonResponse
    {
        $question = $this->service->create($request->validated());

        return $question
            ? new SuccessResponse(message: 'question created')
            : new FailResponse(message: 'question creation failed');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question): JsonResponse
    {
        return new SuccessResponse(
            data: ['data' => QuestionResource::make($question)],
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionUpdateRequest $request, Question $question): JsonResponse
    {
        $isUpdated = $this->service->update($question, $request->validated());

        return $isUpdated
            ? new SuccessResponse(message: 'question updated')
            : new FailResponse(message: 'question update failed');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question): JsonResponse
    {
        $isDeleted = $this->service->delete($question);

        return $isDeleted
            ? new SuccessResponse(message: 'question deleted')
            : new FailResponse(message: 'question delete failed');
    }
}
