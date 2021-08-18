<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TypePiece extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = ['libelle_type_piece','updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
}
