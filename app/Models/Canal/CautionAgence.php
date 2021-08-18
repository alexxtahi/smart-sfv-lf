<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CautionAgence extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['deposant','reference_versement','montant_depose','type_caution_id','agence_id', 'moyen_reglement_id','recu_versement','confirmer', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_versement'];
    
    public function type_caution() {
         return $this->belongsTo('App\Models\Canal\TypeCaution');
    }
    public function moyen_reglement() {
         return $this->belongsTo('App\Models\Parametre\MoyenReglement');
    }
    public function agence() {
         return $this->belongsTo('App\Models\Canal\Agence');
    }
}
