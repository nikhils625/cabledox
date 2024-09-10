<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobDrawing extends Model
{
    use HasFactory;

    use SoftDeletes; 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = ' job_drawings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'client_id',
        'job_id',
        'drawing_name',
        'drawing_type',
        'status',
        'created_at',
        'updated_at',
    ];


    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
    */
    public function validateJobDrawing($inputs,  $id = null)
    {
        $inputs = array_filter($inputs);

        $rules = [
           'drawing_name .*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];

        return \Validator::make($inputs, $rules);
    }   
} 
 