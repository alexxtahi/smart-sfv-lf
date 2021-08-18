<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BonCommande extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['numero_bon','fournisseur_id','livrer','accompteFournisseur','scan_facture_commande','updated_by','deleted_by','created_by'];
    
    protected $dates = ['deleted_at','date_bon_commande','date_reception_commande'];
    
    public function fournisseur() {
        return $this->belongsTo('App\Models\Parametre\Fournisseur');
    }
}
