<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Test\TestCreateRequest;
use App\Http\Requests\Test\TestPassRequest;
use App\Http\Requests\Test\TestUpdateRequest;
use App\Http\Resources\TestResource;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Question;
use App\Models\Test;
use App\Services\TestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    private TestService $service;

    public function __construct()
    {
        $this->service = new TestService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tests = $this->service->index();

        return new SuccessResponse(
            data: ['data' => TestResource::collection($tests)]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestCreateRequest $request): JsonResponse
    {
        $test = $this->service->create($request->validated());

        return $test
            ? new SuccessResponse(message: 'test created')
            : new FailResponse(message: 'test creation failed');
    }

    /**
     * Display the specified resource.
     */
    public function show(Test $test): JsonResponse
    {
        return new SuccessResponse(
            data: ['data' => TestResource::make($test)],
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TestUpdateRequest $request, Test $test): JsonResponse
    {
        $isUpdated = $this->service->update($test, $request->validated());

        return $isUpdated
            ? new SuccessResponse(message: 'test updated')
            : new FailResponse(message: 'test update failed');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $test): JsonResponse
    {
        $isDeleted = $this->service->delete($test);

        return $isDeleted
            ? new SuccessResponse(message: 'test deleted')
            : new FailResponse(message: 'test delete failed');
    }

    public function addQuestion(Test $test, Question $question): JsonResponse
    {
        $questionAdded = $this->service->addQuestion($test, $question);

        return $questionAdded
            ? new SuccessResponse(message: 'question added')
            : new FailResponse(message: 'question add failed');
    }

    public function removeQuestion(Test $test, Question $question): JsonResponse
    {
        $questionAdded = $this->service->removeQuestion($test, $question);

        return $questionAdded
            ? new SuccessResponse(message: 'question removed')
            : new FailResponse(message: 'question remove failed');
    }

    public function pass(Test $test, TestPassRequest $request): JsonResponse
    {
        $pass = $this->service->pass($test, $request->validated());

        return $pass
            ? new SuccessResponse(message: 'pass saved')
            : new FailResponse(message: 'pass save fail');
    }
}
