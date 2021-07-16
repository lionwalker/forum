<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::post('/login',[UserController::class,'login']);
Route::post('/register',[UserController::class,'store']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/posts',[PostController::class,'posts']);
    Route::get('/posts/{post}',[PostController::class,'show']);
    Route::delete('/posts/{post}',[PostController::class,'destroy']);
    Route::put('/posts/{post}',[PostController::class,'update']);
    Route::get('/posts/search/{keyword}',[PostController::class,'search']);
    Route::post('/posts',[PostController::class,'store']);
    Route::get('/my-posts',[PostController::class,'userPosts']);
    Route::get('/profile',[UserController::class,'profile']);
    Route::put('/users/{user}',[UserController::class,'update']);
    Route::post('/logout',[UserController::class,'logout']);

});


