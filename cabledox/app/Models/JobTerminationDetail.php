<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTerminationDetail extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_termination_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'termination_id',
        'cable_master_detail_id',
        'core_id',
        'termination_location',
        'created_at',
        'updated_at',
    ];

    /*
        relationship with job termination model
        to get data of Job Termination
    */
    public function jobsTermination() {
        return $this->belongsTo(JobTermination::class, 'termination_id', 'id');
    }

    /*
        relationship with cable master core details model
        to get data of Cable Master Core Details
    */
    public function cableMasterCoreDetail(){
        return $this->belongsTo(CableMasterCoreDetails::class, 'cable_master_detail_id', 'id');
    }
}