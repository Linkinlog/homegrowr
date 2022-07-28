<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Returns sensors belonging to this plant
     *
     * @return BelongsToMany
     */
    public function sensors(): BelongsToMany
    {
        return $this->belongsToMany(Sensor::class);
    }

    /**
     * Returns the location this plant belongs to
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(PlantLocation::class);
    }

    /**
     * Returns the readings belonging to this plant
     *
     * @return BelongsToMany
     */
    public function readings(): BelongsToMany
    {
        return $this->belongsToMany(Reading::class);
    }
}
