<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'statuses';

    /**
     * The columns we want to allow to be edited
     *
     * @var array
     */
    protected $fillable = [
        'status'
    ];

    /**
     * Defines the relationship between the readings and their status
     *
     * @return void | relationship
     */
    public function readings()
    {
        return $this->hasMany(Reading::class);
    }
}
