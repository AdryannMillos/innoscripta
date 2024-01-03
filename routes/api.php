<?php

use App\Http\Controllers\News\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UsersController;

Route::get('/', function () {
    return response()->json(['message' => 'Live!'], 200);
});

Route::group(['prefix' => 'news'], function () {
    Route::get('/',  [NewsController::class, 'index']);
    Route::post('/', [NewsController::class, 'store']);
});

Route::group(['prefix' => 'users'], function () {
    Route::post('/', [UsersController::class, 'store']);
});
