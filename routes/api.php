<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PlatformController;
use Illuminate\Support\Facades\Storage;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Platforms
    Route::get('/platforms', [PlatformController::class, 'index']);
    Route::post('/platforms/toggle', [PlatformController::class, 'toggle']);

    //upload image
    Route::post('/upload', function (Request $request) {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);
    
        $path = $request->file('image')->store('images', 'public');
        return response()->json(['url' => Storage::url($path)]);
    });
});
