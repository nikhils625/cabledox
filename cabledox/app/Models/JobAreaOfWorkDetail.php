<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAreaOfWorkDetail extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_area_of_work_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;

    protected $fillable = [
        'job_cable_id',
        'installed',
        'checklist',
        'test_result',
        'installed_by',
        'installed_at',
        'checklist_by',
        'checklist_at',
        'test_result_by',
        'test_result_at',
    ];

    /*
        relationship with job cable model
    */
    public function jobcables() {
        return $this->belongsTo(JobCable::class);
    }
}