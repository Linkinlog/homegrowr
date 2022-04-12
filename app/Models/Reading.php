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
        $sensors = Sensors::where('type', 'humidity')->orWhere('type', 'temperature')->get()->pluck('id');

        $readings = Reading::join('sensors', function ($join)
            {
            $join->on('readings.sensors_id', 'sensors.id');
            })
            ->where('sensors.type', 'humidity')
            ->orWhere('sensors.type', 'temperature')
            ->orderByDesc('updated_at')
            ->limit('5')
            ->get();

        return $readings;
    }

    public static function getReadingsByTS($limit)
    {
        return Reading::orderByDesc('updated_at')
        ->limit($limit)
        ->get();
    }

    public static function getReadingsByUUID($uuid, $formatted = NULL)
    {
        isset($_REQUEST['type']) ? $type = $_REQUEST['type'] : $type = NULL;
        if ($type == 'soil' && !isset($_REQUEST['plants_id'])) {
            return ['error' => 'No plants id'];
        }
        $readings = Reading::select('value', 'updated_at', 'type', 'alias', 'relay_pin')
            ->leftJoin('sensors', function ($join) use ($uuid) {
                $join->on('sensors.id', 'readings.sensors_id');
                $join->on('sensors.UUID', DB::raw("'$uuid'"));
                $join->when(isset($_REQUEST['plants_id']), function () use ($join)
                {
                    $join->on('sensors.plants_id', DB::raw($_REQUEST['plants_id']));
                });
            })
            ->when($type, function ($query) use ($type){
                $query->where('type',$type);
            }, function ($query) {
                $query->where('type', 'temperature');
                $query->orWhere('type', 'humidity');
            })
            ->orderByDesc('updated_at')
            ->limit(20);
        $readings = $readings->get();

        // Go over each reading and put it into a friendly vue(haha) for our front end charts
        $arr = [];
        foreach ($readings as $reading) {
            if (!isset($arr[$reading->type])) {
                $arr[$reading->type] = [[$reading->updated_at, $reading->value]];
            } else {
                array_push($arr[$reading->type], [$reading->updated_at, $reading->value]);
            }
        }
        
        if (!$formatted) {
            return $readings;
        } else {
            return $arr;
        }
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('Y-m-d h:i:s');
    }
    public function getUpdatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->format('Y-m-d h:i:s');
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

    public static function check($uuid)
    {
        if (count($readings = Reading::getReadingsByUUID($uuid)) < 1) {
            return false;
        }
        $setOn = null;
        $pin = null;
        $test = count($readings);

        // TODO make the limits variables
        foreach ($readings as $reading) {
            $pin = $reading->relay_pin;
            if ($reading->type == 'temperature' and $reading->value > 90  or $reading->type == 'humidity' and $reading->value > 85) {
                $setOn = True;
            } elseif ($reading->type == 'temperature' and $reading->value < 73) {
                $setOn = False;
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
