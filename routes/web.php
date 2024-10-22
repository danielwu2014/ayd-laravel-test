<?php

use App\Http\Controllers\DevController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware('admin')->group(function () {
    Route::get('/dev', [DevController::class, 'index']);
    Route::post('/dev/execute', [DevController::class, 'executeQuery']);
    Route::post('/dev/export/excel', [DevController::class, 'exportExcel']);
    Route::post('/dev/export/json', [DevController::class, 'exportJson']);
});

Route::get('/error', function () {
    return view('error', ['errorMessage' => request('errorMessage')]);
})->name('error.page');


