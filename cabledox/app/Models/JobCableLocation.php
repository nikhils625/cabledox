<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCableLocation extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string 
     */
    // protected $table = 'job_cable_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_cable_id',
        'location_id',
        'location_type', // 0= to location, 1 = from location
        'created_at',
        'updated_at',
    ];

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function jobCable() {
        return $this->belongsTo(JobCable::class);
    }

    /* relation with job locations*/
    public function jobLocation() {
        return $this->belongsTo(JobLocation::class, 'location_id', 'id');
    }
}