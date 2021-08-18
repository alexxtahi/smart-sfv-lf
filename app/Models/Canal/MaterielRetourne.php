<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;

class MaterielRetourne extends Model
{
   protected $fillable = ['retour_article_id','materiel_id','quantite_vendue','quantite','prix_unitaire'];
   
   public function materiel() {
        return $this->belongsTo('App\Models\Canal\Materiel');
    }
}
