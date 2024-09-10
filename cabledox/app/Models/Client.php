<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_phone',
        'company_logo',
        'no_of_jobs_allocated',
        'status'
    ];
      
    /**
     * @param array $inputs
     * @param null $id
     * @param null $adminArr
     * @return \Illuminate\Validation\Validator
     */
    public function validateClients($inputs, $id = null)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'company_name' => 'required|string|max:255',
            'no_of_jobs_allocated'  => 'required',
            'contact_person_name'    => 'required|string|max:255',
        ];

        if ($id) {
            $rules += [
                'company_phone' => 'required|digits:10|numeric|unique:clients,company_phone,' . $id . ',id,deleted_at,NULL',
                'company_email' => 'required|email|unique:clients,company_email,' . $id . ',id,deleted_at,NULL',
                'phone' => 'required|digits:10|numeric|unique:users,phone,' . $inputs['user_id'] . ',id,deleted_at,NULL',
                'email' => 'required|email|unique:users,email,' . $inputs['user_id'] . ',id,deleted_at,NULL',
                'company_logo' =>'image|mimes:jpeg,png,jpg|max:2048',

            ];
        } else {
            $rules += [
                'company_phone' => 'required|digits:10|numeric|unique:clients,company_phone,NULL,id,deleted_at,NULL',
                'company_email' => 'required|email|unique:clients,company_email,NULL,id,deleted_at,NULL',
                'phone' => 'required|digits:10|numeric|unique:users,phone,NULL,id,deleted_at,NULL',
                'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL',
                'company_logo' =>'required|image|mimes:jpeg,png,jpg|max:2048',
            ];
        }
        return \Validator::make($inputs, $rules);
    }

    /* relation with users*/
    public function users(){
        return $this->hasOne(User::class ,'client_id' ,'id');
    }
}