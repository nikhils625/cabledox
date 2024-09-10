<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class JobFinalCheckSheet extends Model
{
    use HasFactory;
    // use SoftDeletes;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_final_check_sheets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'job_id',
        'cable_location_id',
        'cable_id',
        'upload_image',
        'inspector_name',
        'inspector_signature',
        'inspector_signature_date',
        'pc_inspector_name',
        'pc_inspector_signature',
        'pc_inspector_signature_date',
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
    // protected $dates = ['deleted_at'];

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function job(){
        return $this->belongsTo(Job::class);
    }

    /*
        relationship with Job Cable model
        to get data of JobCable
    */
    public function jobCable(){
        return $this->belongsTo(JobCable::class, 'cable_id', 'id');
    }

    /*
        relationship with job cable locations
        to get data of job cable locations
    */
    public function jobCableLocations() {
        return $this->belongsTo(JobCableLocation::class, 'cable_location_id');
    }

    /*
        relationship with job final check sheet details
        to get data of job final check sheet details
    */
    public function jobFinalCheckSheetDetails() {
        return $this->hasMany(JobFinalCheckSheetDetails::class, 'final_check_sheet_id', 'id');
    }

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    public function validateJobFinalCheckSheet($inputs)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'cable_id'                 => 'required',
            'area_inspected'           => 'required',
            'check_sheet.completed.*'  => 'required|numeric',
            'check_sheet.comment.*'    => 'required',
            'inspector_name'           => 'required',
            'inspector_signature_date' => 'required|date',
            'pc_inspector_name'        => 'required',
            'inspector_signature_date' => 'required|date',
        ];

        if(empty($inputs['old_inspector_signature'])) {
            $rules['inspector_signature'] = 'required';
        }

        if(empty($inputs['old_pc_inspector_signature'])) {
            $rules['pc_inspector_signature'] = 'required';
        }

        $rules['upload_image'] = 'image|mimes:jpeg,png,jpg|max:2048';

        $messages = [
            'inspector_name.required'           => 'Inspector name field is required.',
            'inspector_signature.required'      => 'Inspector signature field is required.',
            'inspector_signature_date.required' => 'Inspector signature date field is required.',
            'inspector_signature_date.date'     => 'Inspector date is not a valid date.',
            'pc_inspector_name.required'        => 'Principle contractor inspector name field is required.',
            'pc_inspector_signature.required'   => 'Principle contractor Inspector signature field is required.',
            'inspector_signature_date.required' => 'Principle contractor inspector signature date field is required.', 
            'inspector_signature_date.date'     => 'Principle contractor inspector signature date is not a valid date.', 
        ];

        return \Validator::make($inputs, $rules);
    }
}
