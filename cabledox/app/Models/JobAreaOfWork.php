<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAreaOfWork extends Model
{
    use HasFactory;

    use SoftDeletes;

     /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_area_of_works';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'job_id',
        'area',
        'area_status',
        'created_at',
        'updated_at',
    ];
}