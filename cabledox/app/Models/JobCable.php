<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCable extends Model
{
    use HasFactory;
    use SoftDeletes; 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_cables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'job_id',
        'cable_type_id',
        'cable_id_type',
        'cable_id',
        'custom_id',
        'unique_code',
        'to',
        'from',
        'size',
        'description',
        'additional_information',
        'file_name', // Pdf Name
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
    public function job(){
        return $this->belongsTo(Job::class);
    }

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function cableType(){
        return $this->belongsTo(CableType::class, 'cable_type_id', 'id');
    }

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function cableIdType(){
        return $this->belongsTo(CableMaster::class, 'cable_id_type', 'id');
    }

    /*
        relationship with job cable locations
        to get data of job cable locations
    */
    public function jobCableLocations() {
        return $this->hasMany(JobCableLocation::class);
    }

    /*
        relationship with job cable to location
        to get data of job cable to location
    */
    public function jobCableTo() {
        return $this->hasOne(JobCableLocation::class)->where('location_type', 0);
    }

    /*
        relationship with job cable to location
        to get data of job cable to location
    */
    public function jobCableFrom() {
        return $this->hasOne(JobCableLocation::class)->where('location_type', 1);
    }

    /*
        relationship with job cable to Job Area Of Work Detail
    */
    public function areaOfWorkDetails() {
        return $this->hasOne(JobAreaOfWorkDetail::class ,'job_cable_id' ,'id');
    }

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    public function validateJobCables($inputs, $id = null, $clientId = null)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'cable_id_type'              => 'required',
            'cable_type'                 => 'required',
            'to'                         => 'required',
            'from'                       => 'required',
            // 'cores'                      => 'required',
            'size'                       => 'required|numeric',
            'description'                => 'required',
        ];

        if ($clientId) {
            if($id) {
                $rules += [
                    'cable_id'    => 'required|unique:job_cables,cable_id,'.$id.',id,client_id,'.$clientId.',deleted_at,NULL',
                    'custom_id'   => 'unique:job_cables,custom_id,'.$id.',id,client_id,'.$clientId.',deleted_at,NULL',
                    'unique_code' => 'unique:job_cables,unique_code,'.$id.',id,client_id,'.$clientId.',deleted_at,NULL',
                ];
            } else {
                $rules += [
                    'cable_id'    => 'required|unique:job_cables,cable_id,null,id,client_id,'.$clientId.',deleted_at,NULL',
                    'custom_id'   => 'unique:job_cables,custom_id,null,id,client_id,'.$clientId.',deleted_at,NULL',
                    'unique_code' => 'unique:job_cables,unique_code,null,id,client_id,'.$clientId.',deleted_at,NULL',
                ];
            }
        }

        return \Validator::make($inputs, $rules);
    }
}