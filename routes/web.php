<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LogController;

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

$app_url = env('APP_URL');
if(!empty($app_url)){
    URL::forceRootUrl($app_url);
    $schema = explode(':', $app_url)[0];
    URL::forceScheme($schema);
}

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/login', [SessionController::class, 'login_index'])->name('login');
Route::post('/login', [SessionController::class, 'login']);
Route::get('/logout', [SessionController::class, 'logout'])->name('logout');

Route::get('/register', [RegistrationController::class, 'index'])->name('register');
Route::post('/register', [RegistrationController::class, 'register']);

Route::get('/confirm-password', [SessionController::class, 'reauth_index'])->middleware('auth')->name('password.confirm');
Route::post('/confirm-password', [SessionController::class, 'reauth'])->middleware(['auth', 'throttle:6,1']);

Route::get('/settings', [SettingsController::class, 'index'])->middleware('auth')->name('settings');
Route::get('/user/disable/{id}', [SettingsController::class, 'disable'])->middleware('auth')->name('user.disable');
Route::get('/user/enable/{id}', [SettingsController::class, 'enable'])->middleware('auth')->name('user.enable');
Route::get('/user/promote/{id}', [SettingsController::class, 'promote'])->middleware('auth')->name('user.promote');
Route::get('/user/demote/{id}', [SettingsController::class, 'demote'])->middleware('auth')->name('user.demote');
Route::get('/user/set_signer/{id}', [SettingsController::class, 'set_signer'])->middleware('auth')->name('user.set_signer');
Route::get('/user/revoke_signer/{id}', [SettingsController::class, 'revoke_signer'])->middleware('auth')->name('user.revoke_signer');

Route::get('/logs', [LogController::class, 'index'])->middleware('auth')->name('logs');

Route::get('/certificates', [CertificateController::class, 'certificates_index'])->middleware('auth')->name('certificates');
Route::get('/certificates/deleted', [CertificateController::class, 'deleted_index'])->middleware('auth')->name('certificates.deleted');

Route::post('/certificate/add', [CertificateController::class, 'add'])->middleware('auth')->name('certificate.add');
Route::get('/certificate/delete/{id}', [CertificateController::class, 'delete'])->middleware('auth')->name('certificate.delete');
Route::get('/certificate/view/{id}', [CertificateController::class, 'get_information'])->middleware('auth')->name('certificate.view');
Route::get('/certificate/restore/{id}', [CertificateController::class, 'restore'])->middleware('auth')->name('certificate.restore');

Route::get('/certificate/permissions/{id}', [PermissionController::class, 'permission_index'])->middleware(['password.confirm'])->name('permissions');
Route::post('/certificate/changeOwner/{id}', [PermissionController::class, 'change_owner'])->middleware(['password.confirm'])->name('permissions.changeOwner');
Route::post('/certificate/addPermission/{id}', [PermissionController::class, 'add'])->middleware(['password.confirm'])->name('permissions.add');
Route::get('/certificate/deletePermission/{id}', [PermissionController::class, 'delete'])->middleware(['password.confirm'])->name('permissions.delete');

Route::get('/secrets/view/{id}', [CertificateController::class, 'secrets_index'])->middleware(['password.confirm'])->name('secrets');
