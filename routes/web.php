<?php


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ProfileController routes for editing current user account
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// UserController routes for managing users
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class,'profile'])->name('profile');
    
    // List all users
    Route::get('/users', [UserController::class,'index'])->name('users.index');
});

require __DIR__.'/auth.php';
