<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home route
Route::get('/', function () {
    return view('welcome');
});

// Auth routes (already set up by Laravel UI)
Auth::routes();

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Patient routes
Route::resource('patients', PatientController::class);

// Comment routes
Route::post('/patients/{patient}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

// Redirect after login
Route::get('/home', function() {
    return redirect()->route('dashboard');
});
