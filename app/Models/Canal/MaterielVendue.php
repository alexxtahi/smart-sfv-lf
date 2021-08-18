<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;

class MaterielVendue extends Model
{
    protected $fillable = ['materiel_id','prix','quantite','retourne', 'vente_materiel_id'];
    
    public function materiel() {
        return $this->belongsTo('App\Models\Canal\Materiel');
    }
}
