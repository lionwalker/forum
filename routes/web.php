<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create'])->middleware('guest')
    ->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::resource('posts',PostController::class)->except('create','show','edit');
    Route::get('posts/add',[PostController::class,'addNewPost']);
    Route::get('posts/edit/{post}',[PostController::class,'getPostData']);
    Route::delete('posts/delete/{post}',[PostController::class,'destroy']);
    Route::get('posts/approve/{post}',[PostController::class,'approvePost']);

    Route::resource('users',UserController::class)->except('create','show','edit');
    Route::get('/users/make-admin/{user}',[UserController::class,'makeUserAdmin']);
    Route::get('/users/block/{user}',[UserController::class,'blockUser']);
    Route::get('users/add',[UserController::class,'addNewUser']);
    Route::get('users/edit/{user}',[UserController::class,'getUserData']);
    Route::delete('users/delete/{user}',[UserController::class,'destroy']);
});

require __DIR__.'/auth.php';
