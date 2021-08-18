<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SousCategorie extends Model
{
     use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['libelle_sous_categorie','categorie_id', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function articles() {
        return $this->hasMany('App\Models\Parametre\Article');
    }
    public function categorie() {
        return $this->belongsTo('App\Models\Parametre\Categorie');
    }
}
