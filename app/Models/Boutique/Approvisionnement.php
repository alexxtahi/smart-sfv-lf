<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approvisionnement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['depot_id','fournisseur_id','numero_conteneur','numero_declaration','numero_immatriculation','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_approvisionnement'];
    
    public function fournisseur() {
        return $this->belongsTo('App\Models\Parametre\Fournisseur');
    }
    public function depot() {
        return $this->belongsTo('App\Models\Parametre\Depot');
    }
}
