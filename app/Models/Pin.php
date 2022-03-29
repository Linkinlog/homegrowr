<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_pins';

    protected $fillable = [
        'pin',
        'alias',
        'plant_name',
        'uuid'
    ];

    public static function getPinFromUUID($uuid, $pin = null)
    {
        $self = new self;

        $id = Pin::select('wp_pins.id')
        ->leftJoin('wp_plants', function ($join) {
            $join->on('wp_pins.plant_name', '=', 'wp_plants.name')->orOn('wp_pins.plant_name', '=', 'wp_plants.location');
            $join->on('wp_plants.harvest_date', '=', DB::raw("'0000-00-00'"));
        })
        ->where('uuid', $uuid)
        ->when($pin, function($query, $pin) {
            $query->where('pin', $pin);
        })
        ->value('id');

        $self->id = $id;
        
        return $self;
    }

    public function get50()
    {
        return Pin::where('id', $this->id)->readings()->limit(50)->get();
    }

    public function get50Formatted()
    {
        return [
            $this->id => $this->get50()
        ];
    }

    public static function getUUIDs()
    {
        return Pin::select('uuid')
        ->distinct()
        ->pluck('uuid');
    }

    public function readings()
    {
        return $this->hasMany(Reading::class);
    }
}
