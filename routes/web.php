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

    Route::get('/schedules', [UserSchedulesController::class, 'index'])->name('schedules.index');

    Route::get('/lunch-break/create', [LunchBreakController::class, 'index'])->name('lunch-break.create');
    Route::post('/lunch-break', [LunchBreakController::class, 'store'])->name('lunch-break.store');
    Route::get('/lunch-break', [LunchBreakController::class, 'index'])->name('lunch-break.index');
    

    Route::resource('services', ServiceController::class);
});



Route::get('/agendar', [ClientScheduleController::class, 'index'])->name('client.schedule.index');
Route::post('/agendar', [ClientScheduleController::class, 'store'])->name('client.schedule.store');
Route::get('/payment/check', action: [PaymentController::class, 'checkPayment'])->name('payment.check');

Route::get('/payments/qrcode', [PaymentController::class, 'show'])->name('payments.qrcode');

require __DIR__.'/auth.php';
