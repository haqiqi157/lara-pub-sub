<?php

use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([JwtMiddleware::class])->group(function () {
    Route::post('logout', [UserController::class, 'logout']);

    Route::middleware([IsAdmin::class])->group(function (){
        Route::prefix('users')->name('user.')->group(function (){
            Route::get('', [UserController::class, 'fetchAll']);
            Route::get('/{id}', [UserController::class, 'findById']);
        });
        Route::prefix('notifications')->group(function () {
            Route::post('send', [AdminNotificationController::class, 'send']);
        });
    });
});



Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
