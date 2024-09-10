<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;

    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array 
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'user_profile',
        'role_id',
        'password',
        'status',
        'create_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*protected $casts = [
        'email_verified_at' => 'datetime',
    ];*/

    /*
        relationship with client
        to get data of client's contact person
    */
    public function clients(){
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    public function validateUsers($inputs, $id = null)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
            'role_id'    => 'required',
        ];

        if ($id) {
            $rules += [
                // 'phone' => 'required|unique:users,phone,' . $id . ',id,deleted_at,NULL',
                'email' => 'required|unique:users,email,' . $id . ',id,deleted_at,NULL',
            ];
        } else {
            $rules += [
                // 'phone' => 'required|unique:users,phone,NULL,id,deleted_at,NULL',
                'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL',
            ];
        }

        return \Validator::make($inputs, $rules);
    }

    /**
     * @param array $inputs
     * @param null $id
     * @param null $adminArr
     * @return \Illuminate\Validation\Validator
     */
    public function validateUserProfile($inputs, $id = null)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'first_name' => 'required',
            'last_name'  => 'required',
        ];

        if ($id) {
            $rules += [
                'phone' => 'required|unique:users,phone,' . $id . ',id,deleted_at,NULL',
                'email' => 'required|unique:users,email,' . $id . ',id,deleted_at,NULL',
                'user_profile' => 'image|mimes:jpeg,png,jpg|max:2048',
            ];
        }
        return \Validator::make($inputs, $rules);
    }
}