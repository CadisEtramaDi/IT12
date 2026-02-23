<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/check-availability', [BookingController::class, 'checkAvailability'])->name('availability.check');

Route::get('/create-booking', [BookingController::class, 'index'])->name('bookings.create');
Route::post('/create-booking', [BookingController::class, 'store'])->name('bookings.store');

Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.list');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Admin routes
Route::prefix('admin')->group(function () {
    // Login routes (no middleware)
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
    
    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        
        // Booking management
        Route::get('/bookings', [AdminController::class, 'bookingsIndex'])->name('admin.bookings.index');
        Route::get('/bookings/{id}', [AdminController::class, 'bookingsShow'])->name('admin.bookings.show');
        Route::put('/bookings/{id}/status', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.update-status');
        Route::delete('/bookings/{id}', [AdminController::class, 'deleteBooking'])->name('admin.bookings.delete');
        
        // Payment management
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::get('/bookings/{id}/payment/create', [PaymentController::class, 'create'])->name('admin.payments.create');
        Route::post('/bookings/{id}/payment', [PaymentController::class, 'store'])->name('admin.payments.store');
        Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('admin.payments.show');
        
        // Sales reports
        Route::get('/reports/sales', [PaymentController::class, 'salesReport'])->name('admin.reports.sales');
    });
});
