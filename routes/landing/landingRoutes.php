<?php

use Illuminate\Support\Facades\Route;
use \App\Models\Pin;


Route::get('/overview', function () {
    $data = [];
    // $pin = new Pin;
    // foreach (Pin::getUUIDs() as $row) {
        // $pin = Pin::getPinfromUUID($row);
        // $data[$pin->id] = $pin->get50();
        // $data .= $pin->getPinFromUUID();
    // }
    return view('overview', $data);
});


Route::get('/', function () {
    return redirect('/overview');
});