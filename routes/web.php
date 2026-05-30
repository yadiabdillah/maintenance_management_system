<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::post('/machines/import', [\App\Http\Controllers\MachineController::class, 'import'])->name('machines.import');
    Route::resource('machines', \App\Http\Controllers\MachineController::class);

    Route::post('/spareparts/{sparepart}/adjust', [\App\Http\Controllers\SparepartController::class, 'adjustStock'])->name('spareparts.adjust');
    Route::resource('spareparts', \App\Http\Controllers\SparepartController::class);

    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Ticket routes with custom assign mechanic routes
    Route::get('/tickets/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assignForm'])->name('tickets.assign.form');
    Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assign'])->name('tickets.assign');
    Route::resource('tickets', \App\Http\Controllers\TicketController::class);

    // Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('export.pdf');

        // Sparepart stock reports
        Route::get('/sparepart-stock', [\App\Http\Controllers\ReportController::class, 'sparepartStock'])->name('sparepart.stock');
        Route::get('/sparepart-stock/csv', [\App\Http\Controllers\ReportController::class, 'sparepartStockCsv'])->name('sparepart.stock.csv');
        Route::get('/sparepart-stock/pdf', [\App\Http\Controllers\ReportController::class, 'sparepartStockPdf'])->name('sparepart.stock.pdf');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
