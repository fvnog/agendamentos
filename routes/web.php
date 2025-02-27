<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LunchBreakController;
use App\Http\Controllers\UserSchedulesController;
use App\Http\Controllers\ClientScheduleController;
use App\Http\Controllers\ServiceController;

use App\Http\Controllers\PixPaymentController;

Route::get('/verificar-pagamento', [PixPaymentController::class, 'verificarPagamento']);
Route::post('/gerar-pix', [PixPaymentController::class, 'createPayment'])->name('gerar.pix');

Route::post('/lock-schedule', [PixPaymentController::class, 'lockSchedule']);
Route::post('/unlock-schedule', [PixPaymentController::class, 'unlockSchedule']);


Route::get('/', [UserSchedulesController::class, 'index'])->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('appointments', AppointmentController::class);
    Route::resource('users', UserController::class);

    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');

    Route::get('/schedules', [UserSchedulesController::class, 'index'])->name('schedules.index');

    Route::get('/lunch-break/create', [LunchBreakController::class, 'index'])->name('lunch-break.create');
    Route::post('/lunch-break', [LunchBreakController::class, 'store'])->name('lunch-break.store');
    Route::get('/lunch-break', [LunchBreakController::class, 'index'])->name('lunch-break.index');
    

    Route::resource('services', ServiceController::class);
});



Route::get('/agendar', [ClientScheduleController::class, 'index'])->name('client.schedule.index');
Route::post('/agendar', [ClientScheduleController::class, 'store'])->name('client.schedule.store');
// routes/web.php

Route::get('/pagamentos/{scheduleId}', [PaymentController::class, 'showPaymentPage'])->name('client.payment');
Route::post('/payment', [PaymentController::class, 'showPaymentPage'])->name('client.payment.showPaymentPage');

require __DIR__.'/auth.php';
