<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Sensors;


class Reading extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'readings';

    protected $fillable = [
        'sensor_id',
        'value'
    ];

    public static function atmosphere()
    {
        $sensors = Sensors::where('type', 'Humidity')->orWhere('type', 'Temperature')->get()->pluck('id');

        $readings = Reading::join('sensors', function ($join)
            {
            $join->on('readings.sensor_id', 'sensors.id');
            })
            ->where('sensors.type', 'Humidity')
            ->orWhere('sensors.type', 'Temperature')
            ->orderByDesc('TS')
            ->limit('5')
            ->get();

        return $readings;
    }

    public static function getReadingsByTS($limit)
    {
        return Reading::orderByDesc('TS')
        ->limit($limit)
        ->get();
    }

    public static function getReadingsByUUID($uuid)
    {
        return Reading::select('value', 'type', 'relay_pin', 'alias')
        ->leftJoin('sensors', function ($join) use ($uuid) {
            $join->on('sensors.id', 'readings.sensors_id');
            $join->on('sensors.UUID', DB::raw("'$uuid'"));
        })
            ->where('type', 'Temperature')
            ->orWhere('type', 'Humidity')
        ->orderByDesc('TS')
        ->limit(2)
        ->get()
        ->unique('type');
    }
}
