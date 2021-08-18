<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleVente;
use App\Models\Boutique\CaisseOuverte;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\MouvementStock;
use App\Models\Boutique\Reglement;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Article;
use App\Models\Parametre\Caisse;
use App\Models\Parametre\Client;
use App\Models\Parametre\Depot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Rawilk\Printing\Receipts\ReceiptPrinter;
//C:\laragon\www\smart-sfv-lf\vendor\rawilk\laravel-printing\src\Printing.php
use function view;
include_once(app_path ()."/number-to-letters/nombre_en_lettre.php");
//include_once("C:/laragon/www/smart-sfv-lf/vendor/rawilk/laravel-printing/src/Printing.php");

class VenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $caisses = DB::table('caisses')->Where('deleted_at', NULL)->orderBy('libelle_caisse', 'asc')->get();
       $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
       $unites = DB::table('unites')->Where('deleted_at', NULL)->orderBy('libelle_unite', 'asc')->get();
       $regimes = DB::table('regimes')->Where('deleted_at', NULL)->orderBy('libelle_regime', 'asc')->get();
       $menuPrincipal = "Boutique";
       $titleControlleur = "Toutes les ventes";
       $btnModalAjout = "TRUE";
       return view('boutique.vente.index',compact('moyenReglements','caisses','unites','regimes', 'nations', 'depots','categories','clients','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vueVente(){
       $depots = [];$auth_user = Auth::user();
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
       $regimes = DB::table('regimes')->Where('deleted_at', NULL)->orderBy('libelle_regime', 'asc')->get();
       if(Auth::user()->role=="Administrateur" or Auth::user()->role=="Concepteur"){
           $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       }
       if(Auth::user()->role=="Gerant"){
          $depots = DB::table('depots')->Where([['deleted_at', NULL],['depots.id',Auth::user()->depot_id]])->orderBy('libelle_depot', 'asc')->get(); 
       }
       $menuPrincipal = "Boutique";
       $titleControlleur = "Vente";
       $btnModalAjout = "TRUE";
       return view('boutique.vente.point-vente',compact('regimes','nations','depots','clients','auth_user', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuPointVente(Request $request){
        $caisse = null; $caisse_ouverte = null; $auth_user = Auth::user();
        //Recupértion de la caisse dans la session
        if($request->session()->has('session_caisse_ouverte')){
            $caisse_ouverte_id = $request->session()->get('session_caisse_ouverte');
            $caisse_ouverte = CaisseOuverte::where([['id',$caisse_ouverte_id],['date_fermeture',null]])->first();
            if($caisse_ouverte){
                $caisse = Caisse::find($caisse_ouverte->caisse_id);
            }
        }
        //Si la caisse n'est pas fermée et que l'user s'est déconnecté
        $caisse_ouverte_non_fermee = CaisseOuverte::where([['user_id',$auth_user->id],['date_fermeture',null]])->first();
        if($caisse_ouverte_non_fermee!=null){
            $request->session()->put('session_caisse_ouverte',$caisse_ouverte_non_fermee->id);
            $caisse_ouverte = CaisseOuverte::find($caisse_ouverte_non_fermee->id);
            if($caisse_ouverte){
                $caisse = Caisse::find($caisse_ouverte->caisse_id);
            }
        }
       
       $depot = Depot::find($auth_user->depot_id);
       $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
       $articles = DepotArticle::with('unite','depot','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where('depot_articles.depot_id',$auth_user->depot_id)
                ->select('depot_articles.*','articles.id as id_article','articles.description_article')
                ->groupBy('depot_articles.article_id')
                ->get();
       $menuPrincipal = "Boutique";
       $titleControlleur = "Point de caisse";
       $btnModalAjout = $caisse_ouverte!=null ? "TRUE" : "FALSE";
       return view('boutique.vente.point-caisse',compact('articles','depot','auth_user','caisse_ouverte','caisse','moyenReglements', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function pointCaisseAdmin(){
       if(Auth::user()->role=="Administrateur" or Auth::user()->role=="Concepteur"){
           $caisses = Caisse::with('depot')->Where('deleted_at', NULL)->get();
           $titleControlleur = "Liste des caisses par dépôt";
       }
       if(Auth::user()->role=="Gerant"){
           $caisses = Caisse::with('depot')->Where([['deleted_at', NULL],['caisses.depot_id',Auth::user()->depot_id]])->get();
           $titleControlleur = "Liste des caisses de votre dépôt";
       }
       $menuPrincipal = "Boutique";
       $btnModalAjout = "FALSE";
       return view('boutique.vente.point-caisse-admin',compact('caisses', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function ponitCaisseVuByAdminGerant(Request $request){
        $auth_user = Auth::user();
        $caisse_ouverte = CaisseOuverte::where([['caisse_id',$request->caisse_id],['date_fermeture',null]])->first();
        $caisse = Caisse::find($request->caisse_id);
        $depot = Depot::find($caisse->depot_id);
        $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
        $articles = DepotArticle::with('unite','depot','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where('depot_articles.depot_id',$caisse->depot_id)
                ->select('depot_articles.*','articles.id as id_article','articles.description_article')
                ->groupBy('depot_articles.article_id')
                ->get();
       $menuPrincipal = "Boutique";
       $titleControlleur = "Point de caisse du dépôt ".$depot->libelle_depot;
       $btnModalAjout = $caisse_ouverte!=null ? "TRUE" : "FALSE";
       return view('boutique.vente.point-caisse',compact('articles','depot','auth_user','caisse_ouverte','caisse','moyenReglements', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vueVenteDivers(){
      
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
       $regimes = DB::table('regimes')->Where('deleted_at', NULL)->orderBy('libelle_regime', 'asc')->get();
       $divers = DB::table('divers')->Where('deleted_at', NULL)->orderBy('libelle_divers', 'asc')->get();
      
       $menuPrincipal = "Boutique";
       $titleControlleur = "Vente divers";
       $btnModalAjout = "TRUE";
       return view('boutique.vente.vente-divers',compact('regimes','nations','clients','divers', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeVente(){
        $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null],['ventes.divers',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function listeVentesCaisse(){
        $totalCaisse = 0; 
//        $date_jour = date("Y-m-d");
        $ventes = Vente::with('depot','caisse_ouverte')
                ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('date_fermeture',null)
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.date_fermeture',null],['caisse_ouvertes.user_id',Auth::user()->id]])
//                ->whereDate('ventes.date_vente',$date_jour)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        foreach ($ventes as $vente){
            $totalCaisse = $totalCaisse + $vente->sommeTotale;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalCaisse"] = $totalCaisse;
        return response()->json($jsonData);
    }
    
    
    public function listeVentesDivers(){
         $ventes = Vente::with('client')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null],['ventes.divers',1]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

      public function listeVentesByCaisse($caisse_id){
        $totalCaisse = 0; 
//        $date_jour = date("Y-m-d");
        $ventes = Vente::with('depot', 'caisse_ouverte')
                ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', null],['caisse_ouvertes.date_fermeture',null],['caisse_ouvertes.caisse_id', $caisse_id]])
                ->groupBy('article_ventes.vente_id')
//                ->whereDate('ventes.date_vente',$date_jour)
                ->orderBy('ventes.id', 'DESC')
                ->get();
        foreach ($ventes as $vente){
            $totalCaisse = $totalCaisse + $vente->sommeTotale;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalCaisse"] = $totalCaisse;
        return response()->json($jsonData);
    }
    public function listeVenteByNumeroTicket($caisse_id, $numero_ticket){
        $ventes = Vente::with('depot', 'caisse_ouverte')
                ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', null], ['caisse_ouvertes.caisse_id', $caisse_id],['ventes.numero_ticket', 'like', '%' . $numero_ticket . '%']])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVenteByCaisseDateVente($caisse_id, $date_vente){
        $date = Carbon::createFromFormat('d-m-Y', $date_vente);
        $ventes = Vente::with('depot', 'caisse_ouverte')
                ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', null], ['caisse_ouvertes.caisse_id', $caisse_id]])
                ->whereDate('ventes.date_vente',$date)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function listeVenteByNumeroFacture($numero) {
        $ventes = Vente::with('client', 'depot')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', '!=', null],['ventes.divers',0], ['ventes.numero_facture', 'like', '%' . $numero . '%']])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVenteDiversByNumeroFacture($numero){
         $ventes = Vente::with('client', 'depot')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', '!=', null],['ventes.divers',1], ['ventes.numero_facture', 'like', '%' . $numero . '%']])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVentesDiversByClient($client){
         $ventes = Vente::with('client')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                 ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.divers',1]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVentesDiversByDate($dates){
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
               ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null],['ventes.divers',1]])
                ->whereDate('ventes.date_vente',$date)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function listeVentesByClient($client){
         $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                 ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.divers',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function getAllFactureClient($client){
         $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                 ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVentesByDepot($depot){
         $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
               ->Where([['ventes.deleted_at', NULL],['ventes.depot_id',$depot],['ventes.client_id','!=',null],['ventes.divers',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVentesByDate($dates){
         $date = Carbon::createFromFormat('d-m-Y', $dates);
         $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
               ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null],['ventes.divers',0]])
                ->whereDate('ventes.date_vente',$date)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function findVenteById($id){
        $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.id',$id],['ventes.client_id','!=',null]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
   
    public function listeVenteAllcaisses(){
       $date_jour = date("Y-m-d");
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente',$date_jour)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function findOneVente($id){
        $ventes = Vente::with('depot')
                    ->select('ventes.*',DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.id',$id]])
                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function findArticleSurVenteByCodeBarre($code_barre,$vente){
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                    ->join('articles','articles.id','=','article_ventes.article_id')->where([['articles.code_barre',$code_barre],['article_ventes.retourne',0]])
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

    //TO DO
    public function listeVentesByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id','!=',null]])
                 ->whereDate('ventes.date_vente','>=',$date1)
                ->whereDate('ventes.date_vente','<=', $date2)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeVentesByPeriodeClient($debut,$fin,$client){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                 ->whereDate('ventes.date_vente','>=',$date1)
                ->whereDate('ventes.date_vente','<=', $date2)
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
        if ($request->isMethod('post') && $request->input('monPanier')) {

                $data = $request->all(); 
               
            try {
                if(empty($data['monPanier'])){
                    return response()->json(["code" => 0, "msg" => "Vous n'avez pas ajouté d'articles à cette vente", "data" => NULL]);
                }
               
                //Si la vente provient d'un point de caisse
                if(!isset($data['client_id'])){
                   
                    //formation numéro du ticket
                    $maxIdVente = DB::table('ventes')->max('id');
                    $annee = date("Y");
                    $numero_id = sprintf("%06d", ($maxIdVente + 1));
                    if(Auth::user()->role!="Caissier"){
                        $caisse_ouverte = CaisseOuverte::where([['caisse_id',$data['caisse_id']],['date_fermeture',null]])->first();
                        if(!$caisse_ouverte){
                            return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => NULL]);
                        }
                    }else{
                        //Recupértion de la caisse dans la session
                        if($request->session()->has('session_caisse_ouverte')){
                            $caisse_ouverte_id = $request->session()->get('session_caisse_ouverte');
                            $caisse_ouverte = CaisseOuverte::find($caisse_ouverte_id);
                        }
                        if(!$caisse_ouverte or $caisse_ouverte->date_fermeture != null){
                            return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => NULL]);
                        }
                    }
                    
                    
                    if($data['montant_a_payer'] > $data['montant_payer'] && !isset($data['attente'])){
                        return response()->json(["code" => 0, "msg" => "Le montant payé n'est pas juste", "data" => NULL]);
                    }
                    
                    $vente = new Vente;
                    $vente->numero_ticket = "TICKET".$annee.$numero_id;
                    $vente->depot_id = $data["depot_id"];
                    $vente->caisse_ouverte_id = $caisse_ouverte->id;
                    $vente->montant_payer = isset($data['montant_payer']) && !empty($data['montant_payer']) ? $data['montant_payer'] : 0;
                    $vente->attente = isset($data['attente']) && !empty($data['attente']) ? TRUE : FALSE;
                    $vente->date_vente = now();
                    $vente->created_by = Auth::user()->id;
                    $vente->save();
                    $date_jour = date("Y-m-d");
                    //Ajout des articles dans la vente
                    if($vente && !empty($data["monPanier"])){
                        //enregistrement des articles de l'approvisionnement  
                        $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        $montantTTC = 0;
                        foreach($panierContent as $index => $article) {
                            
                            $ArticleVente = ArticleVente::where([['vente_id',$vente->id],['depot_id',$data["depot_id"]],['unite_id',$data["monPanier"][$index]["unites"]],['article_id',$data["monPanier"][$index]["articles"]]])->first();
                            if($ArticleVente!=null){
                            }else{
                                $articleVente = new ArticleVente();
                                $articleVente->article_id = $data["monPanier"][$index]["articles"];
                                $articleVente->vente_id = $vente->id;
                                $articleVente->depot_id = $data["depot_id"];
                                $articleVente->unite_id = $data["monPanier"][$index]["unites"];
                                $articleVente->quantite = $data["monPanier"][$index]["quantites"];
                                $articleVente->prix = $data["monPanier"][$index]["prix"];
                                $articleVente->remise_sur_ligne = $data["monPanier"][$index]["remises"];
                                $articleVente->created_by = Auth::user()->id;
                                $articleVente->save();

                                //Vérifions si l'article est stockable ou non
                                $Article = Article::find($data["monPanier"][$index]["articles"]);
                                if($Article!=null && $Article->stockable == 1) {
                                    //Dimunition stock dans depot-article
                                    $DepotArticle = DepotArticle::where([['depot_id', $data["depot_id"]], ['article_id', $data["monPanier"][$index]["articles"]], ['unite_id', $data["monPanier"][$index]["unites"]]])->first();
                                    $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["monPanier"][$index]["articles"]], ['unite_id', $data["monPanier"][$index]["unites"]]])->whereDate('date_mouvement', $date_jour)->first();
                                    if (!$mouvementStock) {
                                        $mouvementStock = new MouvementStock;
                                        $mouvementStock->date_mouvement = $date_jour;
                                        $mouvementStock->depot_id = $data['depot_id'];
                                        $mouvementStock->article_id = $data["monPanier"][$index]["articles"];
                                        $mouvementStock->unite_id = $data["monPanier"][$index]["unites"];
                                        $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                                        $mouvementStock->created_by = Auth::user()->id;
                                    }
                                    $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $data["monPanier"][$index]["quantites"];
                                    $DepotArticle->save();
                                    $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data["monPanier"][$index]["quantites"];
                                    $mouvementStock->save();
                                }
                                //Calcule du montant TTC
                                    $montantTTC = $montantTTC + (($data["monPanier"][$index]["quantites"] * $data["monPanier"][$index]["prix"])-$data["monPanier"][$index]["remises"]);
                            }
                        }
                    }
                    //Encaissement
                    if(!isset($data['attente'])){
                        
                        $reglement = new Reglement();
                        $reglement->montant_reglement = $montantTTC;
                        $reglement->moyen_reglement_id = $data['moyen_reglement_id'];
                        $reglement->date_reglement = now();
                        $reglement->caisse_ouverte_id = $caisse_ouverte->id;
                        $reglement->vente_id = $vente->id;
                        $reglement->created_by = Auth::user()->id;
                        $reglement->save();
                    }
                }
                
                //Si la vente concernen une facture client
                if(isset($data['client_id']) && !empty($data['client_id']) && !isset($data['divers'])) {
                    
                    //Recuperation du crédit total du client
                    $vente_clients = Vente::where([['ventes.deleted_at', NULL],['ventes.proformat',0]])
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.acompte_facture',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->Where('ventes.client_id',$data['client_id'])
                            ->groupBy('article_ventes.vente_id')
                            ->get();
                    if($vente_clients!=null){
                        $credtiTotal = 0;
                        $client = Client::find($data['client_id']);
                        foreach ($vente_clients as $credit_client){
                            $credtiTotal = $credtiTotal + ($credit_client->sommeTotale-$credit_client->acompte_facture);
                        }
                    
                        //Recuperation du montant total du panier
                        $montantPanier = 0;
                        $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        foreach($panierContent as $index => $article) {
                            $montantPanier = $montantPanier + (($data["monPanier"][$index]["quantites"]*$data["monPanier"][$index]["prix"])-$data["monPanier"][$index]["remises"]);
                        }
                        //Vérification du montant total des crédits + doit et le montant plafond
                        if(($montantPanier+$credtiTotal)>$client->plafond_client && $client->plafond_client!=0){
                            return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de ".number_format((($montantPanier+$credtiTotal)-$client->plafond_client), 0, ',', ' ')." F CFA", "data" => NULL]);
                        }
                    }
                    //formation numéro facture
                    $maxIdVente = DB::table('ventes')->max('id');
                    $numero_facture = sprintf("%06d", ($maxIdVente + 1));

                    $vente = new Vente;
                    $vente->numero_facture = $numero_facture;
                    $vente->depot_id = $data['depot_id'];
                    $vente->date_vente = Carbon::createFromFormat('d-m-Y', $data['date_vente']);
                    $vente->client_id = $data['client_id'];
                    $vente->proformat = isset($data['proformat']) && !empty($data['proformat']) ? TRUE : FALSE;
                    $vente->created_by = Auth::user()->id;
                    $vente->save();
                    
                    //Ajout des articles dans la vente
                    if($vente!=null && !empty($data["monPanier"])) {
                        //enregistrement des articles de l'approvisionnement  
                        $panierContents = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        foreach($panierContents as $index => $article) {
                            //empecher d'enregistrer un article du meme lot 2 fois sur la vente
                            $ArticleVente = ArticleVente::where([['vente_id',$vente->id],['article_id', $data["monPanier"][$index]["articles"]],['depot_id',$data["depot_id"],['unite_id',$data["monPanier"][$index]["unites"]]]])->first();
                            if($ArticleVente!=null){
                            }else{
                                $articleVente = new ArticleVente();
                                $articleVente->article_id = $data["monPanier"][$index]["articles"];
                                $articleVente->vente_id = $vente->id;
                                $articleVente->depot_id = $data["depot_id"];
                                $articleVente->unite_id = $data["monPanier"][$index]["unites"];
                                $articleVente->quantite = $data["monPanier"][$index]["quantites"];
                                $articleVente->prix = $data["monPanier"][$index]["prix"];
                                $articleVente->remise_sur_ligne = $data["monPanier"][$index]["remises"];
                                $articleVente->created_by = Auth::user()->id;
                                $articleVente->save();
                                
                            //Vérifions si l'article est stockable ou non
                            $Article = Article::find($data["monPanier"][$index]["articles"]);
                                if ($Article != null && $Article->stockable == 1) {
                                    //Dimunition stock dans depot-article
                                    if ($vente->proformat == 0) {
                                        $DepotArticle = DepotArticle::where([['depot_id', $data["depot_id"]], ['article_id', $data["monPanier"][$index]["articles"]], ['unite_id', $data["monPanier"][$index]["unites"]]])->first();
                                        $mouvementStock = MouvementStock::where([['depot_id', $data['depot_id']], ['article_id', $data["monPanier"][$index]["articles"]], ['unite_id', $data["monPanier"][$index]["unites"]]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_vente']))->first();
                                        if (!$mouvementStock) {
                                            $mouvementStock = new MouvementStock;
                                            $mouvementStock->date_mouvement = Carbon::createFromFormat('d-m-Y', $data['date_vente']);
                                            $mouvementStock->depot_id = $data['depot_id'];
                                            $mouvementStock->article_id = $data["monPanier"][$index]["articles"];
                                            $mouvementStock->unite_id = $data["monPanier"][$index]["unites"];
                                            $mouvementStock->quantite_initiale = $DepotArticle != null ? $DepotArticle->quantite_disponible : 0;
                                            $mouvementStock->created_by = Auth::user()->id;
                                        }
                                        $DepotArticle->quantite_disponible = $DepotArticle->quantite_disponible - $data["monPanier"][$index]["quantites"];
                                        $DepotArticle->save();
                                        $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue + $data["monPanier"][$index]["quantites"];
                                        $mouvementStock->save();
                                    }
                                }
                            }
                        }                      
                    }
                }
                
                //Si la vente concerne un divers ou un article non stockable
                if(isset($data['divers']) && !empty($data['divers'])){
                     //Recuperation du crédit total du client
                    $vente_clients = Vente::where([['ventes.deleted_at', NULL],['ventes.proformat',0]])
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.acompte_facture',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->Where('ventes.client_id',$data['client_id'])
                            ->groupBy('article_ventes.vente_id')
                            ->get();
                    if($vente_clients!=null){
                        $credtiTotal = 0;
                        $client = Client::find($data['client_id']);
                        foreach ($vente_clients as $credit_client){
                            $credtiTotal = $credtiTotal + ($credit_client->sommeTotale-$credit_client->acompte_facture);
                        }
                    
                        //Recuperation du montant total du panier
                        $montantPanier = 0;
                        $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        foreach($panierContent as $index => $article) {
                            $montantPanier = $montantPanier + (($data["monPanier"][$index]["quantites"]*$data["monPanier"][$index]["prix"]));
                        }
                        //Vérification du montant total des crédits + doit et le montant plafond
                        if(($montantPanier+$credtiTotal)>$client->plafond_client && $client->plafond_client!=0){
                            return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de ".number_format((($montantPanier+$credtiTotal)-$client->plafond_client), 0, ',', ' ')." F CFA", "data" => NULL]);
                        }
                    }
                    //formation numéro facture
                    $maxIdVente = DB::table('ventes')->max('id');
                    $numero_facture = sprintf("%06d", ($maxIdVente + 1));

                    $vente = new Vente;
                    $vente->numero_facture = $numero_facture;
                    $vente->date_vente = Carbon::createFromFormat('d-m-Y', $data['date_vente']);
                    $vente->client_id = $data['client_id'];
                    $vente->divers = TRUE;
                    $vente->created_by = Auth::user()->id;
                    $vente->save();
                    
                    //Ajout des articles dans la vente
                    if($vente!=null && !empty($data["monPanier"])) {
                        //enregistrement des articles de l'approvisionnement  
                        $panierContents = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                        $montantTTC = 0;
                        foreach($panierContents as $index => $article) {
                            //empecher d'enregistrer un article du meme lot 2 fois sur la vente
                            $ArticleVente = ArticleVente::where([['vente_id',$vente->id],['divers_id', $data["monPanier"][$index]["divers"]]])->first();
                            if($ArticleVente!=null){
                            }else{
                                $articleVente = new ArticleVente();
                                $articleVente->divers_id = $data["monPanier"][$index]["divers"];
                                $articleVente->vente_id = $vente->id;
                                $articleVente->quantite = $data["monPanier"][$index]["quantites"];
                                $articleVente->prix = $data["monPanier"][$index]["prix"];
                                $articleVente->created_by = Auth::user()->id;
                                $articleVente->save();
                            }
                        }                      
                    }
                }

            $jsonData["data"] = json_decode($vente);
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
     * @param  \App\Vente  $vente
     * @return Response
     */
    public function updateVente(Request $request)
    {
        $vente = Vente::find($request->get('idVente'));
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        if($vente){
            $data = $request->all(); 
            try {
              
            if($vente->client_id==null){
                if(($data['montant_a_payer_add'] > $data['montant_payer_add'] && !isset($data['attente'])) or ($data['montant_a_payer_add'] > $data['montant_payer_add'] && $vente->attente==0)){
                    return response()->json(["code" => 0, "msg" => "Le montant payé n'est pas juste", "data" => NULL]);
                }
                //Si l'admin ou le gérant veut faire une modification
                if(Auth::user()->role != 'Caissier' && $vente->caisse_ouverte_id!=null){
                        $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                        if ($caisse_ouverte==null or $caisse_ouverte->date_fermeture!=null) {
                         
                            return response()->json(["code" => 0, "msg" => "Modification impossible car la caisse est fermée", "data" => NULL]);
                        }
                }
                if(Auth::user()->role == 'Caissier' && $vente->caisse_ouverte_id!=null){
                    $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                    if (!$caisse_ouverte && $caisse_ouverte->date_fermeture!=null) {
                         return response()->json(["code" => 0, "msg" => "Cette caisse est fermée", "data" => NULL]);
                    }
                }
               
                $vente->montant_payer = $data['montant_payer_add'];   
                $vente->attente = isset($data['attente']) && !empty($data['attente']) ? TRUE : FALSE;
                $vente->updated_by = Auth::user()->id;
                $vente->save();
                
                    //Encaissement
                    if ($vente->attente == 0) {
                        $reglement = Reglement::where('vente_id', $vente->id)->first();
                        if ($reglement != null && $caisse_ouverte != null) {
                            $reglement->montant_reglement = $data['montant_a_payer_add'];
                            $reglement->moyen_reglement_id = $data['moyen_reglement_id_add'];
                            $reglement->updated_by = Auth::user()->id;
                            $reglement->save();

                        } else {
                            $newReglement = new Reglement;
                            $newReglement->montant_reglement = $data['montant_a_payer_add'];
                            $newReglement->moyen_reglement_id = $data['moyen_reglement_id_add'];
                            $newReglement->updated_by = Auth::user()->id;
                            $newReglement->save();

                        }
                    }
                }
            if($vente->client_id!=null && $vente->divers==0){
                $proformat = $vente->proformat;
                if($vente->proformat==0 && (isset($data['proformat']) && !empty($data['proformat']))){
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleVentes = ArticleVente::where('vente_id',$vente->id)->get();
                    foreach($articleVentes as $articleVente) {
                        $Article = Article::find($articleVente->article_id);
                        if ($Article != null && $Article->stockable == 1) {
                            $articleDepot = DepotArticle::where([['article_id',$articleVente->article_id],['depot_id',$vente->depot_id],['unite_id',$articleVente->unite_id]])->first();
                            $articleDepot->quantite_disponible = $articleDepot->quantite_disponible - $articleVente->quantite;
                            $articleDepot->save();         
                        }
                    }
                }
                if($vente->proformat==1 && (!isset($data['proformat']) && empty($data['proformat']))){
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleVentes = ArticleVente::where('vente_id',$vente->id)->get();
                    foreach($articleVentes as $articleVente) {
                        $Article = Article::find($articleVente->article_id);
                        if ($Article != null && $Article->stockable == 1) {
                            $articleDepot = DepotArticle::where([['article_id',$articleVente->article_id],['depot_id',$vente->depot_id],['unite_id',$articleVente->unite_id]])->first();
                            $articleDepot->quantite_disponible = $articleDepot->quantite_disponible + $articleVente->quantite;
                            $articleDepot->save(); 
                        }
                    }
                }
                
                $vente->date_vente = Carbon::createFromFormat('d-m-Y', $data['date_vente']);
                $vente->client_id = $data['client_id'];
                $vente->proformat = isset($data['proformat']) && !empty($data['proformat']) ? TRUE : FALSE;
                $vente->updated_by = Auth::user()->id;
                $vente->save();
            
                if($proformat==1 && $vente->proformat==0){
                     //Recuperation du crédit total du client
                    $vente_clients = Vente::where([['ventes.deleted_at', NULL], ['ventes.proformat', 0]])
                            ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.acompte_facture', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->Where('ventes.client_id', $vente->client_id)
                            ->groupBy('article_ventes.vente_id')
                            ->get();
                    if($vente_clients!=null){
                       $credtiTotal = 0;
                       $client = Client::find($vente->client_id);
                       foreach ($vente_clients as $credit_client){
                           $credtiTotal = $credtiTotal + ($credit_client->sommeTotale-$credit_client->acompte_facture);
                       }

                       //Recuperation du montant total du panier
                       $montantPanier = 0;
                       $panierContents = ArticleVente::where('vente_id',$vente->id)->get();
                       foreach($panierContents as $panierContent) {
                           $montantPanier = $montantPanier + (($panierContent->quantite*$panierContent->prix)-$panierContent->remise_sur_ligne);
                       }
                       //Vérification du montant total des crédits + doit et le montant plafond
                       if(($montantPanier+$credtiTotal)>$client->plafond_client && $client->plafond_client!=0){
                            $vente->proformat = TRUE;
                            $vente->updated_by = Auth::user()->id;
                            $vente->save();
                           return response()->json(["code" => 0, "msg" => "Le montant plafond du client est depassé de ".number_format((($montantPanier+$credtiTotal)-$client->plafond_client), 0, ',', ' ')." F CFA", "data" => NULL]);
                       }
                   }
                }
            }
            if($vente->divers==1){
               
                $vente->date_vente = Carbon::createFromFormat('d-m-Y', $data['date_vente']);
                $vente->client_id = $data['client_id'];
                $vente->divers = TRUE;
                $vente->updated_by = Auth::user()->id;
                $vente->save();
            }
            $jsonData["data"] = json_decode($vente);
            return response()->json($jsonData);
            } catch (Exception $exc) {
               $jsonData["code"] = -1;
               $jsonData["data"] = NULL;
               $jsonData["msg"] = $exc->getMessage();
               return response()->json($jsonData); 
            }

        }
        return response()->json(["code" => 0, "msg" => "Echec de modification", "data" => NULL]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vente  $vente
     * @return Response
     */
    public function destroy(Vente $vente)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($vente){
                try {
                if($vente->client_id==null){
                    if ($vente->caisse_ouverte_id != null) {
                        $ventes = Vente::with('depot', 'caisse_ouverte')
                                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')->Where('article_ventes.deleted_at', NULL)
                                ->select('ventes.*', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                                ->Where([['ventes.deleted_at', NULL], ['ventes.client_id', null], ['ventes.id', $vente->id]])
                                ->groupBy('article_ventes.vente_id')
                                ->first();
                        $caisse_ouverte = CaisseOuverte::find($vente->caisse_ouverte_id);
                        if ($caisse_ouverte != null && $caisse_ouverte->date_fermeture == null) {
                            $reglement = Reglement::where('vente_id', $vente->id)->first();
                            $reglement->update(['deleted_by' => Auth::user()->id]);
                            $reglement->delete();
                       
                        } else {
                            return response()->json(["code" => 0, "msg" => "Sppression impossible car la caisse est fermée", "data" => NULL]);
                        }
                    }
                }
            
                if($vente->proformat==0 && $vente->divers==0){
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleVentes = ArticleVente::where('vente_id',$vente->id)->get();
                    foreach($articleVentes as $articleVente) {
                        $Article = Article::find($articleVente->article_id);
                        if ($Article != null && $Article->stockable == 1) {
                            $articleDepot = DepotArticle::where([['article_id',$articleVente->article_id],['depot_id',$vente->depot_id],['unite_id',$articleVente->unite_id]])->first();
                            $articleDepot->quantite_disponible = $articleDepot->quantite_disponible + $articleVente->quantite;
                            $articleDepot->save(); 
                            
                            $mouvementStock = MouvementStock::where([['depot_id', $vente->depot_id], ['article_id', $articleVente->article_id], ['unite_id', $articleVente->unite_id]])->whereDate('date_mouvement', date_format($vente->date_vente,"Y-m-d"))->first();
                            $mouvementStock->quantite_vendue = $mouvementStock->quantite_vendue - $articleVente->quantite;
                            $mouvementStock->save();
                        }
                    }
                }
            
                $vente->update(['deleted_by' => Auth::user()->id]);
                $vente->delete();
                $jsonData["data"] = json_decode($vente);
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
    
    //Ticket
    public function ticketVentePdf($vente){
         $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->ticketVente($vente));
        $ticket = Vente::find($vente);
        // Auto print
        //$printerId = Printing::defaultPrinetrId();
        //$printJod = Printing::newPrintTask()->printer($printerId)->file($pdf->stream('Ticket_'.$ticket->numero_ticket.'.pdf'))->send();

        return $pdf->stream('Ticket_'.$ticket->numero_ticket.'.pdf');
    }
    public function ticketVente($vente){
        $outPut = $this->ticketHeader($vente);
        $outPut.= $this->ticketContent($vente);
        return $outPut;
    }
    public function ticketHeader($vente){
        $info_en_tete = Vente::with('depot','caisse_ouverte')
                        ->join('caisse_ouvertes','caisse_ouvertes.id','=','ventes.caisse_ouverte_id')
                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')    
                        ->join('reglements','reglements.id','=','reglements.caisse_ouverte_id')
                        ->join('moyen_reglements','moyen_reglements.id','=','reglements.moyen_reglement_id')
                        ->join('users','users.id','=','ventes.created_by')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                        ->select('ventes.*','moyen_reglements.libelle_moyen_reglement','caisses.libelle_caisse','users.full_name',DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y à %H:%i:%s") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id', NULL],['ventes.id',$vente]])
                        ->first();
          
        $header = "<html>
                        <head>
                            <meta charset='utf-8'>
                            <title></title>
                                    <style>
                                        @page { size: 30cm 15cm landscape; }
                                       .container{
                                            width: 100%;
                                            margin: 0 5px;
                                            font-size:28px;
                                        }
                                        .container-table{        
                                            width: 100%;
                                        }
                                    </style>
                        </head>
                <body style='margin-bottom:0; margin-top:0px; font-size:24px;'>
                <p style='text-align:center; font-size:24px;'>
                    <b><u>TICKET DE CAISSE</u></b><br/>
                    <img src=".$this->infosConfig()->logo." width='200' height='140'/><br/>
                     ".$this->infosConfig()->commune_compagnie."<br/>
                    Tel : ".$this->infosConfig()->cellulaire."<br/>
                     ".$this->infosConfig()->email_compagnie."
                    <hr/>
                </p>
                <p style='line-height:1.6; font-size:22px;'>
                   Du <b>".$info_en_tete['date_ventes']."</b> au <b>".$info_en_tete['depot']['libelle_depot']."</b><br/>
                   Caisse : <b>".$info_en_tete['libelle_caisse']."</b><br/>
                   Caissier(e) : <b>".$info_en_tete['full_name']."</b><br/>
                   Règlement : <b>".$info_en_tete['libelle_moyen_reglement']."</b>
                   <hr/>
                </p>";     
        return $header;
    }
    public function ticketContent($vente){
        $generator = new BarcodeGeneratorPNG();
        $montantTHT_add = 0; $montantTTTC_add=0; $remise = 0; $tva=0;
        $vente_info = Vente::with('depot','caisse_ouverte')
                        ->join('caisse_ouvertes','caisse_ouvertes.id','=','ventes.caisse_ouverte_id')
                        ->join('reglements','reglements.id','=','reglements.caisse_ouverte_id')
                        ->join('moyen_reglements','moyen_reglements.id','=','reglements.moyen_reglement_id')
                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                        ->select('ventes.*','moyen_reglements.libelle_moyen_reglement','caisses.libelle_caisse','users.full_name', DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id', NULL],['ventes.id',$vente]])
                        ->groupBy('article_ventes.vente_id')
                        ->first();
        $articlesVentes =  ArticleVente::with('article','unite')
                ->join('articles','articles.id','=','article_ventes.article_id')
                ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                ->select('article_ventes.*','param_tvas.montant_tva')
                ->Where([['article_ventes.deleted_at', NULL],['article_ventes.vente_id',$vente]])
                ->get();
        
        foreach ($articlesVentes as $article){
            if($article->article->param_tva_id!=0){
                $prix = round($article->prix/($article->montant_tva+1), 0);
                $montantTHT_add = $montantTHT_add + $prix*$article->quantite;
            }else{
                $montantTHT_add = $montantTHT_add + $article->prix*$article->quantite;
            }
            $montantTTTC_add = $montantTTTC_add + $article->prix*$article->quantite;
            $remise = $remise + $article->remise_sur_ligne;
        }
        
        $content = '<div class="container-table" style="font-size:22px;">
                        <table border="0" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="45%" align="left">Article</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                                <th cellspacing="0" border="2" width="25%" align="center">Prix TTC</th>'
                                //<th cellspacing="0" border="2" width="30%" align="center">Montant TTC</th>
                                //<th cellspacing="0" border="2" width="15%" align="center">TVA</th>
                                .'<th cellspacing="0" border="2" width="20%" align="center">Remise</th>
                            </tr>';
        $articlesTotal = 0; $montantApayer = 0;
        foreach($articlesVentes as $element){
            $articlesTotal = $articlesTotal +1;
            if($element->article->param_tva_id!=0){
                $tva = $element->montant_tva;
            }else{
                $tva =0;
            }
            $montantApayer = $montantApayer + (($element->prix*$element->quantite)-$element->remise_sur_ligne);
            $content.='<tr>
                            <td style="font-size:22px;"  cellspacing="0" border="2" align="left" width="45%">'.$element->article->description_article.'</td>
                            <td style="font-size:22px;"  cellspacing="0" border="2" align="center" width="10%">'.$element->quantite.'</td>
                            <td style="font-size:22px;"  cellspacing="0" border="2" align="right" width="25%">'.number_format($element->prix, 0, ',', ' ').'&nbsp;&nbsp;&nbsp; </td>'
                            //<td style="font-size:22px;"  cellspacing="0" border="2" align="right" width="30%">'.number_format($element->prix*$element->quantite, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                            //<td style="font-size:22px;"  cellspacing="0" border="2" align="right" width="10%">'.number_format($tva*100, 0, ',', ' ').' %&nbsp;&nbsp;&nbsp;</td>
                            .'<td style="font-size:22px;"  cellspacing="0" border="2" align="right" width="20%">'.number_format($element->remise_sur_ligne, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }
        $content.='</table><hr/>';
        $content.='<p style="font-size:21px;">Total articles : '.$articlesTotal.'</p>';
        $content.= //'<p align="right"  style="font-size:16px;">Total HT &nbsp;&nbsp;'.number_format($montantTHT_add, 0, ',', ' ').'</p>
                   //<p align="right"  style="font-size:16px;">Montant TVA &nbsp;&nbsp;'.number_format($montantTTTC_add-$montantTHT_add, 0, ',', ' ').'</p>
                   '<p align="right"  style="font-size:21px;">Remise &nbsp;&nbsp;'.number_format($remise, 0, ',', ' ').' FCFA</p>
                   <p align="right" style="font-size:21px;"><b>NET A PAYER &nbsp;&nbsp;'.number_format($montantTTTC_add-$remise, 0, ',', ' ').' FCFA</b></p>
                   <p align="right" style="font-size:21px;"><b>Espèce reçu :&nbsp;&nbsp;'.number_format($vente_info['montant_payer'], 0, ',', ' ').' FCFA</b></p>
                   <p align="right" style="font-size:21px;"><b>Espèce rendu : &nbsp;&nbsp;'.number_format($vente_info['montant_payer']-$montantApayer, 0, ',', ' ').'  FCFA</b></p><hr/>
                   <p align="center" style="font-size:24px;"><b>Merci de votre visite. Repassez nous voir.</b></p>
                   <p align="center"><img src="data:image/png;base64,'.base64_encode($generator->getBarcode(123456789, $generator::TYPE_CODE_128)).'"></p>
                   <p align="center" style="font-size:21px;"><b>'.$vente_info["numero_ticket"].'</b></p>
                   <p align="center" style="font-size:21px;"><i>&nbsp;&nbsp;&nbsp;&nbsp;Editer le '.date('d-m-Y').' à '.date("H:i:s").'</i></p>
                </div>';
       return $content;
    }

    //Facture vente ou proforma
    public function factureVentePdf($vente){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->factureVente($vente));
        $facture = Vente::find($vente);
        return $pdf->stream('Facture'.$facture->numero_facture.'.pdf');
    }
    public function factureVente($vente){
        $outPut = $this->factureHeader($vente);
        $outPut.= $this->factureContent($vente);
        $outPut.= $this->factureFooter();
        return $outPut;
    }
    public function factureContent($vente){
       
        $montantTHT_add = 0; $montantTTTC_add=0;
        $articlesVentes =  ArticleVente::with('article','unite')
                ->join('articles','articles.id','=','article_ventes.article_id')
                ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                ->select('article_ventes.*','param_tvas.montant_tva')
                ->Where([['article_ventes.vente_id',$vente],['article_ventes.retourne',0]])
                ->get();
        foreach ($articlesVentes as $article){
            if($article->article->param_tva_id!=0){
                $prix = round($article->prix/($article->montant_tva+1), 0);
                $montantTHT_add = $montantTHT_add + $prix*$article->quantite;
            }else{
                $montantTHT_add = $montantTHT_add + $article->prix*$article->quantite;
            }
            $montantTTTC_add = $montantTTTC_add + $article->prix*$article->quantite;
        }
        
        $content = '<div class="container-table">
                        <table border="1" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="55%" align="center">Article</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Prix TTC.</th>
                                <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                            </tr>';
        
        foreach($articlesVentes as $element){
            $content.='<tr>
                            <td style="font-size:13px;"  cellspacing="0" border="2" width="55%">&nbsp;&nbsp;&nbsp;'.$element->article->description_article.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center" width="10%">'.$element->quantite.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right" width="15%">'.number_format($element->prix, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right" width="20%">'.number_format($element->prix*$element->quantite, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }
        
        $content.='<tr>
                        <td style="font-size:13px;"  cellspacing="0" colspan="3" border="2" align="left" width="70%">&nbsp;&nbsp;Total HT</td>
                        <td style="font-size:15px;"  cellspacing="0" colspan="3" border="2" align="right" width="30%">&nbsp;&nbsp;'.number_format($montantTHT_add, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                    </tr> 
                    <tr>
                        <td style="font-size:13px;"  cellspacing="0" colspan="3" border="2" align="left" width="70%">&nbsp;&nbsp;Montant TVA</td>
                        <td style="font-size:15px;"  cellspacing="0" colspan="3" border="2" align="right" width="30%">&nbsp;&nbsp;'.number_format($montantTTTC_add-$montantTHT_add, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                    </tr> 
                    <tr>
                        <td style="font-size:13px;"  cellspacing="0" colspan="3" border="2" align="left" width="70%"><b>&nbsp;&nbsp;NET A PAYER</b></td>
                        <td style="font-size:15px;"  cellspacing="0" colspan="3" border="2" align="right" width="30%">&nbsp;&nbsp;<b>'.number_format($montantTTTC_add, 0, ',', ' ').'</b>&nbsp;&nbsp;&nbsp;</td>
                    </tr>  
                </table>
                <p style="font-style: italic;"> NET A PAYER <b>'.ucfirst(NumberToLetter($montantTTTC_add)).' F CFA</b></p>
         </div>';
        
       return $content;
    }
    
    //Facture divers
    public function factureVenteDiversPdf($vente){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->factureVenteDivers($vente));
        $facture = Vente::find($vente);
        return $pdf->stream('Facture'.$facture->numero_facture.'.pdf');
    }
    public function factureVenteDivers($vente){
        $outPut = $this->factureHeader($vente);
        $outPut.= $this->factureVenteDiversContent($vente);
        $outPut.= $this->factureFooter();
        return $outPut;
    }
    public function factureVenteDiversContent($vente){
        
        $montantTTTC_add=0;
        $articlesVentes =  ArticleVente::where('article_ventes.vente_id',$vente)
                ->join('divers','divers.id','=','article_ventes.divers_id')
                ->select('article_ventes.*','divers.libelle_divers')
                ->get();
        
        $content = '<div class="container-table">
                        <table border="1" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="55%" align="center">Article</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Prix TTC.</th>
                                <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                            </tr>';
        
        foreach($articlesVentes as $element){
            $montantTTTC_add = $montantTTTC_add + $element->quantite*$element->prix;
            $content.='<tr>
                            <td style="font-size:13px;"  cellspacing="0" border="2" width="55%">&nbsp;&nbsp;&nbsp;'.$element->libelle_divers.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center" width="10%">'.$element->quantite.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right" width="15%">'.number_format($element->prix, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="right" width="20%">'.number_format($element->prix*$element->quantite, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }
        
        $content.='<tr>
                        <td style="font-size:13px;"  cellspacing="0" colspan="3" border="2" align="left" width="70%"><b>&nbsp;&nbsp;NET A PAYER</b></td>
                        <td style="font-size:15px;"  cellspacing="0" colspan="3" border="2" align="right" width="30%">&nbsp;&nbsp;<b>'.number_format($montantTTTC_add, 0, ',', ' ').'</b>&nbsp;&nbsp;&nbsp;</td>
                    </tr>  
                </table>
                <p style="font-style: italic;"> NET A PAYER <b>'.ucfirst(NumberToLetter($montantTTTC_add)).' F CFA</b></p>
         </div>';
        
       return $content;
    }
    
     //Header & footer facture
    public function factureHeader($vente){
        $facture = Vente::with('client')
                    ->select('ventes.*',DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.id',$vente]])
                    ->first();
                    $nom_client = $facture->client->full_name_client;
                    $contact_client = $facture->client->contact_client;
                    $adresse_client = $facture->client->adresse_client;
                    $facture->proformat == 1 ? $facture_proformat = " proforma " : $facture_proformat = "";
        $header = "<html>
                         <head>
                            <meta charset='utf-8'>
                            <title></title>
                                    <style>
                                        .container-table{        
                                            margin:200px 0;
                                            width: 100%;
                                        }
                                        .container{
                                            width: 100%;
                                            margin: 2px 5px;
                                            font-size:15px;
                                        }
                                        .fixed-header-left{
                                            width: 34%;
                                            height:4%;
                                            position: absolute; 
                                            line-height:1;
                                            font-size:13px;
                                            top: 0;
                                        }
                                        .fixed-header-right{
                                            width: 40%;
                                            height:6%;
                                            float: right;
                                            position: absolute;
                                            top: 0;
                                            background: #fff;
                                            padding: 10px 0;
                                            color: #333;
                                            border: 1px #333 solid;
                                            border-radius: 3px;
                                        }
                                        .fixed-header-center{
                                            width:35%;
                                            height:7%;
                                            margin: 0 150px;
                                            top: 0;
                                            text-align:center;
                                            position: absolute; 
                                        }
                                        .fixed-footer{
                                            position: fixed; 
                                            bottom: -28; 
                                            left: 0px; 
                                            right: 0px;
                                            height: 80px; 
                                            text-align:center;
                                        }     
                                        .titre-style{
                                         text-align:center;
                                         text-decoration: underline;
                                        }
                                    footer{
                                    font-size:13px;
                                    position: absolute; 
                                    bottom: -35px; 
                                    left: 0px; 
                                    right: 0px;
                                    height: 80px; 
                                    text-align:center;
                                    }
                                    </style>
                        </head>
                <body style='margin-bottom:0; margin-top:0px;'>
                 <div class='fixed-header-left'>
                    <div class='container'>
                         <img src=".$this->infosConfig()->logo." width='200' height='160'/> 
                    </div>
                </div>
                <div class='fixed-header-center'>
                    <div class='container'>
                       Facture ".$facture_proformat." N° : <b>".$facture->numero_facture."</b><br/>
                       Date : <b>".$facture->date_ventes."</b><br/>
                    </div>
                </div>
                <div class='fixed-header-right'>
                    <div class='container'>
                       Doit : <b>".$nom_client."</b><br/>
                       Contact : <b>".$contact_client."</b><br/>
                       Adresse : <b>".$adresse_client."</b>
                    </div>
                </div>";     
        return $header;
    }
     //Footer fiche
    public function factureFooter(){
        $type_compagnie='';$capital='';$rccm='';$ncc='';$adresse_compagnie='';$numero_compte_banque='';$banque='';$nc_tresor='';$email_compagnie='';$cellulaire=''; $telephone_faxe=''; $telephone_fixe='';
        $search  = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
        $replace = array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');
        $nom_compagnie = str_replace($search, $replace, $this->infosConfig()->nom_compagnie);
        if($this->infosConfig()->type_compagnie!=null){
            $type_compagnie = $this->infosConfig()->type_compagnie;
        }
        if($this->infosConfig()->capital!=null){
            $capital = ' au capital de '.number_format($this->infosConfig()->capital, 0, ',', ' ').' F CFA';
        }
        if($this->infosConfig()->rccm!=null){
            $rccm = ' RCCM '.$this->infosConfig()->rccm;
        }
        if($this->infosConfig()->ncc!=null){
            $ncc = ' NCC '.$this->infosConfig()->ncc;
        }
        if($this->infosConfig()->adresse_compagnie!=null){
            $adresse_compagnie = ' Siège social: '.$this->infosConfig()->adresse_compagnie;
        }
        if($this->infosConfig()->numero_compte_banque!=null){
            $numero_compte_banque =$this->infosConfig()->numero_compte_banque;
        }
        if($this->infosConfig()->banque!=null){
            $banque = 'N° de compte - '.$this->infosConfig()->banque.': ';
        }
        if($this->infosConfig()->nc_tresor!=null){
            $nc_tresor = ' - TRESOR: '.$this->infosConfig()->nc_tresor;
        }
        if($this->infosConfig()->email_compagnie!=null){
            $email_compagnie = ' Email : '.$this->infosConfig()->email_compagnie;
        }
        if($this->infosConfig()->cellulaire!=null){
            $cellulaire = ' / '.$this->infosConfig()->cellulaire;
        }
        if($this->infosConfig()->telephone_faxe!=null){
            $telephone_faxe = ' Fax : '.$this->infosConfig()->telephone_faxe;
        }
        if($this->infosConfig()->telephone_fixe!=null){
            $telephone_fixe = ' Tel : '.$this->infosConfig()->telephone_fixe;
        }
        $footer ="<footer>
                        <hr width='100%'>
                      <b>".strtoupper($nom_compagnie)."</b><br/>
                      ".strtoupper($type_compagnie)."".$capital."".$rccm."".$ncc."".$adresse_compagnie."
                        ".$banque."".$numero_compte_banque."".$nc_tresor."".$email_compagnie."
                        Cel: ".$this->infosConfig()->contact_responsable."".$cellulaire."".$telephone_fixe."".$telephone_faxe."
               </footer>
            </body>
        </html>";
        return $footer;
    }
}
