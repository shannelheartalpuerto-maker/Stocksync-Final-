<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
    // Async dashboard data
    Route::get('/dashboard/sales-data', [App\Http\Controllers\AdminController::class, 'salesData'])->name('dashboard.sales_data');
    Route::get('/dashboard/forecast-data', [App\Http\Controllers\AdminController::class, 'forecastData'])->name('dashboard.forecast_data');
    Route::get('/staff', [App\Http\Controllers\AdminController::class, 'manageStaff'])->name('staff.index');
    Route::post('/staff', [App\Http\Controllers\AdminController::class, 'storeStaff'])->name('staff.store');
    Route::put('/staff/{id}', [App\Http\Controllers\AdminController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/{id}', [App\Http\Controllers\AdminController::class, 'destroyStaff'])->name('staff.destroy');
    
    // Admin Management Routes
    Route::get('/admins', [App\Http\Controllers\AdminController::class, 'manageAdmins'])->name('admins.index');
    Route::post('/admins', [App\Http\Controllers\AdminController::class, 'storeAdmin'])->name('admins.store');
    Route::put('/admins/{id}', [App\Http\Controllers\AdminController::class, 'updateAdmin'])->name('admins.update');
    Route::delete('/admins/{id}', [App\Http\Controllers\AdminController::class, 'destroyAdmin'])->name('admins.destroy');
    
    // Inventory Routes
    Route::resource('categories', App\Http\Controllers\CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('brands', App\Http\Controllers\BrandController::class)->except(['create', 'edit', 'show']);
    Route::resource('products', App\Http\Controllers\ProductController::class)->except(['create', 'edit', 'show']);
    Route::put('/products/{id}/thresholds', [App\Http\Controllers\ProductController::class, 'updateThresholds'])->name('products.update_thresholds');
    Route::post('/products/{id}/stock-in', [App\Http\Controllers\ProductController::class, 'stockIn'])->name('products.stock_in');
    Route::post('/products/{id}/damaged', [App\Http\Controllers\ProductController::class, 'reportDamaged'])->name('products.damaged');
    Route::get('/stock-logs', [App\Http\Controllers\StockLogController::class, 'index'])->name('stock_logs.index');
    
    // Transaction Routes
    Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
});

Route::middleware(['auth', 'is_staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StaffController::class, 'index'])->name('dashboard');
    Route::get('/inventory', [App\Http\Controllers\StaffController::class, 'inventory'])->name('inventory');
    Route::post('/inventory/{id}/damage', [App\Http\Controllers\StaffController::class, 'reportDamaged'])->name('inventory.damage');
    
    Route::get('/transactions', [App\Http\Controllers\StaffController::class, 'transactions'])->name('transactions');
    Route::post('/transactions/{id}/return', [App\Http\Controllers\StaffController::class, 'returnTransaction'])->name('transactions.return');
    Route::get('/logs', [App\Http\Controllers\StaffController::class, 'logs'])->name('logs');

    Route::get('/pos', [App\Http\Controllers\StaffController::class, 'pos'])->name('pos');
    Route::post('/pos/process', [App\Http\Controllers\StaffController::class, 'processSale'])->name('pos.process');
    Route::post('/check-stock', [App\Http\Controllers\StaffController::class, 'checkStock'])->name('check_stock');
    Route::post('/report-issue', [App\Http\Controllers\StaffController::class, 'reportIssue'])->name('report_issue');
});

// Deployment Utilities - Run once then remove or secure
// Route::get('/fix-storage', function () { ... });
