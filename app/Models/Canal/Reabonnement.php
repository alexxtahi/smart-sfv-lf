<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reabonnement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['abonnement_id','duree','montant_reabonnement','agence_id','type_abonnement_id', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_debut','date_reabonnement'];
    
    public function abonnement() {
        return $this->belongsTo('App\Models\Canal\Abonnement');
    }
    public function agence()
    {
        return $this->belongsTo('App\Models\Canal\Agence');
    }
    public function type_abonnement() {
        return $this->belongsTo('App\Models\Canal\TypeAbonnement');
    }
    public function option_canals()
    {
        return $this->belongsToMany('App\Models\Canal\OptionCanal');
    }
}
