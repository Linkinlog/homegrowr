<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sensors extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sensors';

    protected $fillable = [
        'sensors',
        'alias',
        'plant_id',
        'uuid'
    ];

    public static function getSensorsFromUUID($uuid, $type = null)
    {
        $self = new self;

        $id = Sensors::select('sensors.id')
        ->leftJoin('plants', function ($join) {
            $join->on('sensors.plant_id', '=', 'plants.name')->orOn('sensors.plant_id', '=', 'plants.location');
            $join->on('plants.harvest_date', '=', DB::raw("'0000-00-00'"));
        })
        ->where('uuid', $uuid)
        ->when($type, function($query, $type) {
            $query->where('type', $type);
        })
        ->value('id');
        // ->get();

        $self->id = $id;
        
        return $self;
    }

    public function get50()
    {
        return $this->readings()->orderBy('readings.TS', 'desc')->limit(50)->get();
    }

    public static function getUUIDs()
    {
        return Sensors::select('uuid')
        ->distinct()
        ->pluck('uuid');
    }

    public function readings()
    {
        return $this->hasMany(Reading::class);
    }
}
