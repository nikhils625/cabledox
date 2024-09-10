<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CableMasterCoreDetails extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'cable_master_core_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cable_master_id',
        'core_name',
        'core_index',
        'wire_index',
        'create_at',
        'updated_at',
    ];

    /**
     * The attributes that should be muted to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*
        relationship with cable master model
        to get data of Cable Master
    */
    public function cableMaster() {
        return $this->belongsTo(CableMaster::class);
    }
}