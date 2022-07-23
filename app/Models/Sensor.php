<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Sensor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sensors';

    protected $fillable = [
        'alias',
        'plant_id',
        'uuid',
        'ipaddr',
        'location_id'
    ];

    use HasFactory;

    public static function getSensorFromUUID($uuid, $type = null)
    {
        $self = new self;

        $id = Self::select('sensors.id')
            ->when($type == 'soil', function ($query) {
                $query->leftJoin('plants', function ($join) {
                    $join->on('sensors.plant_id', '=', 'plants.id');
                    $join->on('plants.harvest_date', '=', DB::raw("'0000-00-00'"));
                });
            })
            ->where('uuid', $uuid)
            ->when($type, function ($query, $type) {
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
        return $this->readings()->orderBy('readings.updated_at', 'desc')->limit(50)->get();
    }

    public static function getUUIDsAndName($type = NULL)
    {
        $sensor = Sensor::select('uuid', 'alias')
            ->when(isset($type), function ($query) use ($type) {
                if ($type == 'atmosphere') {
                    $query->where('type', 'temperature');
                    $query->orWhere('type', 'humidity');
                } elseif ($type == 'camera') {
                    $query->select('ipaddr', 'alias');
                    $query->where('type', 'camera');
                }
            })
            ->distinct();

        return
            $sensor->get();
    }

    public static function getUUIDs(): Sensor
    {
        return Sensor::select('uuid')
            ->distinct()
            ->pluck('uuid');
    }

    /** @var Reading $readings */
    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }

    /** @var Plant $plants */
    public function plants(): BelongsToMany
    {
        return $this->belongsToMany(Plant::class);
    }

    /** @var Location $locations */
    public function locations(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @var Type $types */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'type_sensor');
    }

    /** @var Relay $relays */
    public function relays(): BelongsToMany
    {
        return $this->belongsToMany(Relay::class);
    }
}
