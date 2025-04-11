<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Middleware\ValidateURIParams;

Route::post('register', [ApiAuthController::class, 'register'])->name('api.register');
Route::post('login', [ApiAuthController::class, 'login'])->name('api.login');

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

Route::middleware('auth:sanctum')->group(function() {
    Route::post('logout', [ApiAuthController::class, 'logout'])->name('api.logout');
    Route::get('user', [ApiAuthController::class, 'user'])->name('api.user');
});

//URI paramterek
//1. Regex
Route::get('uri-params1/{number}/{string}/{optional?}', function ($number, $string, $optional = null){
    return response()->json([
        'number' => $number,
        'string' => $string,
        'optional' => $optional
    ]);
})->where('number', '[0-9]+')->where('string', '[A-Za-z]+');

//2. Middleware
Route::get('uri-params2/{number}/{string}/{optional?}', function ($number, $string, $optional = null){
    return response()->json([
        'number' => $number,
        'string' => $string,
        'optional' => $optional
    ]);
})->middleware([ValidateURIParams::class]);

//3. Server side
Route::get('uri-params3/{number}/{string}/{optional?}', function ($number, $string, $optional = null){
    if(!filter_var($number, FILTER_VALIDATE_INT)) {
        $errors['number'] = 'A $number-nek poz. szamnak kell lennie';
    }

    if(!is_string($string)) {
        $errors['string'] = 'A $string-nek sozvegnek kell lennie';
    }

    if(empty($errors)) {
        return response()->json(null, 204);
    }

    return response()->json($errors, 418);
});
