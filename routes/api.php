<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/profiles',[ProfileController::class,'index']);
Route::get('/profiles/search', [ProfileController::class, 'search']);

Route::get('/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migration done';
});