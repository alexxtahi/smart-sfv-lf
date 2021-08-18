<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandeApproviCanal extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['numero_demande','deposant','reference_versement','montant_depose','type_caution_id','recu_versement','approvisionne', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_demande'];
    
    public function type_caution() {
         return $this->belongsTo('App\Models\Canal\TypeCaution');
    }
}
