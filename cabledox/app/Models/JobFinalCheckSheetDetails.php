<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobFinalCheckSheetDetails extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'job_final_check_sheet_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'final_check_sheet_id',
        'fcs_questionnaire_id',
        'completed',
        'comment',
        'created_at',
        'updated_at',
    ];

    /*
        relationship with Job Final CheckSheet model
        to get data of job final check sheet
    */
    public function jobFinalCheckSheet() {
        return $this->belongsTo(JobFinalCheckSheet::class, 'final_check_sheet_id', 'id');
    }

    /*
        relationship with final check sheet questionnaire model
        to get data of final check sheet questionnaire
    */
    public function finalCheckSheetQuestionnaire(){
        return $this->belongsTo(FinalCheckSheetQuestionnaire::class, 'fcs_questionnaire_id', 'id');
    }
}