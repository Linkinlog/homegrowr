<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plant extends Model
{
    
    /**
     * The table associated with the model.
     * 
     * id	name	plant_date	location	harvest_date	created_at	updated_at
     *
     * @var string
     */
    protected $table = 'plants';

    use HasFactory;

    protected $fillable = [
        'name',
        'plant_date',
        'harvest_date',
        'location_id'
    ];

    public function sensors(): BelongsToMany
    {
        return $this->belongsToMany(Sensor::class);
    }

    public function locations(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

}
