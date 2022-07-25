<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Relay extends Model
{
    use HasFactory;

    protected $table = 'relays';

    protected $fillable = [
        'pin',
        'type_id'
    ];

    /**
     * Returns the sensors belonging to this Relay
     *
     * @return BelongsToMany
     */
    public function sensors(): BelongsToMany
    {
        return $this->belongsToMany(Sensor::class);
    }

    /**
     * Returns the type that this Relay belongs to
     *
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Relay_type::class);
    }
}
