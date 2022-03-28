<?php

use Illuminate\Support\Facades\Route;
use \App\Models\Pin;


Route::get('/overview', function () {
    $data = [];
    $pin = new Pin;
    foreach (Pin::getUUIDs() as $row) {
        $pin->uuid = $row->uuid;
        $data .= $pin->getPinFromUUID();
    }
    return view('overview', $data);
});


Route::get('/', function () {
    return redirect('/overview');
});