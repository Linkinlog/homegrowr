<?php

use \App\Models\Reading;
use \App\Models\Sensors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


//General Scaffolding for CRUD REST API


//* Create {reading}

Route::post('/reading', function () {
    if (!isset($_REQUEST['value']) or !isset($_REQUEST['type']) or !isset($_REQUEST['uuid'])) {
        return;
    }
    $uuid = $_REQUEST['uuid'];
    $type = $_REQUEST['type'];
    $plant_id = isset($_REQUEST['plant_id']) ? $_REQUEST['plant_id'] : 0;
    $value = $_REQUEST['value'];

    $id = Sensors::getSensorsfromUUID($uuid, $type)->id;

    if ($id) {
        $result = DB::insert('insert into readings (sensors_id, value) values (?, ?)', [$id, $value]);
    } else {
        DB::insert('insert into sensors (type, uuid, plant_id) values (?, ?, ?)', [$type, $uuid, $plant_id]);
        $id = Sensors::getSensorsfromUUID($uuid, $type)->id;
        $result = DB::insert('insert into readings (sensors_id, value) values (?, ?)', [$id, $value]);
    }

    return $result;
});


//* Create {sensor}

//* Create {plant}



//* Read {reading}

//Route for getting base amount of readings - 15 by default
Route::get('/', function () {
    return Reading::getReadingsByTS(15);
});

Route::get('/readings', function () {
    if (isset($_REQUEST['limit'])) {
        $limit = $_REQUEST['limit'];
    } else {
        $limit = 10;
    }
    return Reading::getReadingsByTS($limit);
});

//Route for getting readings specifically for atmosphere
Route::get('/readings/atmosphere', function () {
    return Reading::atmosphere();
});


//Get readings for given sensor uuid
Route::get('/readings/{uuid}', function ($uuid) {
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


//* Read {relay_pin}

// Query routes for checking status of relays
Route::get('/relay_pin/{uuid}', function ($uuid) {
    $id = Sensors::where('uuid', $uuid)->first()->value('relay_pin');
    return exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $id query");
});

//* Read {sensors}

// Check routes for seeing if the fans should be on/off
Route::get('/sensors', function () {
    $sensors = Sensors::select('alias', 'uuid')
        ->groupBy('uuid')
        ->get();
    echo '<ul>';
    foreach ($sensors as $sensor) {
        echo "<li><a style='font-size:100px; padding: 7px;' href='/api/readings/$sensor->uuid'>$sensor->alias</a></li>";
    }
    echo '</ul>';
});

//Query all UUIDs
Route::get('/sensors/uuids', function () {
    return Sensors::getUUIDs();
});

//* Read {plant}



//* Update {reading}

//* Update {sensor}

//* Update {plant}



//* Delete {reading}

//* Delete {sensor}

//* Delete {plant}
