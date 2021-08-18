<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleVente;
use App\Models\Boutique\CaisseOuverte;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\MouvementStock;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Article;
use App\Models\Parametre\Client;
use App\Models\Parametre\Depot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function response;

class ArticleVenteController extends Controller
{
    public function listeArticlesVente($vente)
    {   $montantTHT_add = 0; $montantTTTC_add=0;$montantRemise_add=0;
        $articlesVentes =  ArticleVente::with('article','unite')
                ->join('articles','articles.id','=','article_ventes.article_id')
                ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                ->select('article_ventes.*','param_tvas.montant_tva')
                ->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne', 0],['article_ventes.vente_id',$vente]])
                ->get();
        foreach ($articlesVentes as $article){
            if($article->article->param_tva_id!=0){
                $prix = round($article->prix/($article->montant_tva+1), 0);
                $montantTHT_add = $montantTHT_add + $prix*$article->quantite;
            }else{
                $montantTHT_add = $montantTHT_add + $article->prix*$article->quantite;
            }
            $montantTTTC_add = $montantTTTC_add + $article->prix*$article->quantite;
            $montantRemise_add = $montantRemise_add + $article->remise_sur_ligne;
        }
    
    $Vente = Vente::find($vente);
    if($Vente->client_id!=null){
        $vente_clients = Vente::where([['ventes.deleted_at', NULL], ['ventes.proformat', 0]])
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.acompte_facture', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                ->Where('ventes.client_id', $Vente->client_id)
                ->groupBy('article_ventes.vente_id')
                ->get();
        $client = Client::find($Vente->client_id);
        if ($vente_clients != null) {
            $credtiTotal = 0;
            foreach ($vente_clients as $credit_client) {
                $credtiTotal = $credtiTotal + ($credit_client->sommeTotale - $credit_client->acompte_facture);
            }
            $jsonData["plafond_client"] = $client->plafond_client;
            $jsonData["doit_client"] = $credtiTotal;
        }else{
            $jsonData["plafond_client"] = $client->plafond_client;
            $jsonData["doit_client"] = 0;
        }
        
    }

        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        $jsonData["montantTHT_add"] = $montantTHT_add;
        $jsonData["montantRemise_add"] = $montantRemise_add;
        $jsonData["montantTTTC_add"] = $montantTTTC_add;
        return response()->json($jsonData);
    }
    
     public function listeArticlesVenteDivers($vente)
    {    $montantTTTC_add=0;
        $articlesVentes =  ArticleVente::with('divers')
                ->select('article_ventes.*')
                ->Where([['article_ventes.deleted_at', NULL],['article_ventes.vente_id',$vente]])
                ->get();
         foreach ($articlesVentes as $article){
            $montantTTTC_add = $montantTTTC_add + $article->prix*$article->quantite;
        }
    $Vente = Vente::find($vente);
    if($Vente->client_id!=null){
        $vente_clients = Vente::where([['ventes.deleted_at', NULL], ['ventes.proformat', 0]])
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.acompte_facture', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                ->Where('ventes.client_id', $Vente->client_id)
                ->groupBy('article_ventes.vente_id')
                ->get();
        $client = Client::find($Vente->client_id);
        if ($vente_clients != null) {
            $credtiTotal = 0;
            foreach ($vente_clients as $credit_client) {
                $credtiTotal = $credtiTotal + ($credit_client->sommeTotale - $credit_client->acompte_facture);
            }
            $jsonData["plafond_client"] = $client->plafond_client;
            $jsonData["doit_client"] = $credtiTotal;
        }else{
            $jsonData["plafond_client"] = $client->plafond_client;
            $jsonData["doit_client"] = 0;
        }
        
    }

        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        $jsonData["montantTTTC_add"] = $montantTTTC_add;
        return response()->json($jsonData);
    }
    
    public function listeArticlesVendusByQuantite(){
        $date_jour = date("Y-m-d");
        $articlesVentes =  ArticleVente::with('article','unite')
                ->join('ventes','ventes.id','=','article_ventes.vente_id')->where('article_ventes.retourne',0)
                ->whereDate('ventes.date_vente',$date_jour)
                ->select('article_ventes.*')
                ->get();
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesVendusByQuantiteArtice($article){
        $articlesVentes =  ArticleVente::with('article','unite')
                            ->where([['article_ventes.article_id',$article],['article_ventes.retourne',0]])
                            ->select('article_ventes.*')
                            ->get();
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    
    public function listeArticlesVendusByQuantiteDepot($depot){
         $articlesVentes =  ArticleVente::with('article','unite')
                            ->join('ventes','ventes.id','=','article_ventes.vente_id')
                            ->where([['ventes.depot_id',$depot],['article_ventes.retourne',0]])
                            ->select('article_ventes.*')
                            ->get();
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }

    public function listeArticlesVendusByQuantitePeriode($debut,$fin){
         $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $articlesVentes =  ArticleVente::with('article','unite')
                            ->join('ventes','ventes.id','=','article_ventes.vente_id')->where('article_ventes.retourne',0)
                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                            ->whereDate('ventes.date_vente','<=',$dateFin)
                            ->select('article_ventes.*')
                            ->get();
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    public function listeArticlesVendusByQuantiteArticlePeriode($debut,$fin,$article){
         $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
         $articlesVentes =  ArticleVente::with('article','unite')
                            ->join('ventes','ventes.id','=','article_ventes.vente_id')
                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                            ->whereDate('ventes.date_vente','<=',$dateFin)
                            ->where([['article_ventes.article_id',$article],['article_ventes.retourne',0]])
                            ->select('article_ventes.*')
                            ->get();
        
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    
    public function listeArticlesVendusByQuantiteDepotArticle($depot,$article){
         $articlesVentes =  ArticleVente::with('article','unite')
                            ->join('ventes','ventes.id','=','article_ventes.vente_id')
                            ->where([['ventes.depot_id',$depot],['article_ventes.article_id',$article],['article_ventes.retourne',0]])
                            ->select('article_ventes.*')
                            ->get();
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    
    public function listeArticlesVendusByQuantiteDepotPeriode($depot,$debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
         $articlesVentes =  ArticleVente::with('article','unite')
                            ->join('ventes','ventes.id','=','article_ventes.vente_id')
                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                            ->whereDate('ventes.date_vente','<=',$dateFin)
                            ->where([['ventes.depot_id',$depot],['article_ventes.retourne',0]])
                            ->select('article_ventes.*')
                            ->get();
        
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    
    public function listeArticlesVendusByQuantiteDepotArticlePeriode($debut,$fin,$depot,$article){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
         $articlesVentes =  ArticleVente::with('article','unite')
                            ->join('ventes','ventes.id','=','article_ventes.vente_id')
                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                            ->whereDate('ventes.date_vente','<=',$dateFin)
                            ->where([['ventes.depot_id',$depot],['article_ventes.article_id',$article],['article_ventes.retourne',0]])
                            ->select('article_ventes.*')
                            ->get();
        
        $jsonData["rows"] = $articlesVentes->toArray();
        $jsonData["total"] = $articlesVentes->count();
        return response()->json($jsonData);
    }
    
    public function findOneArticleOnVente($vente,$article){
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                    ->join('articles','articles.id','=','article_ventes.article_id')->where([['articles.id',$article],['article_ventes.retourne',0]])
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->select('article_ventes.article_id as article_id','article_ventes.prix','unites.libelle_unite','unites.id as id_unite','article_ventes.quantite')
                    ->Where([['ventes.deleted_at', NULL],['ventes.id',$vente]])
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('article_id') or $request->input('divers_id')) {

                $data = $request->all(); 

            try {
               
                //Récuperation du dépôt
                $vente = Vente::find($data['vente_id']);
                $depot = $vente->depot_id;
                
                
                 if ($vente->client_id != null && $vente->divers == 1) {
                    //Recuperation du crédit total du client
                    $vente_clients = Vente::where([['ventes.deleted_at', NULL], ['ventes.proformat', 0]])
                            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.acompte_facture', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->Where('ventes.client_id', $vente->client_id)
                            ->groupBy('article_ventes.vente_id')
                            ->get();
                    $client = Client::find($vente->client_id);
                    if ($vente_clients != null) {
                        $credtiTotal = 0;
                        foreach ($vente_clients as $credit_client) {
                            $credtiTotal = $credtiTotal + ($credit_client->sommeTotale - $credit_client->acompte_facture);
                        }

                        $montant_new_ligne = $data['quantite'] * $data['prix'];

                        //Vérification du montant total des crédits + doit et le montant plafond
                        if (($credtiTotal + $montant_new_ligne) > $client->plafond_client && $client->plafond_client != 0) {
                            return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de " . number_format((($credtiTotal + $montant_new_ligne) - $client->plafond_client), 0, ',', ' ') . " F CFA", "data" => NULL]);
                        }
                    }

                    $articleVente = new ArticleVente();
                    $articleVente->divers_id = $data["divers_id"];
                    $articleVente->vente_id = $vente->id;
                    $articleVente->quantite = $data["quantite"];
                    $articleVente->prix = $data["prix"];
                    $articleVente->created_by = Auth::user()->id;
                    $articleVente->save();
                    $jsonData["data"] = json_decode($articleVente);
                    return response()->json($jsonData);
                }
                $Article = Article::find($data['article_id']);
                if($Article != null && $Article->stockable == 1) {
                   //Vérifions la quantité en stock
                    $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->first();
                    if ($DepotArticle->quantite_disponible < $data['quantite']) {
                        return response()->json(["code" => 0, "msg" => "La quantité demandée est supérieure à la quantité dans ce dépôt qui est " . $DepotArticle->quantite_disponible, "data" => NULL]);
                    }         
                }
                
                if($vente->client_id==null){
                    if($vente->caisse_ouverte_id!=null){
                        $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                        if(!$caisse_ouverte or $caisse_ouverte->date_fermeture!=null){
                            return response()->json(["code" => 0, "msg" => "Ajout impossible car la caisse est fermée", "data" => NULL]);
                        }
                    }
                }  
            if($vente->client_id!=null && $vente->divers==0){
                //Recuperation du crédit total du client
                $vente_clients = Vente::where([['ventes.deleted_at', NULL],['ventes.proformat',0]])
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.acompte_facture',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->Where('ventes.client_id',$vente->client_id)
                            ->groupBy('article_ventes.vente_id')
                            ->get();
                $client = Client::find($vente->client_id);
                if($vente_clients!=null){
                    $credtiTotal = 0;
                    foreach ($vente_clients as $credit_client){
                            $credtiTotal = $credtiTotal + ($credit_client->sommeTotale-$credit_client->acompte_facture);
                    }
                 
                    $montant_new_ligne = ($data['quantite'] * $data['prix'])-$data['remise_sur_ligne'];
                  
                    //Vérification du montant total des crédits + doit et le montant plafond
                    if(($credtiTotal+$montant_new_ligne)>$client->plafond_client && $client->plafond_client!=0){
                        return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de ".number_format((($credtiTotal+$montant_new_ligne)-$client->plafond_client), 0, ',', ' ')." F CFA", "data" => NULL]);
                    }
                }
            }
                
                $ArticleVente = ArticleVente::where([['vente_id', $data['vente_id']], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->first();
                if($ArticleVente!=null){
                    $ArticleVente->quantite = $ArticleVente->quantite + $data['quantite'];
                    $ArticleVente->remise_sur_ligne = $ArticleVente->remise_sur_ligne + intval($data['remise_sur_ligne']);
                    $ArticleVente->save();
                    $articleVentes = $ArticleVente;
                }else{
                    $articleVente = new ArticleVente;
                    $articleVente->article_id = $data['article_id'];
                    $articleVente->vente_id = $data['vente_id'];
                    $articleVente->quantite = $data['quantite'];
                    $articleVente->unite_id = $data['unite_id'];
                    $articleVente->depot_id = $depot;
                    $articleVente->remise_sur_ligne = $data['remise_sur_ligne'];
                    $articleVente->prix = $data['prix'];
                    $articleVente->created_by = Auth::user()->id;
                    $articleVente->save();
                    $articleVentes = $articleVente;
                }
                
                if($Article != null && $Article->stockable == 1) {
                   //Dimunition stock depot-article
                    if($vente->proformat == 0) {
                        $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                        if (!$mouvementStock) {
                            $mouvementStock = new MouvementStock;
                            $mouvementStock->date_mouvement = date_format($vente->date_vente,"Y-m-d");
                            $mouvementStock->depot_id = $data['depot_id'];
                            $mouvementStock->article_id = $data['article_id'];
                            $mouvementStock->unite_id = $data['unite_id'];
                            $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                            $mouvementStock->created_by = Auth::user()->id;
                        }
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $data['quantite'];
                        $DepotArticle->save();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data['quantite'];
                        $mouvementStock->save();
                    } 
                }
               
                $jsonData["data"] = json_decode($articleVentes);
                return response()->json($jsonData);

            } catch (Exception $exc) {
               $jsonData["code"] = -1;
               $jsonData["data"] = NULL;
               $jsonData["msg"] = $exc->getMessage();
               return response()->json($jsonData); 
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => NULL]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\ArticleVente  $articleVente
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
         $articleVente = ArticleVente::find($id);
        if ($articleVente) {
                $data = $request->all(); 
            try {
                    if(isset($data["remise_sur_ligne"]) && $data['remise_sur_ligne']==""){
                      $data['remise_sur_ligne'] = 0;
                    }
                //Récuperation de l'ancien dépôt
                $vente = Vente::find($articleVente->vente_id);
                $depot = $vente->depot_id;
                if ($vente->client_id != null && $vente->divers == 1) {
                    //Recuperation du crédit total du client
                    $vente_clients = Vente::where([['ventes.deleted_at', NULL], ['ventes.proformat', 0]])
                            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.acompte_facture', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->Where('ventes.client_id', $vente->client_id)
                            ->groupBy('article_ventes.vente_id')
                            ->get();
                    $client = Client::find($vente->client_id);
                    if ($vente_clients != null) {
                        $credtiTotal = 0;
                        foreach ($vente_clients as $credit_client) {
                            $credtiTotal = $credtiTotal + ($credit_client->sommeTotale - $credit_client->acompte_facture);
                        }

                        $montant_new_ligne = $data['quantite'] * $data['prix'];

                        //Vérification du montant total des crédits + doit et le montant plafond
                        if (($credtiTotal + $montant_new_ligne) > $client->plafond_client && $client->plafond_client != 0) {
                            return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de " . number_format((($credtiTotal + $montant_new_ligne) - $client->plafond_client), 0, ',', ' ') . " F CFA", "data" => NULL]);
                        }
                    }

                    $articleVente->divers_id = $data["divers_id"];
                    $articleVente->vente_id = $vente->id;
                    $articleVente->quantite = $data["quantite"];
                    $articleVente->prix = $data["prix"];
                    $articleVente->created_by = Auth::user()->id;
                    $articleVente->save();
                    $jsonData["data"] = json_decode($articleVente);
                    return response()->json($jsonData);
                }
                $Article = Article::find($articleVente->article_id);
                if($Article != null && $Article->stockable == 1) {
                    $oldQuantite = $articleVente->quantite;
                    //Ajustement stock dans depot-article
                    $oldDepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $articleVente->article_id],['unite_id', $articleVente->unite_id]])->first();
                    $oldDepotArticle->quantite_disponible = $oldDepotArticle->quantite_disponible + $articleVente->quantite;
                    $oldDepotArticle->save();

                    $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $articleVente->article_id], ['unite_id', $articleVente->unite_id]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                    $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue - $articleVente->quantite;
                    $mouvementStock->save();
                
                    //Vérification du stock disponible
                    $depot_article = DepotArticle::where([['depot_id', $depot], ['article_id', $data['article_id']],['unite_id', $data['unite_id']]])->first();
                    if($depot_article->quantite_disponible < $data['quantite']){
                        //Ajustement stock dans depot-article
                        $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $articleVente->article_id],['unite_id', $articleVente->unite_id]])->first();
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $oldQuantite;
                        $DepotArticle->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $articleVente->article_id], ['unite_id', $articleVente->unite_id]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $articleVente->quantite;
                        $mouvementStock->save();
                        return response()->json(["code" => 0, "msg" => "La quantité demandée est supérieure à la quantité dans ce dépôt qui est ".$depot_article->quantite_disponible, "data" => NULL]);
                    }
                }
                if($vente->client_id==null){   
                   if($vente->caisse_ouverte_id!=null){
                       $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                       if(!$caisse_ouverte or $caisse_ouverte->date_fermeture!=null){
                           return response()->json(["code" => 0, "msg" => "Modification impossible car la caisse est fermée", "data" => NULL]);
                       }
                   }
                }   
                if($vente->client_id!=null){
                    //Recuperation du crédit total du client
                    $vente_clients = Vente::where([['ventes.deleted_at', NULL],['ventes.proformat',0]])
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                ->select('ventes.acompte_facture',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                                ->Where('ventes.client_id',$vente->client_id)
                                ->groupBy('article_ventes.vente_id')
                                ->get();
                    $client = Client::find($vente->client_id);
                    if($vente_clients!=null){
                        $credtiTotal = 0;
                        foreach ($vente_clients as $credit_client){
                                $credtiTotal = $credtiTotal + ($credit_client->sommeTotale-$credit_client->acompte_facture);
                        }
                       
                        $old_mtn = $articleVente->quantite*$articleVente->prix;
                        $new_mtn = $data['quantite']*$data['prix'];
                        $articleVente->remise_sur_ligne > $data['remise_sur_ligne'] ? $new_remise = -($articleVente->remise_sur_ligne-$data['remise_sur_ligne']) : $new_remise = ($data['remise_sur_ligne']-$articleVente->remise_sur_ligne);
                         
                        $montant_ligne = abs($new_mtn-$old_mtn)-$new_remise;
                    
                        //Vérification du montant total des crédits + doit et le montant plafond
                        if(($credtiTotal+$montant_ligne)>$client->plafond_client && $client->plafond_client!=0){
                            return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de ".number_format((($credtiTotal+$montant_ligne)-$client->plafond_client), 0, ',', ' ')." F CFA", "data" => NULL]);
                        }
                    }
                }
                
                $articleVente->article_id = $data['article_id'];
                $articleVente->vente_id = $data['vente_id'];
                $articleVente->unite_id = $data['unite_id'];
                $articleVente->quantite = $data['quantite'];
                $articleVente->prix = $data['prix'];
                $articleVente->remise_sur_ligne = $data['remise_sur_ligne'];
                $articleVente->updated_by = Auth::user()->id;
                $articleVente->save();
              
                //Augmentation stock dans depot-article
                $Articles = Article::find($data['article_id']);
                if($Articles != null && $Articles->stockable == 1) {
                    if($vente->proformat == 0) {
                        $NewDepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $data['article_id']],['unite_id', $data['unite_id']]])->first();
                        $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $data['article_id']], ['unite_id', $data['unite_id']]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                        if (!$mouvementStock) {
                                $mouvementStock = new MouvementStock;
                                $mouvementStock->date_mouvement = date_format($vente->date_vente, "Y-m-d");
                                $mouvementStock->depot_id = $data['depot_id'];
                                $mouvementStock->article_id = $data['article_id'];
                                $mouvementStock->unite_id = $data['unite_id'];
                                $mouvementStock->quantite_initiale = $NewDepotArticle != null ? $NewDepotArticle->quantite_disponible : 0;
                                $mouvementStock->created_by = Auth::user()->id;
                        }
                        $NewDepotArticle->quantite_disponible = $NewDepotArticle->quantite_disponible - $data['quantite'];
                        $NewDepotArticle->save();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data['quantite'];
                        $mouvementStock->save();
                    }  
                }
                
                $jsonData["data"] = json_decode($articleVente);
                return response()->json($jsonData);

            } catch (Exception $exc) {
               $jsonData["code"] = -1;
               $jsonData["data"] = NULL;
               $jsonData["msg"] = $exc->getMessage();
               return response()->json($jsonData); 
            }
        }
        return response()->json(["code" => 0, "msg" => "Saisie invalide", "data" => NULL]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ArticleVente  $articleVente
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
         $articleVente = ArticleVente::find($id);
            if($articleVente){
                try {
                    //Récuperation de l'ancien dépôt
                $vente = Vente::find($articleVente->vente_id);
                $depot = $vente->depot_id;
                if($vente->caisse_ouverte_id!=null){
                    $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                    if(!$caisse_ouverte or $caisse_ouverte->date_fermeture!=null){
                        return response()->json(["code" => 0, "msg" => "Supression impossible car la caisse est fermée", "data" => NULL]);
                    }
                }
                //Ajustement stock dans depot-article
                $Article = Article::find($articleVente->article_id);
                if($Article != null && $Article->stockable == 1) {
                    if($vente->proformat==0){
                        $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $articleVente->article_id],['unite_id', $articleVente->unite_id]])->first();
                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible + $articleVente->quantite;
                        $DepotArticle->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $depot], ['article_id', $articleVente->article_id], ['unite_id', $articleVente->unite_id]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue - $articleVente->quantite;
                        $mouvementStock->save();
                    }  
                }
               
                $articleVente->update(['deleted_by' => Auth::user()->id]);
                $articleVente->delete();
                $jsonData["data"] = json_decode($articleVente);
                return response()->json($jsonData);
                } catch (Exception $exc) {
                   $jsonData["code"] = -1;
                   $jsonData["data"] = NULL;
                   $jsonData["msg"] = $exc->getMessage();
                   return response()->json($jsonData); 
                }
            }
        return response()->json(["code" => 0, "msg" => "Echec de suppression", "data" => NULL]);
    }
    
    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    
    //Etat
    public function articlesVendusByQuantitePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantite());
        return $pdf->stream('liste_articles_vendus.pdf');
    }
    public function articlesVendusByQuantite(){
        $date_jour = date("Y-m-d");
       $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->whereDate('caisse_ouvertes.date_ouverture',$date_jour)
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du jour</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['ventes.client_id',null]])
                                            ->join('articles','articles.id','article_ventes.article_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.$article->prix.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
       
         $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where('ventes.client_id','!=',null)
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->join('articles','articles.id','article_ventes.article_id')
                                            ->whereDate('ventes.date_vente',$date_jour)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.$article->prix.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Mouvement d'un article sur une période
    
    public function articlesVendusByQuantitePeriodeArticlePdf($debut,$fin,$article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantitePeriodeArticle($debut,$fin,$article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_article_'.$info_article->description_article.'_vendus_du_'.$debut.'_au_'.$fin.'_.pdf');
    }
    public function articlesVendusByQuantitePeriodeArticle($debut,$fin,$article_id){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
         $info_article = Article::find($article_id);
       $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock concernant '.$info_article->description_article.' du '.$debut.' au '.$fin.' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.$article->prix.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
       
         $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where([['ventes.client_id','!=',null],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','unites.libelle_unite')
                                            ->get();
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    //Mouvement sur une période
    public function articlesVendusByQuantitePeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantitePeriode($debut,$fin));
        return $pdf->stream('liste_articles_vendus_du_'.$debut.'_au_'.$fin.'_.pdf');
    }
    public function articlesVendusByQuantitePeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
       $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du '.$debut.' au '.$fin.' </h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['ventes.client_id',null]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('articles','articles.id','=','article_ventes.article_id')
                                            ->join('unites','unites.id','=','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
       
         $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where('ventes.client_id','!=',null)
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('articles','articles.id','=','article_ventes.article_id')
                                            ->join('unites','unites.id','=','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Mouvement d'un article
    public function articlesVendusByQuantiteArticlePdf($article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByQuantiteArticle($article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_articles_vendus_dans_le_depot_'.$info_article->description_article.'.pdf');
    }
    public function articlesVendusByQuantiteArticle($article_id){
       $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
       $info_article = Article::find($article_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock concernant '.$info_article->description_article.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->select('article_ventes.*','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
      
        $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                    <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where([['ventes.client_id','!=',null],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->select('article_ventes.*','unites.libelle_unite')
                                            ->get();
        
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function articlesVendusByDepotPdf($depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepot($depot));
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_articles_'.$info_depot->libelle_depot.'_vendus.pdf');
    }
    public function articlesVendusByDepot($depot_id){
       $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $info_depot = Depot::find($depot_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['ventes.depot_id',$depot_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->join('articles','articles.id','article_ventes.article_id')
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
      
        $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                    <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where([['ventes.client_id','!=',null],['ventes.depot_id',$depot_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->join('articles','articles.id','article_ventes.article_id')
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
        
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function articlesVendusByDepotArticlePdf($depot,$article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepotArticle($depot,$article));
        $info_depot = Depot::find($depot);
        $info_article = Article::find($article);
        return $pdf->stream('liste_'.$info_article->description_article.'_vendus_dans_le_depot_'.$info_depot->libelle_depot.'_pdf');
    }
    public function articlesVendusByDepotArticle($depot_id,$article_id){
         $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $info_depot = Depot::find($depot_id);
        $info_article = Article::find($article_id);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du dépôt '.$info_depot->libelle_depot.' concernant '.$info_article->description_article.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['ventes.depot_id',$depot_id],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->select('article_ventes.*','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
      
        $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                    <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where([['ventes.client_id','!=',null],['ventes.depot_id',$depot_id],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('unites','unites.id','article_ventes.unite_id')
                                            ->select('article_ventes.*','unites.libelle_unite')
                                            ->get();
        
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function articlesVendusByDepotPeriodePdf($depot,$debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepotPeriode($depot,$debut,$fin));
         $info_depot = Depot::find($depot);
        return $pdf->stream('liste_articles_vendus_du_'.$debut.'_au_'.$fin.'_dans_le_depo_'.$info_depot->libelle_depot.'.pdf');
    }
    public function articlesVendusByDepotPeriode($depot,$debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
         $info_depot = Depot::find($depot);
        $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock du '.$debut.' au '.$fin.' dans le dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="4" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['ventes.client_id',null],['ventes.depot_id',$depot]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('articles','articles.id','=','article_ventes.article_id')
                                            ->join('unites','unites.id','=','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
       
         $outPut .= '<tr>
                        <td  colspan="8" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Pirx U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where([['ventes.client_id','!=',null],['ventes.depot_id',$depot]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('articles','articles.id','=','article_ventes.article_id')
                                            ->join('unites','unites.id','=','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="8" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function articlesVendusByDepotArticlePeriodePdf($debut,$fin,$depot,$article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articlesVendusByDepotArticlePeriode($debut,$fin,$depot,$article));
         $info_depot = Depot::find($depot);
          $info_article = Article::find($article);
        return $pdf->stream('liste_'.$info_article->description_article.'_vendus_du_'.$debut.'_au_'.$fin.'_dans_le_depo_'.$info_depot->libelle_depot.'.pdf');

    }
    public function articlesVendusByDepotArticlePeriode($debut,$fin,$depot_id,$article_id){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
         $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
         $info_depot = Depot::find($depot_id);
          $info_article = Article::find($article_id);
        $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal de mouvement de stock concernant '.$info_article->description_article.' du '.$debut.' au '.$fin.' dans le dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
                $articles =  ArticleVente::where([['ventes.caisse_ouverte_id',$data->id],['ventes.client_id',null],['ventes.depot_id',$depot_id],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('articles','articles.id','=','article_ventes.article_id')
                                            ->join('unites','unites.id','=','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
                $totalCiasse = 0;
            foreach($articles as $article){
                $totalCiasse = $totalCiasse + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
           $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
       
         $outPut .= '<tr>
                        <td  colspan="6" cellspacing="0" border="2"><h3 align="center">Vente hors caisse</h3></td>
                    </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix U.</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Valeur net</th>
                        </tr>';
        $articlesHorsC =  ArticleVente::where([['ventes.client_id','!=',null],['ventes.depot_id',$depot_id],['article_ventes.article_id',$article_id]])
                                            ->join('ventes','ventes.id','article_ventes.vente_id')
                                            ->join('articles','articles.id','=','article_ventes.article_id')
                                            ->join('unites','unites.id','=','article_ventes.unite_id')
                                            ->whereDate('ventes.date_vente','>=',$dateDebut)
                                            ->whereDate('ventes.date_vente','<=',$dateFin)
                                            ->select('article_ventes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                            ->get();
        $totalHorsC = 0;
         foreach($articlesHorsC as $article){
                $totalHorsC= $totalHorsC + $article->quantite*$article->prix-$article->remise_sur_ligne;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$article->prix.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$article->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($article->quantite*$article->prix-$article->remise_sur_ligne, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
        $outPut.= '<tr><td colspan="6" cellspacing="0" border="2" align="left">&nbsp; Total hors caisse <b>'.number_format($totalHorsC, 0, ',', ' ').'</b></td></tr>';
        $outPut .='</table></div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal+$totalHorsC, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    
    //Header and footer des pdf
    public function header(){
        $header = '<html>
                    <head>
                        <style>
                            @page{
                                margin: 100px 25px;
                                }
                            header{
                                    position: absolute;
                                    top: -60px;
                                    left: 0px;
                                    right: 0px;
                                    height:20px;
                                }
                            .container-table{        
                                            margin:80px 0;
                                            width: 100%;
                                        }
                            .fixed-footer{.
                                width : 100%;
                                position: fixed; 
                                bottom: -28; 
                                left: 0px; 
                                right: 0px;
                                height: 50px; 
                                text-align:center;
                            }
                            .fixed-footer-right{
                                position: absolute; 
                                bottom: -150; 
                                height: 0; 
                                font-size:13px;
                                float : right;
                            }
                            .page-number:before {
                                            
                            }
                        </style>
                    </head>
    /
    <script type="text/php">
        if (isset($pdf)){
            $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
        <body>
        <header>
        <p style="margin:0; position:left;">
            <img src='.$this->infosConfig()->logo.' width="200" height="160"/>
        </p>
        </header>';     
        return $header;
    }
    public function footer(){
        $footer ="<div class='fixed-footer'>
                        <div class='page-number'></div>
                    </div>
                    <div class='fixed-footer-right'>
                     <i> Editer le ".date('d-m-Y')."</i>
                    </div>
            </body>
        </html>";
        return $footer;
    }
}
