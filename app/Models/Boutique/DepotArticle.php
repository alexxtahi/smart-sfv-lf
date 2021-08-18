<?php

namespace App\Models\Boutique;

use Illuminate\Database\Eloquent\Model;

class DepotArticle extends Model
{
    protected $fillable = ['quantite_disponible', 'article_id','approvisionnement_id', 'depot_id', 'prix_vente','promotion','unite_id', 'updated_by', 'deleted_by', 'created_by'];
    
    protected $dates = ['deleted_at','date_peremption','date_debut_promotion','date_fin_promotion'];
    public function depot() {
        return $this->belongsTo('App\Models\Parametre\Depot');
    }
    public function unite() {
        return $this->belongsTo('App\Models\Parametre\Unite');
    }
    public function article() {
        return $this->belongsTo('App\Models\Parametre\Article');
    }
    public static function getArticlesDepot($depot){
        $articles =  DepotArticle::where('depot_id',$depot)
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->select('depot_articles.quantite_disponible','articles.description_article','unites.libelle_unite')
                    ->get();
        return $articles;
    }
}
