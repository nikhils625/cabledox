<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinalCheckSheetQuestionnaire extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [ 
        'user_id',
        'client_id',
        'question_name',
        'status' 
    ];

     /**
     * @param array $inputs
     * @param null $id
     * @param null $adminArr
     * @return \Illuminate\Validation\Validator
     */
    public function validateFinalCheckSheet($inputs, $id = null)
    {
        $inputs = array_filter($inputs);

         if ($id) {
            $rules = [
                'question_name' => 'required|unique:final_check_sheet_questionnaires,question_name,' . $id . ',id,deleted_at,NULL',
            ];
        } else {
            $rules = [
                'question_name' => 'required|unique:final_check_sheet_questionnaires,question_name,NULL,id,deleted_at,NULL',
            ];
        }

        return \Validator::make($inputs, $rules);
    }
}
 