<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reglement extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['scan_cheque','vente_id','commande_id','reste_a_payer', 'moyen_reglement_id','montant_reglement','numero_cheque_virement','caisse_ouverte_id', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_reglement'];
    
    public function caisse_ouverte() {
        return $this->belongsTo('App\Models\Boutique\CaisseOuverte');
    }
    public function moyen_reglement() {
        return $this->belongsTo('App\Models\Parametre\MoyenReglement');
    }
}
