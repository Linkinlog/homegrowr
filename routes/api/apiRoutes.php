<?php

use \App\Models\Reading;
use \App\Models\Pin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $readings = Reading::orderByDesc('TS')
        ->limit(15)
        ->get();
    return $readings;
});

Route::post('/addreading', function () {
    if (!isset($_REQUEST['value']) or !isset($_REQUEST['pin']) or !isset($_REQUEST['uuid'])) {
        return;
    }

    $id = DB::table('wp_pins')
        ->select('wp_pins.id')
        ->leftJoin('wp_plants', function ($join) {
            $join->on('wp_pins.plant_name', '=', 'wp_plants.name')->orOn('wp_pins.plant_name', '=', 'wp_plants.location');
            $join->on('wp_plants.harvest_date', '=', DB::raw("'0000-00-00'"));
        })
        ->where('uuid', $_REQUEST['uuid'])
        ->where('pin', $_REQUEST['pin'])
        ->value('id');

    $result = DB::insert('insert into wp_readings (pin_id, value) values (?, ?)', [$id, $_REQUEST['value']]);
    return $result;
});

Route::post('/uploadpicture', function () {
    $test = 'here2';
    return $test;
});

Route::get('/atmosphere', function () {
    return Reading::atmosphere();
});

Route::get('/q/{uuid}', function ($uuid) {
    $id = Pin::where('uuid', $uuid)->first()->value('relay_pin');
    return exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $id query");
});

Route::get('/check', function () {
    $pins = Pin::select('plant_name', 'uuid')
        ->groupBy('uuid')
        ->get();
    echo '<ul>';
    foreach ($pins as $pin) {
        echo "<li><a href='/check/$pin->uuid'>$pin->plant_name</a></li>";
    }
    echo '</ul>';
});

Route::get('/check/{uuid}', function ($uuid) {
    $readings = DB::table('wp_readings')
        ->select('value', 'alias', 'relay_pin', 'plant_name')
        ->leftJoin('wp_pins', function ($join) use ($uuid) {
            $join->on('wp_pins.id', '=', 'wp_readings.pin_id');
            $join->on('wp_pins.UUID', '=', DB::raw("'$uuid'"));
        })
        ->where('alias', '=', 'Temperature')
        ->orWhere('alias', '=', 'Humidity')
        ->orderByDesc('TS')
        ->limit(2)
        ->get();

    $setOn = null;
    $pin = null;
    $title = $readings[0]->plant_name;
    echo "<h1>Atmosphere for $title</h1>";
    foreach ($readings as $reading) {
        if ($reading->alias == 'Temperature' and $reading->value > 90  or $reading->alias == 'Humidity' and $reading->value > 85) {
            $setOn = True;
            $pin = $reading->relay_pin;
            echo "$reading->alias -> $reading->value <br />";
        } else {
            $pin = $reading->relay_pin;
            echo "$reading->alias -> $reading->value <br />";
        }
    }
    if ($setOn) {
        exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $pin HIGH");
        return "$pin On ";
    } else {
        exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $pin LOW");
        return "$pin Off ";
    }
});
