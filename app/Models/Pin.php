<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
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
}