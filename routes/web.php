<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [App\Http\Controllers\AmoCRMController::class, 'index'])->name('dashboard');
    Route::any('/contact-binding/{id}', [App\Http\Controllers\AmoCRMController::class, 'contactBinding'])->name('contact-binding');
    Route::get('/history', [App\Http\Controllers\AmoCRMController::class, 'history'])->name('history');

    Route::get('/amocrm/get_token', [\App\Http\Controllers\AmoCRMIntegrityController::class, 'get_token'])->name('amocrm.integrate');
    Route::any('/contact-binding-create', [\App\Http\Controllers\AmoCRMController::class, 'contactBindingCreate'])->name('contact-binding-create');
});

require __DIR__.'/auth.php';
