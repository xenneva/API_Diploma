<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function myInfo(): JsonResponse
    {
        $user = auth('sanctum')->user();

        return new SuccessResponse(["data" => [
            'name' => $user->name,
            'email' => $user->email,
        ]]);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $user->update($request->validated());

        return new SuccessResponse(message: 'User update successful');
    }
}