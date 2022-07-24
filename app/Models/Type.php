<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Type extends Model
{
    use HasFactory;

    protected $table = 'types';

    protected $fillable = [
        'alias'
    ];

    /**
     * Returns the sensors belonging to this Type
     *
     * @return BelongsToMany
     */
    public function sensors(): BelongsToMany
    {
        return $this->belongsToMany(Sensor::class);
    }
}
