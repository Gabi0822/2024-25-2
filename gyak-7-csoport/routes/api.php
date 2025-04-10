<?php

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\ApiAuthController;

 Route::post('register', [ApiAuthController::class. 'register'])->name('api.register');


 Route::get('/user', function (Request $request) {
     return $request->user();
 })->middleware('auth:sanctum');
