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
use App\Http\Controllers\StripeController;
use App\Http\Controllers\PixPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminScheduleController;
use App\Http\Controllers\AdminFinanceController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Http\Controllers\FixedScheduleController;
use App\Http\Controllers\DeleteScheduleController;

Route::get('/agendamentos-whatsapp-bot-3478fhjdks', function (Request $request) {
    $date = $request->input('date', Carbon::today()->toDateString()); // Captura a data da URL ou usa a atual

    // 🔹 BUSCA DIRETA NO BANCO, SEM CACHE
    $schedules = Schedule::whereDate('date', $date)
        ->whereNotNull('client_id') // Apenas horários reservados
        ->with('client') // Carrega os dados do cliente
        ->get()
        ->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'client_name' => $schedule->client->name ?? 'Não cadastrado',
                'client_phone' => $schedule->client->telefone ?? '',
                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
            ];
        });

    return response()->json($schedules);
});



Route::get('/verificar-pagamento', [PixPaymentController::class, 'verificarPagamento']);
Route::post('/gerar-pix', [PixPaymentController::class, 'createPayment'])->name('gerar.pix');

Route::post('/lock-schedule', [PixPaymentController::class, 'lockSchedule']);
Route::post('/unlock-schedule', [PixPaymentController::class, 'unlockSchedule']);
Route::post('/checkout-cartao', [StripeController::class, 'checkout'])->name('checkout.cartao');



Route::get('/', function () {
    return redirect()->route('client.schedule.index');
});

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
    Route::post('/schedules/store', [ScheduleController::class, 'store'])->name('schedules.store');
    
    Route::get('/schedules/delete', [DeleteScheduleController::class, 'index'])->name('schedules.delete');
    Route::post('/schedules/delete/single', [DeleteScheduleController::class, 'deleteSingle'])->name('schedules.delete.single');
    Route::post('/schedules/delete/by-date', [DeleteScheduleController::class, 'deleteByDate'])->name('schedules.delete.byDate');
    Route::post('/schedules/delete/future', [DeleteScheduleController::class, 'deleteFutureSchedules'])->name('schedules.delete.future');
    
// Gerenciamento de Horários Fixos
// Gerenciamento de Horários Fixos
Route::get('/schedules/fixed', [FixedScheduleController::class, 'index'])->name('schedules.fixed.index');
Route::post('/schedules/fixed/store', [FixedScheduleController::class, 'store'])->name('schedules.fixed.store');
Route::post('/schedules/fixed/delete', [FixedScheduleController::class, 'delete'])->name('schedules.fixed.delete');
Route::post('/schedules/fixed/update', [FixedScheduleController::class, 'updateServices'])->name('schedules.fixed.update');


    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');

    Route::get('/schedules', [UserSchedulesController::class, 'index'])->name('schedules.index');

    Route::get('/lunch-break/create', [LunchBreakController::class, 'index'])->name('lunch-break.create');
    Route::post('/lunch-break', [LunchBreakController::class, 'store'])->name('lunch-break.store');
    

    Route::resource('services', ServiceController::class);

        Route::get('/admin/schedules', [AdminScheduleController::class, 'index'])->name('admin.schedules.index'); // Lista horários
        Route::post('/admin/schedules/add-client', [AdminScheduleController::class, 'addClient'])->name('admin.schedules.add-client'); // Adicionar cliente manualmente
        Route::post('/admin/schedules/remove-client', [AdminScheduleController::class, 'removeClient'])->name('admin.schedules.remove-client');

        Route::get('/admin/pagamentos', [AdminFinanceController::class, 'index'])
        ->name('admin.payments.index');
    

});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');


Route::get('/agendar', [ClientScheduleController::class, 'index'])->name('client.schedule.index');
Route::post('/agendar', [ClientScheduleController::class, 'store'])->name('client.schedule.store');

Route::get('/schedules', [ScheduleController::class, 'getSchedules'])->name('schedules.get');
Route::post('/schedule/check-availability', [ScheduleController::class, 'checkAvailability'])->name('schedule.check');



// routes/web.php

Route::get('/pagamentos/{scheduleId}', [PaymentController::class, 'showPaymentPage'])->name('client.payment');
Route::post('/payment', [PaymentController::class, 'showPaymentPage'])->name('client.payment.showPaymentPage');

require __DIR__.'/auth.php';
