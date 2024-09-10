<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistMaster extends Model
{
    use HasFactory;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'checklist_masters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'checklist_name',
        'create_at',
        'updated_at',
    ];

    /* relation with users*/
    public function users() {
        return $this->hasOne(User::class ,'user_id', 'id');
    }

    /* relation with job Company/Client*/
    public function jobCompany() {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}