<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Boutique\Approvisionnement;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function response;
use function view;


class RestaurageDataController extends Controller
{
    public function index(){
       
       $menuPrincipal = "Datas bases";
       $titleControlleur = "Liste de toutes les tables de la base";
       $btnModalAjout = "FALSE";
       return view('auth.restaurage_data_vues.all_tables',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function oneTable($table){
       $menuPrincipal = ucwords(str_replace('_', ' ', substr($table, 0,-1)));
       $titleControlleur = "Liste des enregistrements supprimées dans cette table";
       $btnModalAjout = "FALSE";
        return view('auth.restaurage_data_vues.one_table',compact('table', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeAllTable(){
        $allTables = DB::select(DB::raw("SHOW TABLES"));
        return $allTables;
    }
    
    public function listeContentOneTable($table){  
        
        $name_colone_table = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'smartsfv' AND TABLE_NAME = '$table' AND ORDINAL_POSITION = 2");

        $array = json_decode(json_encode($name_colone_table), true);

        $libelle = $array[0]["COLUMN_NAME"]; 
        if($table=="users"){
            $tableConcernee = DB::table($table)
                        ->select('users.full_name','users.role','users.email','users.contact',$table.'.id',$table.'.deleted_at',DB::raw($table.".$libelle as libelle"))
                        ->Where($table.'.deleted_at', '<>' ,NULL)
                        ->orderBy($table.'.deleted_at', 'desc')->get();
        }else{
           $tableConcernee = DB::table($table)
                        ->join('users','users.id','=', $table.'.deleted_by')
                        ->select('users.full_name','users.role','users.email','users.contact',$table.'.id',$table.'.deleted_at',DB::raw($table.".$libelle as libelle"))
                        ->Where($table.'.deleted_at', '<>' ,NULL)
                        ->orderBy($table.'.deleted_at', 'desc')->get();
        }
        
       $jsonData["rows"] = $tableConcernee->toArray();
       $jsonData["total"] = $tableConcernee->count();
        return response()->json($jsonData);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function restaurage(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Enregistrement restauré avec succès."];
        $table = $request->get('table');
        $id = $request->get('id');
        
        if(!empty($table) && !empty($id)){
                if($table=='article_approvisionnements'){
                    $article_approvisionnement = DB::table("article_approvisionnements")->where('id', $id)->select('article_approvisionnements.*')->first();
                    $article = Article::find($article_approvisionnement->article_id);
                    $article->quantite_en_stock = $article->quantite_en_stock + $article_approvisionnement->quantite;
                    $article->save();
                    
                    //Récuperation du dépôt
                    $approvisionnement = Approvisionnement::find($article_approvisionnement->approvisionnement_id);
                    $depot = $approvisionnement->depot_id;

                    //Ajustement stock dans depot-article
                    $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $article_approvisionnement->article_id]])->first();
                    $DepotArticle->quantite = $DepotArticle->quantite + $article_approvisionnement->quantite;
                    $DepotArticle->save();
                }
                if($table=='article_ventes'){
                    $article_vente = DB::table("article_ventes")->where('id', $id)->select('article_ventes.*')->first();
                    $article = Article::find($article_vente->article_id);
                    
                    //Récuperation du dépôt
                    $vente = Vente::find($article_vente->vente_id);
                    $depot = $vente->depot_id;

                    //Ajustement stock dans depot-article
                    $DepotArticle = DepotArticle::where([['depot_id', $depot], ['article_id', $article_vente->article_id]])->first();
                    $DepotArticle->quantite = $DepotArticle->quantite - $article_vente->quantite;
                    $DepotArticle->save();
                }
                if($table=='reglements'){
                    $reglement = DB::table("reglements")->where('id', $id)->select('reglements.*')->first();
                    if($reglement->vente_id!=null){
                        $RgVente = Vente::find($reglement->vente_id);
                        $RgVente->acompte_facture = $RgVente->acompte_facture + $reglement->montant_reglement;
                        $RgVente->save();
                    }
                    if($reglement->approvisionnement_id!=null){
                        $RgApprovisionnement = Approvisionnement::find($reglement->approvisionnement_id);
                        $RgApprovisionnement->acompte_approvisionnement = $RgApprovisionnement->acompte_approvisionnement + $reglement->montant_reglement;
                        $RgApprovisionnement->save();
                    }
                }
                if($table=='transfert_stocks'){
                    $transfert_stock = DB::table("transfert_stocks")->where('id', $id)->select('transfert_stocks.*')->first();
                    $DepotDepart = DepotArticle::where([['depot_id', $transfert_stock->depot_depart_id],['article_id', $transfert_stock->article_id]])->first();
                    $DepotArrivee = DepotArticle::where([['depot_id', $transfert_stock->depot_arrivee_id],['article_id', $transfert_stock->article_id]])->first();
                    $DepotDepart->quantite = $DepotDepart->quantite - $transfert_stock->quantite;
                    $DepotDepart->save();
                    $DepotArrivee->quantite = $DepotArrivee->quantite + $transfert_stock->quantite;
                    $DepotArrivee->save();
                }
                if($table=='destockages'){
                    $destockage = DB::table("destockages")->where('id', $id)->select('destockages.*')->first();
                    //Recherche de cet article dans le dépôt
                    $DepotArticle = DepotArticle::where([['depot_id', $destockage->depot_id],['article_id', $destockage->article_id]])->first();
                    //On ramène le stock à sa place
                    $DepotArticle->quantite = $DepotArticle->quantite - $destockage->quantite;
                    $DepotArticle->save();
                }
            try {
                $tableConcerne = DB::table($table)->where('id', $id)->get();
                        DB::table($table)->where('id', $id)
                                    ->update([
                                        'deleted_at' => NULL,
                                        'updated_by' => Auth::user()->id,
                                    ]);
                
                $jsonData["data"] = json_decode($tableConcerne);
                
            return response()->json($jsonData);

            } catch (Exception $exc) {
               $jsonData["code"] = -1;
               $jsonData["data"] = NULL;
               $jsonData["msg"] = $exc->getMessage();
               return response()->json($jsonData); 
            }

        }
        return response()->json(["code" => 0, "msg" => "Echec de restauration de l'enregistrement", "data" => NULL]);        
    }
}
