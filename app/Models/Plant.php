<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plants extends Model
{
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plants';

    protected $fillable = [
        'name',
        'plant_date',
        'harvest_date',
        'location'
    ];

}
