<?php

use App\Http\Controllers\FileController;
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

Route::name('OTHER.')->group(function () {
    Route::get('file/{filename}', [FileController::class, 'outputDataFile'])->middleware(['check.token.file'])->name('GET_IMAGE');
});
