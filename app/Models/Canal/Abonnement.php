<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abonnement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['numero_abonnement','numero_decodeur','adresse_decodeur','duree', 'payement_abonnement', 'payement_equipement', 'type_abonnement_id','agence_id', 'abonne_id','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_debut','date_abonnement'];
    
    public function abonne() {
        return $this->belongsTo('App\Models\Canal\Abonne');
    }
    public function type_abonnement() {
        return $this->belongsTo('App\Models\Canal\TypeAbonnement');
    }
    public function agence()
    {
        return $this->belongsTo('App\Models\Canal\Agence');
    }
    public function option_canals()
    {
        return $this->belongsToMany('App\Models\Canal\OptionCanal');
    }
}
