<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CertificateController;

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
    return view('pages.home');
})->name('home');

Route::get('/login', [SessionController::class, 'index'])->name('login');
Route::post('/login', [SessionController::class, 'login']);
Route::get('/logout', [SessionController::class, 'logout'])->name('logout');

Route::get('/register', [RegistrationController::class, 'index'])->name('register');
Route::post('/register', [RegistrationController::class, 'register']);

Route::get('/certificates', [CertificateController::class, 'certificates_index'])->middleware('auth')->name('certificates');
Route::post('/certificate/add', [CertificateController::class, 'certificate_add'])->middleware('auth')->name('certificate.add');
Route::get('/certificate/delete/{id}', [CertificateController::class, 'certificate_delete'])->middleware('auth')->name('certificate.delete');
Route::get('/certificate/view/{id}', [CertificateController::class, 'certificate_view'])->middleware('auth')->name('certificate.view');
Route::post('certificate/changeOwner/{id}', [CertificateController::class, 'certificate_changeOwner'])->middleware('auth')->name('certificate.changeOwner');
