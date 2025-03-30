<?php

use App\Http\Controllers\API\TestController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'name'     => 'required',
    ]);

    $user = User::create($request->all());

    return response($user->createToken('token')->plainTextToken);
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
 
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken('access_token')->plainTextToken;
 
    return response()->json(['token' => $token]);
});

Route::prefix('/')->group(function () {
    Route::apiResource('tests', TestController::class);
})->middleware('auth:sanctum');