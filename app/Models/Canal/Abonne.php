<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abonne extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['civilite','full_name_abonne', 'type_piece_id', 'numero_piece', 'nation_id', 'localite_id', 'adresse_abonne', 'code_postal', 'email_abonne', 'contact1', 'contact2', 'contact_conjoint','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_naissance_abonne'];
    
    public function localite() {
        return $this->belongsTo('App\Models\Canal\Localite');
    }
    public function nation() {
        return $this->belongsTo('App\Models\Parametre\Nation');
    }
    public function type_piece() {
        return $this->belongsTo('App\Models\Canal\TypePiece');
    }
}
