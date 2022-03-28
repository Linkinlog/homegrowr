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

    public function getPinfromUUID()
    {
        return Pin::select('wp_pins.id')
        ->leftJoin('wp_plants', function ($join) {
            $join->on('wp_pins.plant_name', '=', 'wp_plants.name')->orOn('wp_pins.plant_name', '=', 'wp_plants.location');
            $join->on('wp_plants.harvest_date', '=', DB::raw("'0000-00-00'"));
        })
        ->where('uuid', $this->uuid)
        ->where('pin', $this->pin)
        ->value('id');
    }
}
