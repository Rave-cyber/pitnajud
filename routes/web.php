<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\OrderTrackingController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::resource('orders', OrderController::class)->except(['index', 'store']);

    // Transaction routes
    Route::resource('transactions', TransactionController::class);
});

// Consolidated Admin Routes Group
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Employees
    Route::resource('employees', EmployeeController::class)
        ->names([
            'index' => 'employee.index',
            'create' => 'employee.create',
            'store' => 'employee.store',
            'show' => 'employee.show',
            'edit' => 'employee.edit',
            'update' => 'employee.update',
            'destroy' => 'employee.destroy',
        ]);

    // Suppliers
    Route::resource('suppliers', SupplierController::class)
        ->names([
            'index' => 'suppliers.index',
            'create' => 'suppliers.create',
            'store' => 'suppliers.store',
            'show' => 'suppliers.show',
            'edit' => 'suppliers.edit',
            'update' => 'suppliers.update',
            'destroy' => 'suppliers.destroy',
        ]);    
        
    // Inventory
    Route::resource('inventory', InventoryController::class)
        ->names([
            'index' => 'inventory.index',
            'create' => 'inventory.create',
            'store' => 'inventory.store',
            'show' => 'inventory.show',
            'edit' => 'inventory.edit',
            'update' => 'inventory.update',
            'destroy' => 'inventory.destroy',
        ]);
        
    // Corrected Sales Report Routes
    Route::prefix('sales-reports')->name('sales-reports.')->group(function () {
        Route::get('/', [SalesReportController::class, 'index'])->name('index');
        Route::post('/generate', [SalesReportController::class, 'generate'])->name('generate');
    });
});

// Order Tracking (public route)
Route::get('/track-order', [OrderTrackingController::class, 'trackOrder'])->name('track.order');

require __DIR__.'/auth.php';