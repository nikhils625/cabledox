<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestParameter extends Model
{
    use HasFactory;

    use SoftDeletes;
    
    protected $fillable = [
        'user_id', 
        'client_id',
        'parameter_name',
        'status' 
    ];


    /**
     * @param array $inputs
     * @param null $id
     * @param null $adminArr
     * @return \Illuminate\Validation\Validator
     */
    public function validateTestParameter($inputs, $id = null)
    {
        $inputs = array_filter($inputs);

        if ($id) {
            $rules = [
                'parameter_name' => 'required|unique:test_parameters,parameter_name,' . $id . ',id,deleted_at,NULL',
            ];
        } else {
            $rules = [
                'parameter_name' => 'required|unique:test_parameters,parameter_name,NULL,id,deleted_at,NULL',
            ];
        }
        return \Validator::make($inputs, $rules);
    }
}
 