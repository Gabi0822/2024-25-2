<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('tickets.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {

    Route::get('/tickets', [TicketController::class,'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class,'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class,'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class,'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketController::class,'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class,'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class,'destroy'])->name('tickets.destroy');

});
require __DIR__.'/auth.php';
