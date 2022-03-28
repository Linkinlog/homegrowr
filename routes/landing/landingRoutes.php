<?php

use Illuminate\Support\Facades\Route;


Route::get('/overview', function () {
    return view('overview');
});


Route::get('/', function () {
    return redirect('/overview');
});