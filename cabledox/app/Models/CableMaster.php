<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CableMaster extends Model
{
    use HasFactory;
    use SoftDeletes;    

    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'cable_masters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'cable_type_id',
        'cores',
        /*'cable_is_pair_triple_quad',*/
        'no_of_pair_triple_quad',
        'status',
        'create_at',
        'updated_at',
    ];

    /**
     * The attributes that should be muted to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /*
        relationship with cable type model
        to get data of Cable Type
    */
    public function cableType(){
        return $this->belongsTo(CableType::class, 'cable_type_id', 'id');
    }

    /*
        relationship with cable master core details model
        to get data of Cable Master Core Details
    */
    public function cableMasterCoreDetails() {
        return $this->hasMany(CableMasterCoreDetails::class);
    }

    /**
     * @param array $inputs
     * @param null $id
     * @return \Illuminate\Validation\Validator
     */
    public function validateCableMaster($inputs, $id = null)
    {
        $inputs = array_filter($inputs);

        $rules = [
            'cable_type_id'              => 'required',
            /*'cable_is_pair_triple_quad'  => 'in:2,3,4',*/
            'cores'                      => 'required|numeric|min:1',
            // 'no_of_pair_triple_quad'     => 'required|numeric|min:1',
            'core_name.*'                => 'required',
        ];

        return \Validator::make($inputs, $rules);
    }
}