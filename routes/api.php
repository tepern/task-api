<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$groupData = [
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'tasks',
];

Route::group($groupData, function() {
    Route::post('', [TaskController::class, 'create']);
    Route::post('search', [TaskController::class, 'search']);
    Route::put('update/{id}', [TaskController::class, 'update']);
    Route::patch('update/{id}', [TaskController::class, 'updateStatus']);
    Route::patch('archive/{id}', [TaskController::class, 'archive']);
    Route::delete('delete/{id}', [TaskController::class, 'destroy']);
});
