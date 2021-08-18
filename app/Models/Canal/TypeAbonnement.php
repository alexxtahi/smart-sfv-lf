<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeAbonnement extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['libelle_type_abonnement','prix_type_abonnement', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
    
//    public function abonnements() {
//        return $this->hasMany('App\Models\Canal\Abonnement');
//    }
}
