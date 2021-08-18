<?php

namespace App\Http\Controllers\Boutique;

use App\Exports\ArticleByDepotExport;
use App\Http\Controllers\Controller;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\Promotions;
use App\Models\Parametre\Depot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use function response;

class DepotArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = Depot::where('depots.deleted_at',NULL)
               ->select('depots.*')
               ->orderBy('libelle_depot')
               ->get();
        
       $menuPrincipal = "Boutique";
       $titleControlleur = "Les des dépôts avec leurs articles";
       $btnModalAjout = "FALSE";
       return view('boutique.depot-articles.index',compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuArticleByUniteInDepot(Request $request){
    
       $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
       $depot_choisi = Depot::find($request->depot_id);
       $menuPrincipal = "Boutique";
       $titleControlleur = "Les des articles du dépôt ".$depot_choisi->libelle_depot;
       $btnModalAjout = "FALSE";
       return view('boutique.depot-articles.articles-in-depot',compact('categories', 'depot_choisi', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    

    public function listeArticleByUniteInDepot($depot_id){
        $articles = DepotArticle::with('unite','depot','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where('depot_articles.depot_id',$depot_id)
                ->select('depot_articles.*','articles.id as id_article','articles.description_article')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function listeArticleByUniteInDepotByCode($code){
        $articles = DepotArticle::with('unite','depot','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->leftJoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                ->where('articles.code_barre',$code)
                ->select('depot_articles.*','param_tvas.montant_tva')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function findArticleInDepot($article_id,$depot_id){
        $article = DepotArticle::where([['depot_articles.article_id',$article_id],['depot_articles.depot_id',$depot_id]])
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->select('depot_articles.*','articles.prix_achat_ttc')
                ->get();
        $jsonData["rows"] = $article->toArray();
        $jsonData["total"] = $article->count();
        return response()->json($jsonData);
    }
    
    public function listeArticlesByDepot($depot){
        $article = DepotArticle::where('depot_articles.depot_id',$depot)
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->select('articles.id as id_article','articles.description_article','depot_articles.quantite_disponible')
                ->get();
        $jsonData["rows"] = $article->toArray();
        $jsonData["total"] = $article->count();
        return response()->json($jsonData);
    }
    
    public function listeDepotByArticle($article){
        $depots = DepotArticle::with('depot','unite')
                ->select('depot_articles.*')
                ->join('depots', 'depots.id', '=', 'depot_articles.depot_id')
                ->where([['depot_articles.article_id', $article], ['depots.deleted_at', null]])
                ->get();
        $jsonData["rows"] = $depots->toArray();
        $jsonData["total"] = $depots->count();
        return response()->json($jsonData);
    }
    
    public function findArticleInDepotByUnite($article,$depot,$unite){
        $depots = DepotArticle::with('depot','unite','article')
                ->where([['depot_articles.article_id', $article], ['depot_articles.depot_id', $depot],['depot_articles.unite_id', $unite]])
                ->select('depot_articles.*',DB::raw('DATE_FORMAT(depot_articles.date_peremption, "%d-%m-%Y") as date_peremptions'))
                ->get();
        $jsonData["rows"] = $depots->toArray();
        $jsonData["total"] = $depots->count();
        return response()->json($jsonData);
    }
    
    public function findArticleInDepotByUniteCaisse($article,$depot,$unite){
        //On verifie si l'article est en promotion au moment de la vente
        $article_en_promotion = Promotions::where([['unite_id',$unite],['depot_id',$depot],['article_id',$article]])->first();
        if($article_en_promotion!=null){
            $date_jour = date("Y-m-d");
            $date_debut = date_format($article_en_promotion->date_debut,"Y-m-d");
            $date_fin = date_format($article_en_promotion->date_fin,"Y-m-d");
            
            if($date_debut<=$date_jour && $date_fin>=$date_jour && $article_en_promotion->en_promotion==1){
                $depots = DepotArticle::with('depot','unite','article')
                    ->join('promotions','promotions.article_id','depot_articles.article_id')
                    ->where([['depot_articles.article_id', $article], ['depot_articles.depot_id', $depot],['depot_articles.unite_id', $unite]])
                    ->select('depot_articles.*','promotions.prix_promotion as prix_ventes')
                    ->get();
            }else{
                $depots = DepotArticle::with('depot','unite','article')
                    ->where([['depot_articles.article_id', $article], ['depot_articles.depot_id', $depot],['depot_articles.unite_id', $unite]])
                    ->select('depot_articles.*','depot_articles.prix_vente as prix_ventes')
                    ->get();  
            }
        }else{
            $depots = DepotArticle::with('depot','unite','article')
                    ->where([['depot_articles.article_id', $article], ['depot_articles.depot_id', $depot],['depot_articles.unite_id', $unite]])
                    ->select('depot_articles.*','depot_articles.prix_vente as prix_ventes')
                    ->get();  
        }
       
        $jsonData["rows"] = $depots->toArray();
        $jsonData["total"] = $depots->count();
        return response()->json($jsonData);
    }
    
    public function listeUniteByDepotArticle($depot,$article){
        $depots = DepotArticle::with('depot','unite','article')
                ->where([['depot_articles.article_id', $article], ['depot_articles.depot_id', $depot]])
                ->select('depot_articles.*')
                ->groupBy('depot_articles.unite_id')
                ->get();
        $jsonData["rows"] = $depots->toArray();
        $jsonData["total"] = $depots->count();
        return response()->json($jsonData);
    }
    
    public function getArticleInformationsInDepotUnite($article,$depot,$unite){
        $depots = DepotArticle::with('article')
                ->where([['depot_articles.article_id', $article], ['depot_articles.depot_id', $depot],['depot_articles.unite_id', $unite]])
                ->select('depot_articles.*')
                ->get();
        $jsonData["rows"] = $depots->toArray();
        $jsonData["total"] = $depots->count();
        return response()->json($jsonData);
    }
    
    public function getAllArticleInOneDepot($depot){
        $totalArticle=0; $totalVente = 0;
        $articles = DepotArticle::where('depot_articles.depot_id',$depot)
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
        foreach($articles as $article){
            $totalArticle = $totalArticle + $article->quantite_disponible;
            $totalVente = $totalVente + ($article->prix_vente*$article->quantite_disponible);
        }
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        $jsonData["totalVente"] = $totalVente;
        $jsonData["totalArticle"] = $totalArticle;
        return response()->json($jsonData);
    }
    
    public function getAllArticleInOneDepotByCategorie($depot, $categorie){
        $totalArticle=0; $totalVente = 0;
        $articles = DepotArticle::where([['depot_articles.depot_id',$depot],['articles.categorie_id',$categorie]])
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('categories','categories.id','=','articles.categorie_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
        foreach($articles as $article){
            $totalArticle = $totalArticle + $article->quantite_disponible;
            $totalVente = $totalVente + ($article->prix_vente*$article->quantite_disponible);
        }
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        $jsonData["totalVente"] = $totalVente;
        $jsonData["totalArticle"] = $totalArticle;
        return response()->json($jsonData);
    }
    
    public function getAllArticleInOneDepotByQuantite($depot, $quantite) {
        $totalArticle = 0;
        $totalVente = 0;
        
        if($quantite == 1){
            $articles = DepotArticle::where([['depot_articles.depot_id', $depot],['depot_articles.quantite_disponible','>',0]])
                ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
                ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.categorie_id')
                ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
                ->get();
        }
        
        if($quantite == 2){
            $articles = DepotArticle::where([['depot_articles.depot_id', $depot],['depot_articles.quantite_disponible','=',0]])
                ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
                ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.categorie_id')
                ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
                ->get();
        }
        
        foreach ($articles as $article) {
            $totalArticle = $totalArticle + $article->quantite_disponible;
            $totalVente = $totalVente + ($article->prix_vente * $article->quantite_disponible);
        }
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        $jsonData["totalVente"] = $totalVente;
        $jsonData["totalArticle"] = $totalArticle;
        return response()->json($jsonData);
    }

    public function getAllArticleInOneDepotByCategorieQuantite($depot, $categorie, $quantite){
        $totalArticle = 0;
        $totalVente = 0;
        
        if($quantite == 1){
            $articles = DepotArticle::where([['depot_articles.depot_id', $depot],['articles.categorie_id',$categorie],['depot_articles.quantite_disponible','>',0]])
                ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
                ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.categorie_id')
                ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
                ->get();
        }
        
        if($quantite == 2){
            $articles = DepotArticle::where([['depot_articles.depot_id', $depot],['articles.categorie_id',$categorie],['depot_articles.quantite_disponible','=',0]])
                ->join('unites', 'unites.id', '=', 'depot_articles.unite_id')
                ->join('articles', 'articles.id', '=', 'depot_articles.article_id')
                ->join('categories', 'categories.id', '=', 'articles.categorie_id')
                ->leftjoin('param_tvas', 'param_tvas.id', '=', 'articles.param_tva_id')
                ->select('depot_articles.*', 'param_tvas.montant_tva', 'articles.param_tva_id', 'articles.prix_achat_ttc', 'articles.description_article', 'articles.code_barre', 'unites.libelle_unite')
                ->get();
        }
        
        foreach ($articles as $article) {
            $totalArticle = $totalArticle + $article->quantite_disponible;
            $totalVente = $totalVente + ($article->prix_vente * $article->quantite_disponible);
        }
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        $jsonData["totalVente"] = $totalVente;
        $jsonData["totalArticle"] = $totalArticle;
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
                $DepotArticle = DepotArticle::where([['depot_id', $data['depot_id']],['article_id', $data['article_id']],['unite_id', $data['unite_id']]])->first();
                if($DepotArticle!=null){
                    return response()->json(["code" => 0, "msg" => "Ce enregistrement existe déjà sur cet article, vérifier la liste", "data" => NULL]);      
                 }
                $depotArticle = new DepotArticle;
                $depotArticle->article_id = $data['article_id'];
                $depotArticle->depot_id = $data['depot_id'];
                $depotArticle->prix_vente = $data['prix_vente'];
                $depotArticle->unite_id = $data['unite_id'];
                $depotArticle->created_by = Auth::user()->id;
                $depotArticle->save();
                $jsonData["data"] = json_decode($depotArticle);
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
     * @param  \App\DepotArticle  $depotArticle
     * @return Response
     */
    public function update(Request $request, DepotArticle $depotArticle)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($depotArticle){
            $data = $request->all(); 
            try {
              
                $depotArticle->depot_id = $data['depot_id'];
                $depotArticle->prix_vente = $data['prix_vente'];
                $depotArticle->unite_id = $data['unite_id'];
                $depotArticle->updated_by = Auth::user()->id;
                $depotArticle->save();
                $jsonData["data"] = json_decode($depotArticle);
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
     * @param  \App\DepotArticle  $depotArticle
     * @return Response
     */
    public function destroy(DepotArticle $depotArticle)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($depotArticle){
                try {
                $depotArticle->update(['deleted_by' => Auth::user()->id]);
                $depotArticle->delete();
                $jsonData["data"] = json_decode($depotArticle);
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
    
    //Export excel
    public function exportExcelArticlByDepot($depot_id){
        return Excel::download(new ArticleByDepotExport($depot_id), 'articles_par_depot.xlsx');
    }
    
    
    //Article par dépôt PDF
    public function listeArticleByDepotPdf($depot_id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepot($depot_id));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_'.$info_depot->libelle_depot.'_.pdf');
    }
    public function articleByDepot($depot_id){
        $info_depot = Depot::find($depot_id);
       
        $datas = DepotArticle::where('depot_articles.depot_id',$depot_id)
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
         $total = 0;  $totalArticle=0; $totalVente = 0;
       foreach ($datas as $data){
           $totalArticle = $totalArticle + $data->quantite_disponible;
           $totalVente = $totalVente + ($data->prix_vente*$data->quantite_disponible);
           $data->param_tva_id !=null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente; $prix_achat_ttc = $data->prix_achat_ttc;
            
            $prix_vente_ht = ($prix_vente_ttc/($tva + 1));
            $prixVHT = round($prix_vente_ht,0);
            
            $prix_achat_ht = ($prix_achat_ttc/($tva + 1));
            $prixAHT = round($prix_achat_ht,0);
            
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->code_barre.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite_disponible.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixAHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_achat_ttc, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixVHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente*$data->quantite_disponible, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($totalArticle, 0, ',', ' ').' article(s)</b> <i>pour un montant de vente TTC global de </i><b>'.number_format($totalVente, 0, ',', ' ').'</b> F CFA';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Article par dépôt by categorie PDF
    public function listeArticleByDepotByCategoriePdf($depot_id,$categorie){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepotByCategorie($depot_id,$categorie));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_'.$info_depot->libelle_depot.'_.pdf');
    }
    public function articleByDepotByCategorie($depot_id,$categorie){
       
        $info_depot = Depot::find($depot_id);
        $info_categorie = \App\Models\Parametre\Categorie::find($categorie);
        $datas = DepotArticle::where([['depot_articles.depot_id',$depot_id],['articles.categorie_id',$categorie]])
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('categories','categories.id','=','articles.categorie_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles de catégorie '.$info_categorie->libelle_categorie.' du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
         $total = 0;  $totalArticle=0; $totalVente = 0;
       foreach ($datas as $data){
           $totalArticle = $totalArticle + $data->quantite_disponible;
           $totalVente = $totalVente + ($data->prix_vente*$data->quantite_disponible);
           $data->param_tva_id !=null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente; $prix_achat_ttc = $data->prix_achat_ttc;
            
            $prix_vente_ht = ($prix_vente_ttc/($tva + 1));
            $prixVHT = round($prix_vente_ht,0);
            
            $prix_achat_ht = ($prix_achat_ttc/($tva + 1));
            $prixAHT = round($prix_achat_ht,0);
            
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->code_barre.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite_disponible.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixAHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_achat_ttc, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixVHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente*$data->quantite_disponible, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($totalArticle, 0, ',', ' ').' article(s)</b> <i>pour un montant de vente TTC global de </i><b>'.number_format($totalVente, 0, ',', ' ').'</b> F CFA';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    
    //Article par dépôt by quantité PDF
    public function listeArticleByDepotByQuantitePdf($depot_id,$quantite){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepotByQuantite($depot_id,$quantite));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_'.$info_depot->libelle_depot.'_.pdf');
    }
    public function articleByDepotByQuantite($depot_id,$quantite){
       
        $info_depot = Depot::find($depot_id);
     
        if($quantite == 1){
            $datas = DepotArticle::where([['depot_articles.depot_id',$depot_id],['depot_articles.quantite_disponible','>',0]])
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('categories','categories.id','=','articles.categorie_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
            $outPut = $this->header();
            $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles avec quantité disponible du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        }
        
        if($quantite == 2){
            $datas = DepotArticle::where([['depot_articles.depot_id',$depot_id],['depot_articles.quantite_disponible','=',0]])
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('categories','categories.id','=','articles.categorie_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
            $outPut = $this->header();
            $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles dont la quantité est 0 du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        }
        
        
        $outPut .= '<tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
         $total = 0;  $totalArticle=0; $totalVente = 0;
       foreach ($datas as $data){
           $totalArticle = $totalArticle + $data->quantite_disponible;
           $totalVente = $totalVente + ($data->prix_vente*$data->quantite_disponible);
           $data->param_tva_id !=null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente; $prix_achat_ttc = $data->prix_achat_ttc;
            
            $prix_vente_ht = ($prix_vente_ttc/($tva + 1));
            $prixVHT = round($prix_vente_ht,0);
            
            $prix_achat_ht = ($prix_achat_ttc/($tva + 1));
            $prixAHT = round($prix_achat_ht,0);
            
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->code_barre.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite_disponible.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixAHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_achat_ttc, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixVHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente*$data->quantite_disponible, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($totalArticle, 0, ',', ' ').' article(s)</b> <i>pour un montant de vente TTC global de </i><b>'.number_format($totalVente, 0, ',', ' ').'</b> F CFA';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Article par dépôt by quantité PDF
    public function listeArticleByDepotByCategorieQuantitePdf($depot_id,$categorie,$quantite){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByDepotByCategorieQuantite($depot_id,$categorie,$quantite));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = Depot::find($depot_id);
        return $pdf->stream('liste_articles_du_depot_'.$info_depot->libelle_depot.'_.pdf');
    }
    public function articleByDepotByCategorieQuantite($depot_id,$categorie,$quantite){
       
        $info_depot = Depot::find($depot_id);
        $info_categorie = \App\Models\Parametre\Categorie::find($categorie);
        
        if($quantite == 1){
            $datas = DepotArticle::where([['depot_articles.depot_id',$depot_id],['articles.categorie_id',$categorie],['depot_articles.quantite_disponible','>',0]])
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('categories','categories.id','=','articles.categorie_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
            $outPut = $this->header();
            $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles de catégorie '.$info_categorie->libelle_categorie.' avec quantité disponible du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        }
        
        if($quantite == 2){
            $datas = DepotArticle::where([['depot_articles.depot_id',$depot_id],['articles.categorie_id',$categorie],['depot_articles.quantite_disponible','=',0]])
                    ->join('unites','unites.id','=','depot_articles.unite_id')
                    ->join('articles','articles.id','=','depot_articles.article_id')
                    ->join('categories','categories.id','=','articles.categorie_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('depot_articles.*','param_tvas.montant_tva','articles.param_tva_id','articles.prix_achat_ttc','articles.description_article','articles.code_barre','unites.libelle_unite')
                    ->get();
            $outPut = $this->header();
            $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles de catégorie '.$info_categorie->libelle_categorie.' dont la quantité est 0 du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">';
        }
        
        
        $outPut .= '<tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC vente</th>
                        </tr>
                    </div>';
         $total = 0;  $totalArticle=0; $totalVente = 0;
       foreach ($datas as $data){
           $totalArticle = $totalArticle + $data->quantite_disponible;
           $totalVente = $totalVente + ($data->prix_vente*$data->quantite_disponible);
           $data->param_tva_id !=null ? $tva = $data->montant_tva : $tva = 0;
            $prix_vente_ttc = $data->prix_vente; $prix_achat_ttc = $data->prix_achat_ttc;
            
            $prix_vente_ht = ($prix_vente_ttc/($tva + 1));
            $prixVHT = round($prix_vente_ht,0);
            
            $prix_achat_ht = ($prix_achat_ttc/($tva + 1));
            $prixAHT = round($prix_achat_ht,0);
            
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->code_barre.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite_disponible.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixAHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_achat_ttc, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($prixVHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente*$data->quantite_disponible, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($totalArticle, 0, ',', ' ').' article(s)</b> <i>pour un montant de vente TTC global de </i><b>'.number_format($totalVente, 0, ',', ' ').'</b> F CFA';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    
    //Article en voie de péremption
    public function listeArticleEnVoiePeremptionPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeArticleEnVoiePeremption());
        return $pdf->stream('liste_articles_en_voie_peremption.pdf');
    }
    public function listeArticleEnVoiePeremption(){
        $now = date("Y-m-d");
        $datas = DepotArticle::where([['depot_articles.deleted_at',null],['depot_articles.date_peremption','>',$now]])
                                    ->join('depots','depots.id','=','depot_articles.depot_id')
                                    ->join('articles','articles.id','=','depot_articles.article_id')
                                    ->join('unites','unites.id','=','depot_articles.unite_id')
                                    ->select('depot_articles.date_peremption', 'articles.description_article','depots.libelle_depot','unites.libelle_unite',DB::raw('DATE_FORMAT(depot_articles.date_peremption, "%d-%m-%Y") as date_peremptions'))
                                    ->orderBy('depot_articles.date_peremption','ASC')
                                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles en voie de péremption</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date de péremption</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Sera périmé dans</th>
                        </tr>
                    </div>';
      
        foreach ($datas as $data){
            $now = date("Y-m-d");
            $diff = strtotime($data->date_peremption) - strtotime($now); 
            $nbJour = $diff/86400;
            if($nbJour > 61){ 
                continue;
            }
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->libelle_depot.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->libelle_unite.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->date_peremptions.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$nbJour.' jour(s)</td>
                        </tr>';
       }
       
        $outPut .='</table>';
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
