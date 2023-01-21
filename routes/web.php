<?php

use App\Http\Controllers\Auth as Auth;
use App\Http\Controllers\Backend as Backend;
use App\Http\Controllers\HomeController;
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
    return redirect('/backend');
});

Route::post('logout', [Auth\LoginController::class, 'logout'])->name('logout');
Route::prefix('backend')->group(function () {
    Route::get('/', [Auth\LoginController::class, 'showLoginForm']);
    Route::post('/', [Auth\LoginController::class, 'login'])->name('login');
    Route::get('forgot-password', [Auth\ResetsPasswordsController::class, 'showForgotPasswordResetForm'])->name('forgot-password');
    Route::post('sentresetpassword', [Auth\ResetsPasswordsController::class, 'getResetToken'])->name('sentresetpassword');
    Route::get('reset', [Auth\ResetsPasswordsController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [Auth\ResetsPasswordsController::class, 'resetPassword'])->name('password.update');
});

Route::prefix('backend')->middleware(['auth:web'])->group(function () {
    Route::get('dashboard', [Backend\DashboardController::class, 'index'])->name('dashboard');
    // Route::resource('profile', Backend\ProfileController::class);

    /* Role Route */
    Route::get('roles/select2', [Backend\RoleController::class, 'select2'])->name('roles.select2');
    Route::resource('roles', Backend\RoleController::class);

    /* Menu Manager Route */
    Route::resource('menu-manager', Backend\MenuManagerController::class);
    Route::post('menu-manager/changeHierarchy', [Backend\MenuManagerController::class, 'changeHierarchy'])->name('menu-manager.changeHierarchy');

    /* User Route */
    Route::resource('users', Backend\UserController::class);
    Route::post('reset-password-users', [Backend\UserController::class, 'resetpassword'])->name('users.reset-password-users');
    Route::get('change-password', [Backend\UserController::class, 'changepassword'])->name('change-password');
    Route::post('update-change-password', [Backend\UserController::class, 'updatechangepassword'])->name('update-change-password');


    /* Dokter Route */
    Route::get('dokter/select2', [Backend\DokterController::class, 'select2'])->name('dokter.select2');
    Route::resource('dokter', Backend\DokterController::class);

    /* Pasien Route */
    Route::get('pasien/select2', [Backend\PasienController::class, 'select2'])->name('pasien.select2');
    Route::resource('pasien', Backend\PasienController::class);

    /* Perawatan Route */
    Route::get('perawatan/select2', [Backend\PerawatanController::class, 'select2'])->name('perawatan.select2');
    Route::resource('perawatan', Backend\PerawatanController::class);

    /* Rekam Medis Route */
    Route::resource('rekam-medis', Backend\RekamMedisController::class);
});
