<?php

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\ApiAuthController;
 use App\Http\Middleware\ValidateURIParams;

 Route::post('register', [ApiAuthController::class, 'register'])->name('api.register');
 Route::post('login', [ApiAuthController::class, 'login'])->name-('api.login');

/*
 Route::get('/user', function (Request $request) {
     return $request->user();
 })->middleware('auth:sanctum');
 */

 Route::middleware('auth:sanctum')->group(function() {
    Route::post('logout', [ApiAuthController::class, 'logout'])->name('api.logout');
    Route::get('user', [ApiAuthController::class, 'user'])->name('api.user');
 });

 //URI parameterek

 //1. Beepitett valtozat, ha nem mukodik 404
 Route::get('uri-params1/{number}/{string}/{optional?}', function ($number, $string, $optional = null) {
    return response()->json([
        'number' => $number,
        'string' => $string,
        'optional' => $optional,
    ]);
 })->where('number', '[0-9]+')->where('string', '[A-Za-Z]+');

 //2. Middleware keszitese
 Route::get('uri-params2/{number}/{string}/{optional?}', function ($number, $string, $optional = null) {
    return response()->json([
        'number' => $number,
        'string' => $string,
        'optional' => $optional,
    ]);
 })->middleware([ValidateURIParams::class]);

 //3. Atengedjuk, majd controller-lel kezeljuk
 Route::get('uri-params3/{number}/{string}/{optional?}', function ($number, $string, $optional = null) {
    $errors = [];

    if(!filter_var($number,FILTER_VALIDATE_INT)){
        $errors['number'] = 'A $number-nek poz. szamnak kell lenni';
    }

    if(!is_string($string)){
        $errors['string'] = 'A $string-nek szovegnek kell lenni';
    }

    if(empty($errors)){
        return response()->json(null,204);
    }

    return response()->json($errors, 418);
 });

//Ticket REST vegpontok
Route::get('tickets/{id?}', [ApiAuthController::class,'getTickets'])->where('id','[0-9]+')->name('api.tickets.getTickets');
Route::get(   'tickets/paginated', [ApiAuthController::class, 'getTicketsPaginated'])->name('api.tickets.getTicketsPaginated');
Route::post('tickets', [ApiAuthController::class,'store'])->name('api.tickets.store');
Route::put('tickets/{id}', [ApiAuthController::class,'update'])->where('id','[0-9]+')->name('api.tickets.update');
Route::delete('tickets/{id}', [ApiAuthController::class,'destroy'])->where('id','[0-9]+')->name('api.tickets.destroy');

