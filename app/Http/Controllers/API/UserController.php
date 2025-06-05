<?php

namespace App\Http\Controllers\API;

use App\Enums\QuestionLevels;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function myInfo(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $level = DB::table('auto_test_passes')
            ->where('user_id', $user->id)
            ->whereNotNull('result')
            ->orderByDesc('id')
            ->first()?->result;

        if (!$level) {
            $level = 'пройдите входное тестирование';
        } else {
            $level = QuestionLevels::toLine($level);
        }

        return new SuccessResponse(["data" => [
            'name' => $user->name,
            'email' => $user->email,
            'level' => $level,
        ]]);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $user = auth('sanctum')->user();

        $user->update($request->validated());

        return new SuccessResponse(message: 'User update successful');
    }
}