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

    public $timestamps = false;

    protected $fillable = [
        'type',
        'alias',
        'plants_id',
        'uuid'
    ];

    public static function getSensorsFromUUID($uuid, $type = null)
    {
        $self = new self;

        $id = Self::select('sensors.id')
        ->when($type == 'soil', function ($query) {
            $query->leftJoin('plants', function ($join) {
                $join->on('sensors.plants_id', '=', 'plants.id');
                $join->on('plants.harvest_date', '=', DB::raw("'0000-00-00'"));
            });
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

    public static function getNameFromUUID($uuid, $type = NULL)
    {
        return Self::where('uuid', $uuid)
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->value('alias');
    }

    public function get50()
    {
        return $this->readings()->orderBy('readings.TS', 'desc')->limit(50)->get();
    }

    public static function getUUIDsAndName()
    {
        return Sensors::select('uuid', 'alias')
            ->distinct()
            ->get();
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
