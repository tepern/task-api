<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

$groupData = [
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'tasks',
];

Route::group($groupData, function () {
    Route::post('', [TaskController::class, 'create'])->middleware('auth:sanctum');
    Route::post('search', [TaskController::class, 'search']);
    Route::put('update/{id}', [TaskController::class, 'update'])->middleware('auth:sanctum');
    Route::patch('update/{id}', [TaskController::class, 'updateStatus'])->middleware('auth:sanctum');
    Route::patch('archive/{id}', [TaskController::class, 'archive'])->middleware('auth:sanctum');
    Route::delete('delete/{id}', [TaskController::class, 'destroy'])->middleware('auth:sanctum');
    Route::get('edit/{id}', [TaskController::class], 'edit');
});
