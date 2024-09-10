<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class JobUser extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'job_id',
        'user_id',
    ];

    /*
        relationship with job
        to get data
    */
    public function jobs(){
        return $this->belongsTo(Job::class);
    }
}
