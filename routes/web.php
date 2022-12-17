<?php

use App\Http\Controllers\Admin\RouteController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('', AdminController::class);

    Route::group(['prefix' => 'routes'], function () {
        Route::get('', [RouteController::class, 'index']);
        Route::get('list', [RouteController::class, 'list']);
    });
   
});
