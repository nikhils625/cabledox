<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobChecklistDetail extends Model
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
        'job_id',
        'job_cable_id',
        'checklist_master_id',
        'name',
        'submit_date',
        'created_at',
        'updated_at',
    ];

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
    public function jobCable() {
        return $this->belongsTo(JobCable::class);
    }

    /* relation with job locations*/
    public function checklist() {
        return $this->belongsTo(ChecklistMaster::class, 'checklist_master_id', 'id');
    }

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    public function validateJobChecklistDetail($inputs)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'cable'                    => 'required',
            'checklist.*.checklist_master_id'  => 'required',
            'checklist.*.name'    	   => 'required',
            'checklist.*.date'    	   => 'required|date',
        ];

        return \Validator::make($inputs, $rules);
    }
}