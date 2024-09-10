<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CableType extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'cable_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cable_name',
        'status',
        'create_at',
        'updated_at',
    ];

    /**
     * The attributes that should be muted to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /* relation with users*/
    public function cableMaster(){
        return $this->hasOne(CableMaster::class ,'cable_type_id', 'id');
    }
}
