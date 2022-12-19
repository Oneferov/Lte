<?php

use App\Http\Controllers\RouteController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;
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

Route::controller(RouteController::class)->group(function () {
    Route::get('routes', 'index');
    Route::get('routes/list', 'list')->name('route.list');
});

Route::controller(FileController::class)->group(function () {
    Route::get('files', 'index')->name('file.index');
    Route::post('files/show', 'show')->name('file.show');
    Route::post('files/save', 'save')->name('file.save');
});

Route::get('', AdminController::class);

