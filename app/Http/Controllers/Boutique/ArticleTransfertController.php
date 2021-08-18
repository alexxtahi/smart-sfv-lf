<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleTransfert;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\MouvementStock;
use App\Models\Boutique\TransfertStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ArticleTransfertController extends Controller
{
    public function listeArticleTransferts($transfert_stock)
    {
        $articleTransferts = ArticleTransfert::with('article','unite_depart','unite_reception')
                            ->select('article_transferts.*')
                            ->Where([['article_transferts.deleted_at', NULL],['article_transferts.transfert_stock_id',$transfert_stock]])
                            ->get();
        $jsonData["rows"] = $articleTransferts->toArray();
        $jsonData["total"] = $articleTransferts->count();
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
        if ($request->isMethod('post') && $request->input('article_id')) {

                $data = $request->all(); 

            try {
                
                //Récuperation des dépôts
                $transfertStock = TransfertStock::find($data['transfert_stock_id']);
                $depot_depart_id = $transfertStock->depot_depart_id;
                $depot_arrivee_id = $transfertStock->depot_arrivee_id;
                
                if(!isset($data["depot_depart_id"]) or $transfertStock->depot_depart_id!=$data['depot_depart_id']){
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => NULL]);
                }
                
                $depotDpart = DepotArticle::where([['depot_id', $depot_depart_id], ['article_id', $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();
                
                //On vérifie la quantité du stock
                if($depotDpart!=null && $data['quantite'] > $depotDpart->quantite_disponible){
                    return response()->json(["code" => 0, "msg" => "La quantite à transferer est supérieure à la quantité disponible en stock qui est ".$depotDpart->quantite_disponible, "data" => NULL]);
                }
               
                $articleTransfert = ArticleTransfert::where([['transfert_stock_id', $data['transfert_stock_id']], ['article_id', $data['article_id']],['unite_depart', $data['unite_id']],['unite_reception', $data['unite_reception']]])->first();
                if(!$articleTransfert){
                    $articleTransfert = new ArticleTransfert();
                    $articleTransfert->article_id = $data["article_id"];
                    $articleTransfert->unite_depart = $data["unite_id"];
                    $articleTransfert->unite_reception = $data["unite_reception"];
                    $articleTransfert->transfert_stock_id = $data['transfert_stock_id'];
                    $articleTransfert->created_by = Auth::user()->id;
                }
                
                $articleTransfert->quantite_depart = $articleTransfert->quantite_depart + $data['quantite'];
                $articleTransfert->quantite_reception = $articleTransfert->quantite_reception + $data['quantite_reception'];
                $articleTransfert->save();
                
                //Traitement sur le stock dans depot-article 
                if ($articleTransfert != null) {
                    $depotDpart = DepotArticle::where([['depot_id', $data["depot_depart_id"]], ['article_id',  $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();
                    $depotArrive = DepotArticle::where([['depot_id', $data["depot_arrivee_id"]], ['article_id',  $data["article_id"]], ['unite_id', $data["unite_reception"]]])->first();
                    $mouvementStock = MouvementStock::where([['depot_id', $data['depot_depart_id']], ['article_id',  $data["article_id"]], ['unite_id',  $data["unite_id"]]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();

                    if (!$mouvementStock) {
                        $mouvementStock = new MouvementStock;
                        $mouvementStock->date_mouvement = $transfertStock->date_transfert;
                        $mouvementStock->depot_id = $data['depot_depart_id'];
                        $mouvementStock->article_id = $data["article_id"];
                        $mouvementStock->unite_id = $data["unite_id"];
                        $mouvementStock->quantite_initiale = $depotDpart != null ? $depotDpart->quantite_disponible : 0;
                        $mouvementStock->created_by = Auth::user()->id;
                    }

                    if (!$depotArrive) {
                        $depotArrive = new DepotArticle;
                        $depotArrive->article_id = $data["article_id"];
                        $depotArrive->depot_id = $data["depot_arrivee_id"];
                        $depotArrive->unite_id = $data["unite_reception"];
                    }

                    $depotDpart->quantite_disponible = $depotDpart->quantite_disponible - $data['quantite'];
                    $depotDpart->save();
                    $depotArrive->quantite_disponible = $depotArrive->quantite_disponible + $data['quantite_reception'];
                    $depotArrive->save();
                    $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $data['quantite'];
                    $mouvementStock->save();
                }
                $jsonData["data"] = json_decode($articleTransfert);
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
     * @param  \App\ArticleTransfert  $articleTransfert
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
         $articleTransfert = ArticleTransfert::find($id);
        if ($articleTransfert) {
                $data = $request->all(); 
            try {
               
                //Récuperation de l'ancien dépôt
                $transfertStock = TransfertStock::find($articleTransfert->transfert_stock_id);
                $depot_depart_id = $transfertStock->depot_depart_id;
                $depot_arrivee_id = $transfertStock->depot_arrivee_id;
                
                if(!isset($data["depot_depart_id"]) or $transfertStock->depot_depart_id!=$data['depot_depart_id']){
                    return response()->json(["code" => 0, "msg" => "Vous avez changé de dépôt, vous devez donc confirmer ce changement avant de pouvoir ajouter un article.", "data" => NULL]);
                }
                
                //Ajustement des stocks dans depot-article
                $depotDpart = DepotArticle::where([['depot_id', $depot_depart_id], ['article_id', $articleTransfert->article_id], ['unite_id', $articleTransfert->unite_depart]])->first();
                $depotArrive = DepotArticle::where([['depot_id', $depot_arrivee_id], ['article_id', $articleTransfert->article_id], ['unite_id', $articleTransfert->unite_reception]])->first();
              
                $depotDpart->quantite_disponible = $depotDpart->quantite_disponible + $articleTransfert->quantite_depart;
                $depotDpart->save();
                $depotArrive->quantite_disponible = $depotArrive->quantite_disponible - $articleTransfert->quantite_reception;
                $depotArrive->save();
                
                $mouvementStock = MouvementStock::where([['depot_id', $transfertStock->depot_depart_id],['article_id', $articleTransfert->article_id],['unite_id', $articleTransfert->unite_depart]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();
                $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee - $articleTransfert->quantite_depart;
                $mouvementStock->save();
                
                //On vérifie la quantité du stock
                if($data['quantite'] > $depotDpart->quantite_disponible){
                    $depotDpart->quantite_disponible = $depotDpart->quantite_disponible - $articleTransfert->quantite_depart;
                    $depotDpart->save();
                    $depotArrive->quantite_disponible = $depotDpart->quantite_disponible + $articleTransfert->quantite_reception;
                    $depotArrive->save();
                    $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $articleTransfert->quantite_depart;
                    $mouvementStock->save();
                    return response()->json(["code" => 0, "msg" => "La quantite à transferer est supérieure à la quantité disponible en stock qui est ".$depotDpart->quantite_disponible, "data" => NULL]);
                }
                
                $articleTransfert->article_id = $data["article_id"];
                $articleTransfert->unite_depart = $data["unite_id"];
                $articleTransfert->unite_reception = $data["unite_reception"];
                $articleTransfert->quantite_depart = $data['quantite'];
                $articleTransfert->quantite_reception = $data['quantite_reception'];
                $articleTransfert->updated_by = Auth::user()->id;
                $articleTransfert->save();

                //Traitement sur le stock dans depot-article 
                if ($articleTransfert != null) {
                    $DepotDpart = DepotArticle::where([['depot_id', $data["depot_depart_id"]], ['article_id',  $data["article_id"]], ['unite_id', $data["unite_id"]]])->first();
                    $DepotArrive = DepotArticle::where([['depot_id', $data["depot_arrivee_id"]], ['article_id',  $data["article_id"]], ['unite_id', $data["unite_reception"]]])->first();
                    $mouvementStock = MouvementStock::where([['depot_id', $data["depot_depart_id"]],['article_id', $data["article_id"]],['unite_id', $data["unite_id"]]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();

                    if (!$mouvementStock) {
                        $mouvementStock = new MouvementStock;
                        $mouvementStock->date_mouvement = $transfertStock->date_transfert;
                        $mouvementStock->depot_id = $data['depot_depart_id'];
                        $mouvementStock->article_id = $data["article_id"];
                        $mouvementStock->unite_id = $data["unite_id"];
                        $mouvementStock->quantite_initiale = $DepotDpart != null ? $DepotDpart->quantite_disponible : 0;
                        $mouvementStock->created_by = Auth::user()->id;
                    }
                    if (!$DepotArrive) {
                        $DepotArrive = new DepotArticle;
                        $DepotArrive->article_id = $data["article_id"];
                        $DepotArrive->depot_id = $data["depot_arrivee_id"];
                        $DepotArrive->unite_id = $data["unite_reception"];
                    }

                    $DepotDpart->quantite_disponible = $DepotDpart->quantite_disponible - $data['quantite'];
                    $DepotDpart->save();
                    $DepotArrive->quantite_disponible = $DepotArrive->quantite_disponible + $data['quantite_reception'];
                    $DepotArrive->save();
                    $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $data['quantite'];
                    $mouvementStock->save();
                }
                $jsonData["data"] = json_decode($articleTransfert);
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
     * @param  \App\ArticleTransfert  $articleTransfert
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
         $articleTransfert = ArticleTransfert::find($id);
            if($articleTransfert){
                try {
                     //Récuperation de l'ancien dépôt
                $transfertStock = TransfertStock::find($articleTransfert->transfert_stock_id);
                $depot_depart_id = $transfertStock->depot_depart_id;
                $depot_arrivee_id = $transfertStock->depot_arrivee_id;
                
                //Ajustement des stocks dans depot-article
                $depotDpart = DepotArticle::where([['depot_id', $depot_depart_id], ['article_id', $articleTransfert->article_id], ['unite_id', $articleTransfert->unite_depart]])->first();
                $depotArrive = DepotArticle::where([['depot_id', $depot_arrivee_id], ['article_id', $articleTransfert->article_id], ['unite_id', $articleTransfert->unite_reception]])->first();

                $depotDpart->quantite_disponible = $depotDpart->quantite_disponible + $transfertStock->quantite_depart;
                $depotDpart->save();
                $depotArrive->quantite_disponible = $depotArrive->quantite_disponible - $transfertStock->quantite_reception;
                $depotArrive->save();
                
                $mouvementStock = MouvementStock::where([['depot_id', $depot_depart_id],['article_id', $articleTransfert->article_id],['unite_id', $articleTransfert->unite_depart]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();
                $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee - $articleTransfert->quantite_depart;
                $mouvementStock->save();
                
                $articleTransfert->update(['deleted_by' => Auth::user()->id]);
                $articleTransfert->delete();
                $jsonData["data"] = json_decode($articleTransfert);
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
}
