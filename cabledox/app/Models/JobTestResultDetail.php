<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTestResultDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 
        'job_cable_id',
        'test_parameter_id',
        'output',
        'created_at',
        'updated_at' 
    ];


    /**
     * @param array $inputs
     * @param null $id
     * @param null $adminArr
     * @return \Illuminate\Validation\Validator
     */
    public function validateJobTestResult($inputs)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'output.*' => 'required',
        ];

        return \Validator::make($inputs, $rules);
    }
}