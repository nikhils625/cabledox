<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory;
 
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'job_number',
        'site_name',
        'post_code',
        'address',
        'closed_by',
        'closed_at',
        'is_pdf_generated',
        'status',
    ];

    /* relation with job users*/
    public function jobUsers(){
        return $this->hasMany(JobUser::class);
    }

    /* relation with job cables*/
    public function jobCables(){
        return $this->hasMany(JobCable::class);
    }

    /* relation with job single line drawings*/
    public function jobSingleLineDrawings(){
        return $this->hasMany(JobDrawing::class)->where('drawing_type', 1);
    }

    /* relation with job schematic drawings*/
    public function jobSchematicLineDrawings(){
        return $this->hasMany(JobDrawing::class)->where('drawing_type', 2);
    }

    /* relation with job location drawings*/
    public function jobLocationLineDrawings(){
        return $this->hasMany(JobDrawing::class)->where('drawing_type', 3);
    }

    /* relation with job Company/Client*/
    public function jobCompany(){
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * @param array $inputs
     * @param null $id
     * @param null $adminArr
     * @return \Illuminate\Validation\Validator
     */
    public function validateJob($inputs, $id = null, $clientId = null)
    {
        $inputs = array_filter($inputs);
     
        $rules = [
            'user_id'   =>'required',
            'site_name' => 'required',
            'post_code' => 'required',
            'address'   =>'required',
        ];

        if ($clientId) {
            if($id) {
                $rules += [
                    'job_number' => 'required|numeric|unique:jobs,job_number,'.$id.',id,client_id,'.$clientId.',deleted_at,NULL',
                ];
            } else {
                $rules += [
                    'job_number' => 'required|numeric|unique:jobs,job_number,null,id,client_id,'.$clientId.',deleted_at,NULL',
                ];
            }
        }
        return \Validator::make($inputs, $rules);
    }
}