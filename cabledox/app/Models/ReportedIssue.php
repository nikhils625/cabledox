<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReportedIssue extends Model
{

    use HasFactory;         

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'reported-issue';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'job_id',
        'location_id',
        'priority',      
        'description',
        'created_by' ,
        'created_date', 
        'status',   
        'created_at',
        'updated_at',
    ];

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
    */
    public function validateReportIssue($inputs)
    {
        $inputs = array_filter($inputs, 'strlen');

        $rules = [
            'location_id' => 'required',
            'priority'    => 'required|min:0',
            'description' => 'required',
        ];

        return \Validator::make($inputs, $rules);
    }
}
