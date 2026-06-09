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
    // Dashboard - All authenticated users
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // Master Data Mesin - Super Admin only
    Route::post('/machines/import', [\App\Http\Controllers\MachineController::class, 'import'])
        ->name('machines.import')
        ->middleware('role:Super Admin');
    Route::resource('machines', \App\Http\Controllers\MachineController::class)
        ->middleware('role:Super Admin');

    // Sparepart - Operator & Super Admin only
    Route::get('/spareparts/search', [\App\Http\Controllers\SparepartController::class, 'search'])
        ->name('spareparts.search')
        ->middleware('role:Operator,Super Admin');
    Route::post('/spareparts/{sparepart}/adjust', [\App\Http\Controllers\SparepartController::class, 'adjustStock'])
        ->name('spareparts.adjust')
        ->middleware('role:Operator,Super Admin');
    Route::resource('spareparts', \App\Http\Controllers\SparepartController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->middleware('role:Operator,Super Admin');

    // Manajemen Pengguna - Super Admin only
    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->middleware('role:Super Admin');

    // Ticket routes
    Route::get('/tickets/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assignForm'])
        ->name('tickets.assign.form')
        ->middleware('role:Supervisor,Super Admin');
    Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\TicketController::class, 'assign'])
        ->name('tickets.assign')
        ->middleware('role:Supervisor,Super Admin');
    Route::resource('tickets', \App\Http\Controllers\TicketController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Profile routes - All authenticated users
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::get('/password', [\App\Http\Controllers\ProfileController::class, 'password'])->name('password');
        Route::put('/password/update', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
        Route::get('/photo', [\App\Http\Controllers\ProfileController::class, 'photo'])->name('photo');
        Route::put('/photo/update', [\App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('photo.update');
        Route::delete('/photo/delete', [\App\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('photo.delete');
    });

    // Report routes - Supervisor & Super Admin only
    Route::prefix('reports')->name('reports.')->middleware('role:Supervisor,Super Admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/export-csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('export.csv');
        Route::get('/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('export.pdf');

        // Sparepart stock reports
        Route::get('/sparepart-stock', [\App\Http\Controllers\ReportController::class, 'sparepartStock'])->name('sparepart.stock');
        Route::get('/sparepart-stock/csv', [\App\Http\Controllers\ReportController::class, 'sparepartStockCsv'])->name('sparepart.stock.csv');
        Route::get('/sparepart-stock/pdf', [\App\Http\Controllers\ReportController::class, 'sparepartStockPdf'])->name('sparepart.stock.pdf');

        // Machine data reports
        Route::get('/machine-data', [\App\Http\Controllers\ReportController::class, 'machineData'])->name('machine.data');
        Route::get('/machine-data/csv', [\App\Http\Controllers\ReportController::class, 'machineDataCsv'])->name('machine.data.csv');
        Route::get('/machine-data/pdf', [\App\Http\Controllers\ReportController::class, 'machineDataPdf'])->name('machine.data.pdf');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
