<?php

use Illuminate\Support\Facades\Route;
use \App\Models\Sensors;

Route::get('/overview', function () {
    $data = Sensors::getUUIDsAndName();
    return view('overview', ['data' => $data->toArray()]);
});


Route::get('/', function () {
    return redirect('/overview');
});
