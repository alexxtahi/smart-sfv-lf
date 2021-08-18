<?php

namespace App\Models\Canal;

use Illuminate\Database\Eloquent\Model;

class Rebi extends Model
{
    protected $fillable = ['concerne','abonnement_id','reabonnement_id','vente_materiel_id', 'demande_approvi_canal_id','caution_agence_id', 'montant_recharge','montant_recharge_agence','montant_recharge_client', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_operation'];
}
