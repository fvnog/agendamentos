<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LunchBreakController;
use App\Http\Controllers\UserSchedulesController;


Route::get('/', [UserSchedulesController::class, 'index'])->name('welcome');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('appointments', AppointmentController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('users', UserController::class);


    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/meus-horarios', [ScheduleController::class, 'index'])->name('schedules.index');
    

    Route::get('/lunch-break/create', [LunchBreakController::class, 'index'])->name('lunch-break.create');
Route::post('/lunch-break', [LunchBreakController::class, 'store'])->name('lunch-break.store');
Route::get('lunch-break', [LunchBreakController::class, 'index'])->name('lunch-break.index');



});



require __DIR__.'/auth.php';
