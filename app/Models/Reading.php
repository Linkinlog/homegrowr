<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Sensors;
use Carbon\Carbon;

class Reading extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'readings';

    protected $fillable = [
        'sensors_id',
        'value',
        'status_id'
    ];

    public static function atmosphere()
    {
        $sensors = Sensors::where('type', 'Humidity')->orWhere('type', 'Temperature')->get()->pluck('id');

        $readings = Reading::join('sensors', function ($join)
            {
            $join->on('readings.sensors_id', 'sensors.id');
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
        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : $type = NULL;
        $reading = Reading::select('value', 'updated_at', 'type')
            ->leftJoin('sensors', function ($join) use ($uuid) {
                $join->on('sensors.id', 'readings.sensors_id');
                $join->on('sensors.UUID', DB::raw("'$uuid'"));
            })
            ->when($type, function ($query) use ($type){
                $query->where('type',$type);
            }, function ($query) {
                $query->where('type', 'Temperature');
                $query->orWhere('type', 'Humidity');
            })
            ->orderByDesc('updated_at')
            ->limit(10);
        $reading = $reading->get()->toJson();
        return "{\"$uuid\":$reading}";
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('Y-m-d H:i');
    }
    public function getUpdatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('Y/m/d');
    }

    public static function checkAll()
    {
        $uuids = Sensors::select('uuid')
            ->distinct()
            ->pluck('uuid');
        foreach ($uuids as $uuid) {
            echo Self::check($uuid);
        }
    }

    //TODO Remove the user-friendly-ness of this route once we can see it with Vue/Chartjs
    public static function check($uuid)
    {
        if (count($readings = Reading::getReadingsByUUID($uuid)) < 1) {
            return false;
        }
        $setOn = null;
        $pin = null;
        $test = count($readings);

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

    }
}
