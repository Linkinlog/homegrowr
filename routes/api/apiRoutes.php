<?php

use \App\Models\Reading;
use \App\Models\Pin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

//Route for adding readings
Route::post('/addreading', function () {
    if (!isset($_REQUEST['value']) or !isset($_REQUEST['pin']) or !isset($_REQUEST['uuid'])) {
        return;
    }

    $uuid = $_REQUEST['uuid'];
    $pin = $_REQUEST['pin'];

    $id = Pin::getPinfromUUID($uuid, $pin)->id;

    $result = DB::insert('insert into wp_readings (pin_id, value) values (?, ?)', [$id, $_REQUEST['value']]);

    return $result;
});

//Route for getting base amount of readings - 15 by default
Route::get('/', function () {
    return Reading::getReadingsByTS(15);
});

//Route for getting specified amount of readings
Route::get('readings/{limit}', function ($limit) {
    return Reading::getReadingsByTS($limit);
});

//Route for getting readings specifically for atmosphere
Route::get('/atmosphere', function () {
    return Reading::atmosphere();
});

// Query routes for checking status of relays
Route::get('/q/{uuid}', function ($uuid) {
    $id = Pin::where('uuid', $uuid)->first()->value('relay_pin');
    return exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $id query");
});

// Check routes for seeing if the fans should be on/off
Route::get('/check', function () {
    $pins = Pin::select('alias', 'uuid')
        ->groupBy('uuid')
        ->get();
    echo '<ul>';
    foreach ($pins as $pin) {
        echo "<li><a style='font-size:100px; padding: 7px;' href='/api/check/$pin->uuid'>$pin->alias</a></li>";
    }
    echo '</ul>';
});

Route::get('/check/{uuid}', function ($uuid) {
    //TODO Reading::checkStats($uuid);
    // $readings = new Reading;
    // $readings->uuid = $uuid;
    $readings = Reading::getReadingsByUUID($uuid);

    $setOn = null;
    $pin = null;

    $title = $readings[0]->alias;
    echo "<h1>Atmosphere for $title</h1>";

    foreach ($readings as $reading) {
        $pin = $reading->relay_pin;
        if ($reading->pin == 'Temperature' and $reading->value > 90  or $reading->pin == 'Humidity' and $reading->value > 85) {
            $setOn = True;
            echo "<h3>$reading->pin -> $reading->value <br /></h3>";
        } elseif ($reading->pin == 'Temperature' and $reading->value < 73) {
            $setOn = False;
            echo "<h3><strong>Temp under danger limit, $reading->value</h3>";
        } else {
            echo "<h3>$reading->pin -> $reading->value <br /></h3>";
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

//UUID Routes
Route::get('/uuid', function () {
    return Pin::getUUIDs();
});
