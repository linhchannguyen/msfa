<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Batch Job Routes
|--------------------------------------------------------------------------
*/

Route::get('/sumary-points', function() {
    // return Artisan::queue('job:sumary-points')->onConnection('redis')->onQueue('jobs');
    return Artisan::call('job:sumary-points');
});
