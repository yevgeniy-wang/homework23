<?php

use App\Http\Controllers\API\LabelController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user/{user}/verify', [\App\Http\Controllers\API\AuthController::class, 'verify'])->name('verify_email');

Route::post('/auth/login', [\App\Http\Controllers\API\AuthController::class, 'login'])->name('login');

Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users', [UserController::class, 'update'])->name('users.update');
Route::get('/users', [UserController::class, 'list'])->name('users.list');
Route::delete('/users', [UserController::class, 'destroy'])->name('users.destroy');




Route::middleware('auth:sanctum')->group(function (){
    Route::get('/auth/logout', [\App\Http\Controllers\API\AuthController::class, 'logout'])->name('logout');

    Route::post('/users/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/link/users', [ProjectController::class, 'linkUsers'])->name('projects.link-users');
    Route::get('/projects', [ProjectController::class, 'list'])->name('projects.list');
    Route::delete('/projects', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/users/labels', [LabelController::class, 'store'])->name('labels.store');
    Route::post('/labels/link/projects', [LabelController::class, 'linkUsers'])->name('labels.link-projects');
    Route::get('/labels', [LabelController::class, 'list'])->name('labels.list');
    Route::delete('/labels', [LabelController::class, 'destroy'])->name('labels.destroy');
});



