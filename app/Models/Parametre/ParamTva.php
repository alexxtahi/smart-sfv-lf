<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;

class ParamTva extends Model
{
    protected $fillable = ['montant_tva', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function articles() {
        return $this->hasMany('App\Models\Parametre\Article');
    }
}
