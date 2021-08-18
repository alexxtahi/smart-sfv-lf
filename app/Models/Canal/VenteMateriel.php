<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VenteMateriel extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['numero_facture','agence_id','client_id','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_vente'];
    
    public function agence() {
        return $this->belongsTo('App\Models\Canal\Agence');
    }
}
