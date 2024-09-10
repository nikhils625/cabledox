<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTermination extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_terminations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'job_id',
        'cable_id',
        'location_id',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be muted to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function jobs(){
        return $this->belongsTo(Job::class);
    }

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function cable(){
        return $this->belongsTo(CableMaster::class);
    }

    /*
        relationship with job cable locations
        to get data of job cable locations
    */
    public function jobCableLocations() {
        return $this->belongsTo(JobCableLocation::class);
    }

    /*
        relationship with job cable locations
        to get data of job cable locations
    */
    public function jobTerminationDetails() {
        return $this->hasMany(JobTerminationDetail::class, 'termination_id', 'id');
    }

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    public function validateJobTerminationdetails($inputs)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'cable_id'    => 'required',
            'location_id' => 'required',
            'termination_detail.core_id.*'              => 'required|numeric',
            'termination_detail.termination_location.*' => 'required',
        ];

        return \Validator::make($inputs, $rules);
    }
}