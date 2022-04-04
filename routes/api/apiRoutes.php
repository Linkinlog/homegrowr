<?php

use \App\Models\Reading;
use \App\Models\Sensors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

//Route for adding readings
Route::post('/addreading', function () {
    // TODO * change arduinio script to send type instead of pin and then change accordingly here
    if (!isset($_REQUEST['value']) or !isset($_REQUEST['pin']) or !isset($_REQUEST['uuid'])) {
        return;
    }
    date_default_timezone_set("America/New_York");

    $uuid = $_REQUEST['uuid'];
    $type = $_REQUEST['pin'];

    $plant_id = $type !== 'soil' ? NULL : (isset($_REQUEST['plant_id']) ? $_REQUEST['plant_id'] : 0);
    $value = $_REQUEST['value'];

    $ts = date('Y-m-d H:i:s');
    $status = 1;

    if (intval($value) == 0) {
        $value = 0;
        $status = 2;
    }

    $id = Sensors::getSensorsfromUUID($uuid, $type)->id;

    if (!$id || intval($id) == 0) {
        $id = DB::table('sensors')->insertGetId(
            ['type' => $type, 'uuid' => $uuid, 'plant_id' => $plant_id, 'relay_pin' => 0]
        );
    }

    $result = DB::insert('insert into readings (sensors_id, value, status_id, TS) values (?, ?, ?, ?)', [$id, $value, $status, $ts]);

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
    $id = Sensors::where('uuid', $uuid)->first()->value('relay_pin');
    return exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $id query");
});

// Check routes for seeing if the fans should be on/off
Route::get('/check', function () {
    $sensors = Sensors::select('alias', 'uuid')
        ->groupBy('uuid')
        ->get();
    echo '<ul>';
    foreach ($sensors as $sensor) {
        echo "<li><a style='font-size:100px; padding: 7px;' href='/api/check/$sensor->uuid'>$sensor->alias</a></li>";
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
        if ($reading->type == 'Temperature' and $reading->value > 90  or $reading->type == 'Humidity' and $reading->value > 85) {
            $setOn = True;
            echo "<h3>$reading->type -> $reading->value <br /></h3>";
        } elseif ($reading->type == 'Temperature' and $reading->value < 73) {
            $setOn = False;
            echo "<h3><strong>Temp under danger limit, $reading->value</h3>";
        } else {
            echo "<h3>$reading->type -> $reading->value <br /></h3>";
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
    return Sensors::getUUIDs();
});
