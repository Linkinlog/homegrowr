<?php

use \App\Models\Reading;
use \App\Models\Plants;
use \App\Models\Sensors;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\ReadingsController;

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
Route::get('/status', function ()
{
    $sensors = Sensors::select('alias', 'uuid')
        ->groupBy('uuid')
        ->get();
    echo '<ul>';
    foreach ($sensors as $sensor) {
        echo "<li><a style='font-size:100px; padding: 7px;' href='/api/readings/$sensor->uuid'>$sensor->alias</a></li>";
    }
    echo '</ul>';
});

//Check
Route::get('/check', function ()
{
    return Reading::checkAll();
});

//* CRUD {reading}
Route::controller(ReadingsController::class)->group(function ()
{
    Route::post('/readings', 'store');
    Route::get('/readings', 'index');
    Route::get('/readings/atmosphere', 'atmosphere');
    Route::get('/readings/{uuid:uuid}', 'show');
    Route::put('/readings/{uuid:uuid}', 'update');
    Route::delete('/readings/{uuid:uuid}', 'destroy');
});

//TODO Copy crud functionality for the other models
//* Create {sensors}
Route::post('/sensors', function () {
    if (!isset($_REQUEST['type']) || !isset($_REQUEST['uuid'])) {
        return;
    }
    $uuid = $_REQUEST['uuid'];
    $type = strtolower($_REQUEST['type']);
    $sensor = Sensors::find(Sensors::getSensorsfromUUID($uuid, $type)->id) ?? new Sensors;
    // $sensor = new Sensors;
    $sensor->type = $type;
    $sensor->uuid = $uuid;
    $sensor->plants_id = isset($_REQUEST['plants_id']) ? intval($_REQUEST['plants_id']) : 0;
    $sensor->relay_pin = isset($_REQUEST['relay_pin']) ? intval($_REQUEST['relay_pin']) : 0;
    if ($_REQUEST['ipaddr']) {
        $sensor->ipaddr = isset($_REQUEST['ipaddr']) ? $_REQUEST['ipaddr'] : 0;
    }
    $sensor->save();
});

//* Create {plant}
Route::post('/plants', function () {
    if (!isset($_REQUEST['name']) || !isset($_REQUEST['plant_date']) || !isset($_REQUEST['harvest_date']) || !isset($_REQUEST['location']));
    $plant = new Plants;
    $plant->name = $_REQUEST['name'];
    $plant->plant_date = $_REQUEST['plant_date'];
    $plant->harvest_date = $_REQUEST['harvest_date'];
    $plant->location = $_REQUEST['location'];
    $plant->save();
    return $plant->id ?: 'Failed';
});

/**
 * * GET /api/
 *
 * @return array base amount of readings, 15 by default
 */
Route::get('/', function () {
    return Reading::getReadingsByTS(15);
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

/**
 * * GET /api/sensors/name
 *
 * Return alias based off the uuid given
 *
 * @return array all active uuids
 */
Route::get('/sensors/name/{uuid}', function ($uuid) {
    return Sensors::getNameFromUUID($uuid);
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

//* Update {sensor}

//* Update {plant}

//* Delete {sensor}

//* Delete {plant}
