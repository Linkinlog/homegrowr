<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\Sensors;
use Illuminate\Http\Request;



class ReadingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (isset($request['limit'])) {
            $limit = $request['limit'];
        } else {
            $limit = 10;
        }
        return Reading::getReadingsByTS($limit);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!isset($request['value']) or !isset($request['type']) or !isset($request['uuid'])) {
            return 0;
        }
        date_default_timezone_set("America/New_York");

        $uuid = $request['uuid'];
        $type = strtolower($request['type']);

        $plants_id = $type !== 'soil' ? NULL : (isset($request['plants_id']) ? $request['plants_id'] : 0);
        $value = $request['value'];

        $status = 1;

        if (intval($value) == 0) {
            $value = 0;
            $status = 2;
        }

        $sensor_id = Sensors::getSensorsfromUUID($uuid, $type)->id;

        if (!$sensor_id || intval($sensor_id) == 0) {
            $sensor_id = new Sensors;
            $sensor_id->type = $type;
            $sensor_id->uuid = $uuid;
            $sensor_id->plants_id = $plants_id;
            $sensor_id->save();
            $sensor_id = $sensor_id->id;
        }

        $reading = new Reading;
        $reading->sensors_id = $sensor_id;
        $reading->value = $value;
        $reading->status_id = $status;
        $result = $reading->save();

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  string $uuid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $uuid)
    {
        return Reading::getReadingsByUUID($uuid, $request->has('temperature') ?: true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reading  $reading
     * @return \Illuminate\Http\Response
     */
    public function edit(Reading $reading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reading  $reading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reading $reading)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reading  $reading
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reading $reading)
    {
        //
    }
    /**
     * Show the readings relating to humidity and temperature
     *
     * @return \App\Models\Reading 
     */
    public function atmosphere()
    {
        return Reading::atmosphere();
    }
}
