<?php

use Illuminate\Support\Facades\Route;
use \App\Models\Sensors;


Route::get('/overview', function () {
    $data = [];
    // $sensor = new Sensors;
    // foreach (Sensors::getUUIDs() as $row) {
    // $sensor = Sensors::getSensorsfromUUID($row);
    // $data[$sensor->id] = $sensor->get50();
    // $data .= $sensor->getSensorsFromUUID();
    // }
    return 'WIP';
    // return view('overview', $data);
});


Route::get('/', function () {
    return redirect('/overview');
});