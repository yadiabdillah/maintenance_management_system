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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/machines/import', [\App\Http\Controllers\MachineController::class, 'import'])->name('machines.import');
    Route::resource('machines', \App\Http\Controllers\MachineController::class);

    Route::post('/spareparts/{sparepart}/adjust', [\App\Http\Controllers\SparepartController::class, 'adjustStock'])->name('spareparts.adjust');
    Route::resource('spareparts', \App\Http\Controllers\SparepartController::class);

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
