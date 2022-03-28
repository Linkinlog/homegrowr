<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Pin;


class Reading extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wp_readings';

    protected $fillable = [
        'pin_id',
        'value'
    ];

    public static function atmosphere()
    {
        $pins = Pin::where('alias', 'Humidity')->orWhere('alias', 'Temperature')->get()->pluck('id');

        $readings = Reading::join('wp_pins', function ($join)
            {
                $join->on('wp_readings.pin_id', 'wp_pins.id');
            })
            ->where('wp_pins.alias', 'Humidity')
            ->orWhere('wp_pins.alias', 'Temperature')
            ->orderByDesc('TS')
            ->limit('5')
            ->get();

        return $readings;

        // $readings = Reading::select('value', 'TS', 'alias')
        //     ->join('wp_pins', function($join)
        //     {
        //         $join->on('wp_pins.alias', '=', DB::raw("'Humidity'"))
        //         ->orOn('wp_pins.alias', '=', DB::raw("'Temperature'"));
        //     })
        //     ->orderByDesc('TS')
        //     ->limit(45)
        //     ->get();
        // return $readings;
        // return 'here';
    }
}
