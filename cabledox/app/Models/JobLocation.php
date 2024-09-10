<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobLocation extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'location_name',
        'created_by',
        'created_at',
        'updated_at',
    ];

    /* relation with users*/
    public function users() {
        return $this->hasOne(User::class ,'created_by', 'id');
    }

    /* relation with job Company/Client*/
    public function jobCompany() {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    /* relation with job cable location*/
    public function jobCableLocation() {
        return $this->hasMany(JobCableLocation::class, 'location_id', 'id');
    }
}