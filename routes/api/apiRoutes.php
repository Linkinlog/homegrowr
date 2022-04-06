<?php

use \App\Models\Reading;
use \App\Models\Plants;
use \App\Models\Sensors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


//General Scaffolding for CRUD REST API
/*
Routes
* GET /api/
* GET /api/status
* POST /api/readings
* GET /api/readings
* GET /api/readings/atmosphere
* GET /api/readings/{uuid}
* GET /api/sensors
* GET /api/sensors/uuids
* GET /api/relay_pin/{uuid}
*/

//Status
/**
 * * GET /api/status
 *
 * @return html list of sensors and their hyperlinks
 */
Route::get('/status', function () {
    $sensors = Sensors::select('alias', 'uuid')
        ->groupBy('uuid')
        ->get();
    echo '<ul>';
    foreach ($sensors as $sensor) {
        echo "<li><a style='font-size:100px; padding: 7px;' href='/api/readings/$sensor->uuid'>$sensor->alias</a></li>";
    }
    echo '</ul>';
});

//* Create {reading}

/**
 * * POST /api/reading
 *
 * @param int $_REQUEST['value']
 * @param text $_REQUEST['type']
 * @param  varchar $_REQUEST['uuid']
 *
 * @return int / error
 */
Route::post('/readings', function () {
    if (!isset($_REQUEST['value']) or !isset($_REQUEST['type']) or !isset($_REQUEST['uuid'])) {
        return 0;
    }
    date_default_timezone_set("America/New_York");

    $uuid = $_REQUEST['uuid'];
    $type = strtolower($_REQUEST['type']);

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

//* Create {sensor}
Route::post('/sensor', function () {
    if (!isset($_REQUEST['type']) || !isset($_REQUEST['uuid'])) {
        return;
    }
    $sensor = new Sensors;
    $sensor->type = strtolower($_REQUEST['type']);
    $sensor->uuid = $_REQUEST['uuid'];
    $sensor->plant_id = isset($_REQUEST['plant_id']) ? intval($_REQUEST['plant_id']) : 0;
    $sensor->relay_pin = isset($_REQUEST['relay_pin']) ? intval($_REQUEST['relay_pin']) : 0;
    $sensor->save();
});

//* Create {plant}

//* Read {reading}

/**
 * * GET /api/
 *
 * @return array base amount of readings, 15 by default
 */
Route::get('/', function () {
    return Reading::getReadingsByTS(15);
});

/**
 * * GET /api/readings
 *
 * @param int $_REQUEST['limit']
 *
 * @return array custom amount of readings, 10 by default
 */
Route::get('/readings', function () {
    if (isset($_REQUEST['limit'])) {
        $limit = $_REQUEST['limit'];
    } else {
        $limit = 10;
    }
    return Reading::getReadingsByTS($limit);
});

/**
 * * GET /api/readings/atmosphere
 *
 * @return array readings specifically for atmosphere
 */
Route::get('/readings/atmosphere', function () {
    return Reading::atmosphere();
});

/**
 * * GET /api/readings/{uuid}
 *
 * Get readings for given sensor uuid
 *
 * @param  varchar $_REQUEST['uuid']
 *
 * @return string reading for the uuid 
 */
Route::get('/readings/{uuid}', function ($uuid) {
    //TODO Reading::checkStats($uuid);
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

/**
 * * GET /api/relay_pin/{uuid}
 *
 * Query routes for checking status of relays
 *
 * @param  varchar $_REQUEST['uuid']
 *
 * @return string reading of relay
 */
Route::get('/relay_pin/{uuid}', function ($uuid) {
    $id = Sensors::where('uuid', $uuid)->first()->value('relay_pin');
    return exec("sudo /usr/bin/python3 /opt/scripts/relay-switcher.py $id query");
});

//* Read {sensors}

/**
 * * GET /api/sensors
 *
 * Query all sensors
 */
Route::get('/sensors', function () {
    return Sensors::limit('50')->get();
});

/**
 * * GET /api/sensors/uuids
 *
 * Query all UUIDs
 *
 * @return array all active uuids
 */
Route::get('/sensors/uuids', function () {
    return Sensors::getUUIDs();
});

//* Read {plant}

/**
 * * GET /api/plants
 *
 * @return array plants
 */
Route::get('/plants', function () {
    return Plants::limit('50')->get();
});

//* Update {reading}

//* Update {sensor}

//* Update {plant}

//* Delete {reading}

//* Delete {sensor}

//* Delete {plant}
