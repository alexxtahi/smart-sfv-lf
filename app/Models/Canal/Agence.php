<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agence extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['libelle_agence', 'numero_identifiant_agence', 'contact_agence', 'localite_id', 'adresse_agence', 'nom_responsable', 'contact_responsable', 'email_agence', 'numero_cc', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function localite() {
         return $this->belongsTo('App\Models\Canal\Localite');
    }
}
