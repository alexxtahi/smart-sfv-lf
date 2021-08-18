<?php

namespace App\Models\Parametre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     
    protected $fillable = ['description_article','stockable','taille_id', 'categorie_id', 'prix_achat_ttc','unite_id', 'prix_vente_ttc_base', 'quantite_en_stock', 'sous_categorie_id', 'code_barre', 'reference_interne', 'rayon_id', 'rangee_id', 'param_tva_id', 'taux_airsi_achat', 'taux_airsi_vente', 'poids_net', 'poids_brut', 'stock_mini', 'stock_max', 'volume', 'prix_vente_en_gros_base', 'prix_vente_demi_gros_base','prix_pond_ttc','image_article', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at'];
    
    public function depot_article() {
        return $this->hasMany('App\Models\Boutique\DepotArticle');
    }
    public function categorie() {
        return $this->belongsTo('App\Models\Parametre\Categorie');
    } 
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    } 
    public function taille() {
        return $this->belongsTo('App\Models\Parametre\Taille');
    } 
    public function sous_categorie() {
        return $this->belongsTo('App\Models\Parametre\SousCategorie','sous_categorie_id');
    }

    public function rayon() {
        return $this->belongsTo('App\Models\Parametre\Rayon');
    }
    public function rangee() {
        return $this->belongsTo('App\Models\Parametre\Rangee');
    }
    public function param_tva() {
        return $this->belongsTo('App\Models\Parametre\ParamTva');
    }
    public function airsi_achat() {
        return $this->belongsTo('App\Models\Parametre\ParamTva','taux_airsi_achat');
    }
    public function airsi_vente() {
        return $this->belongsTo('App\Models\Parametre\ParamTva','taux_airsi_vente');
    }
    public function fournisseurs()
    {
        return $this->belongsToMany('App\Models\Parametre\Fournisseur');
    }
}
