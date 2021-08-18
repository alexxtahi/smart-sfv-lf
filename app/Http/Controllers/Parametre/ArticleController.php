<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Boutique\DepotArticle;
use App\Models\Parametre\Article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $depots = DB::table('depots')->Where('deleted_at', NULL)->get();
       $param_tvas = DB::table('param_tvas')->Where('deleted_at', NULL)->get();
       $rangees = DB::table('rangees')->Where('deleted_at', NULL)->orderBy('libelle_rangee', 'asc')->get();
       $rayons = DB::table('rayons')->Where('deleted_at', NULL)->orderBy('libelle_rayon', 'asc')->get();
       $unites = DB::table('unites')->Where('deleted_at', NULL)->orderBy('libelle_unite', 'asc')->get();
       $tailles = DB::table('tailles')->Where('deleted_at', NULL)->orderBy('libelle_taille', 'asc')->get();
       $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
       $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', NULL)->orderBy('full_name_fournisseur', 'asc')->get();
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Article";
       $btnModalAjout = "TRUE";
       return view('parametre.article.index',compact('categories','fournisseurs','depots','tailles', 'rayons','unites','rangees','param_tvas','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeArticle() {
        $articles = Article::with('categorie','sous_categorie','taille', 'unite', 'rayon', 'rangee', 'param_tva','airsi_achat','airsi_vente','fournisseurs')
                ->leftjoin('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('articles.*',DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
                ->Where('articles.deleted_at', NULL)
                ->orderBy('description_article', 'ASC')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function listeArticleByCategorie($categorie) {
        $articles = Article::with('categorie','sous_categorie','taille', 'unite', 'rayon', 'rangee', 'param_tva','airsi_achat','airsi_vente','fournisseurs')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('articles.*',DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
                ->Where([['articles.deleted_at', NULL], ['articles.categorie_id', $categorie]])
                ->orderBy('description_article', 'ASC')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }

    public function articleByCode($code){
        $articles = Article::with('categorie','sous_categorie','taille', 'unite', 'rayon', 'rangee', 'param_tva','airsi_achat','airsi_vente','fournisseurs')
                 ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('articles.*',DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
                ->Where([['articles.deleted_at', NULL],['articles.code_barre','like','%'.$code.'%']])
                 ->orderBy('description_article', 'ASC')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    
    public function articleByName($name){
        $articles = Article::with('categorie','sous_categorie','taille', 'unite', 'rayon', 'rangee', 'param_tva','airsi_achat','airsi_vente','fournisseurs')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('articles.*',DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
                ->Where([['articles.deleted_at', NULL],['articles.description_article','like','%'.$name.'%']])
                  ->orderBy('description_article', 'ASC')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    
    public function listeArticleDepots($depot){
        $articles = Article::with('categorie','sous_categorie','taille', 'unite','rayon','rangee','param_tva','fournisseurs','sous_categorie')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->join('depots','depots.id','=','depot_articles.depot_id')
                ->select('articles.*','depot_articles.quantite_disponible as quantiteTotale')
                ->Where([['articles.deleted_at', NULL],['depot_articles.depot_id',$depot]])
                ->orderBy('description_article', 'ASC')
                ->get();
       $jsonData["rows"] = $articles->toArray();
       $jsonData["total"] = $articles->count();
       return response()->json($jsonData);
    }
    
    public function listeArticleByDepotCategorie($depot,$categorie){
        $articles = Article::with('categorie','sous_categorie','taille', 'unite','rayon','rangee','param_tva','fournisseurs','sous_categorie')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->join('depots','depots.id','=','depot_articles.depot_id')
                ->select('articles.*','depot_articles.quantite_disponible as quantiteTotale')
                ->Where([['articles.deleted_at', NULL],['depot_articles.depot_id',$depot],['articles.categorie_id',$categorie]])
                ->orderBy('description_article', 'ASC')
                ->groupBy('articles.id')
                ->get();
       $jsonData["rows"] = $articles->toArray();
       $jsonData["total"] = $articles->count();
       return response()->json($jsonData);
    }
    
    public function listeArticleByFournisseur($fournisseur){
        $articles = Article::with('param_tva')
                ->join('article_fournisseur','article_fournisseur.article_id','=','articles.id')
                ->join('fournisseurs','fournisseurs.id','=','article_fournisseur.fournisseur_id')
                ->select('articles.*')
                ->Where([['articles.deleted_at', NULL],['article_fournisseur.fournisseur_id',$fournisseur]])
                ->orderBy('description_article', 'ASC')
                ->get();
       $jsonData["rows"] = $articles->toArray();
       $jsonData["total"] = $articles->count();
       return response()->json($jsonData);
    }
    
    public function findArticle($id){
        $article = Article::with('param_tva')
                ->select('articles.*')
                ->Where([['articles.deleted_at', NULL],['articles.id',$id]])
                ->get();
       $jsonData["rows"] = $article->toArray();
       $jsonData["total"] = $article->count();
       return response()->json($jsonData);
    }
    public function inventaireListeArticlesByDepot($depot){
        $articles = DepotArticle::with('unite','article')
                    ->where('depot_articles.depot_id',$depot)
                    ->select('depot_articles.*')
                    ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function inventaireListeArticlesCategorieInDepot($categorie,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.categorie_id',$categorie]])
                ->select('depot_articles.*')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function inventaireListeArticlesSousCategorieInDepot($sous_categorie,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.sous_categorie_id',$sous_categorie]])
                ->select('depot_articles.*')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function inventaireListeArticlesCodeBarreInDepot($code,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.code_barre','=',$code]])
                ->select('depot_articles.*')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function inventaireListeArticlesIdInDepot($id,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.id','=',$id]])
                ->select('depot_articles.*')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    
    public function listeArticleGrouperBySousCategorieInDepot($sous_categorie,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.sous_categorie_id',$sous_categorie]])
                ->select('depot_articles.*')
                ->groupeBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function listeArticlesGroupeByCodeBarreInDepot($code,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.code_barre','=',$code]])
                ->select('depot_articles.*')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
        return response()->json($jsonData);
    }
    public function listeArticlesGroupeByIdInDepot($id,$depot){
        $articles = DepotArticle::with('unite','article')
                ->join('articles','articles.id','=','depot_articles.article_id')
                ->where([['depot_articles.depot_id',$depot],['articles.id','=',$id]])
                ->select('depot_articles.*')
                ->groupBy('depot_articles.article_id')
                ->get();
        $jsonData["rows"] = $articles->toArray();
        $jsonData["total"] = $articles->count();
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
        if ($request->isMethod('post') && $request->input('description_article')) {

                $data = $request->all(); 

            try {
                $Article = Article::where('description_article', $data['description_article'])->orWhere('code_barre', $data['code_barre'])->first();
                if($Article!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base, vérifier le nom de l'article ou le code barre", "data" => NULL]);
                }
               
                $article = new Article;
                $article->description_article = $data['description_article'];
                $article->categorie_id = $data['categorie_id'];
                $article->prix_achat_ttc = $data['prix_achat_ttc'];
                $article->prix_vente_ttc_base =  $data['prix_vente_ttc_base'];
                $article->code_barre = $data['code_barre'];
                $article->sous_categorie_id = isset($data['sous_categorie_id']) && !empty($data['sous_categorie_id']) ? $data['sous_categorie_id'] : null;
                $article->reference_interne = isset($data['reference_interne']) && !empty($data['reference_interne']) ? $data['reference_interne'] : null;
                $article->unite_id = isset($data['unite_id']) && !empty($data['unite_id']) ? $data['unite_id'] : null;
                $article->taille_id = isset($data['taille_id']) && !empty($data['taille_id']) ? $data['taille_id'] : null;
                $article->rayon_id = isset($data['rayon_id']) && !empty($data['rayon_id']) ? $data['rayon_id'] : null;
                $article->rangee_id = isset($data['rangee_id']) && !empty($data['rangee_id']) ? $data['rangee_id'] : null;
                $article->param_tva_id = $data['param_tva_id'];
                $article->taux_airsi_achat = isset($data['taux_airsi_achat']) && $data['taux_airsi_achat']!=0 ? $data['taux_airsi_achat'] : null;
                $article->taux_airsi_vente = isset($data['taux_airsi_vente']) && $data['taux_airsi_vente']!=0 ? $data['taux_airsi_vente'] : null;
                $article->poids_net = isset($data['poids_net']) && !empty($data['poids_net']) ? $data['poids_net'] : null;
                $article->poids_brut = isset($data['poids_brut']) && !empty($data['poids_brut']) ? $data['poids_brut'] : null;
                $article->stock_mini = isset($data['stock_mini']) && !empty($data['stock_mini']) ? $data['stock_mini'] : null;
                $article->stock_max = isset($data['stock_max']) && !empty($data['stock_max']) ? $data['stock_max'] : null;
                $article->volume = isset($data['volume']) && !empty($data['volume']) ? $data['volume'] : null;
                $article->prix_vente_en_gros_base = isset($data['prix_vente_en_gros_base']) && !empty($data['prix_vente_en_gros_base']) ? $data['prix_vente_en_gros_base'] : null;
                $article->prix_vente_demi_gros_base = isset($data['prix_vente_demi_gros_base']) && !empty($data['prix_vente_demi_gros_base']) ? $data['prix_vente_demi_gros_base'] : null;
                $article->prix_pond_ttc = isset($data['prix_pond_ttc']) && !empty($data['prix_pond_ttc']) ? $data['prix_pond_ttc'] : 0;
                $article->stockable = isset($data['stockable']) && !empty($data['stockable']) ? FALSE : TRUE;
                //Ajout de l'image de l'article s'il y a en
                if(isset($data['image_article'])){
                    $image_article = request()->file('image_article');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$image_article->getClientOriginalName()));
                    //Vérification du format de fichier
                    $extensions = array('.png','.jpg', '.jpeg');
                    $extension = strrchr($file_name, '.');
                    //Début des vérifications de sécurité...
                    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                    {
                        return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                    }
                    $path = public_path().'/img/article/';
                    $image_article->move($path,$file_name);
                    $article->image_article = 'img/article/'.$file_name;
                }
                $article->created_by = Auth::user()->id;
                $article->save();
                if(!empty($data['fournisseurs'])){
                    $article->fournisseurs()->sync($data['fournisseurs']); 
                }
                
                //Création de l'article dans les dépôts avec leur prix par rapport au dépôt
                if($article && !empty($data['depots'])){
                    $depots = $data["depots"];
                    $unites = $data["unites"];
                    $prix_ventes = $data["prix_ventes"];
                    
                    foreach ($depots as $index => $depot) {
                        $DepotArticle = DepotArticle::where([['depot_id', $depot],['article_id', $article->id],['unite_id', $unites[$index]]])->first();
                        if($DepotArticle!=null){
                            
                        }else{
                            $depotArticle = new DepotArticle;
                            $depotArticle->article_id = $article->id;
                            $depotArticle->depot_id = $depot;
                            $depotArticle->prix_vente = $prix_ventes[$index];
                            $depotArticle->unite_id = $unites[$index];
                            $depotArticle->created_by = Auth::user()->id;
                            $depotArticle->save();
                        }
                    }
                }
                $jsonData["data"] = json_decode($article);
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
     * @param  \App\Article  $article
     * @return Response
     */
    public function updateArticle(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        $article = Article::find($data['idArticle']);
        if($article){
            
            try {
                
                $article->description_article = $data['description_article'];
                $article->categorie_id = $data['categorie_id'];
                $article->prix_achat_ttc = $data['prix_achat_ttc'];
                $article->prix_vente_ttc_base = $data['prix_vente_ttc_base'];
                $article->code_barre = $data['code_barre'];
                $article->sous_categorie_id = isset($data['sous_categorie_id']) && !empty($data['sous_categorie_id']) ? $data['sous_categorie_id'] : null;
                $article->reference_interne = isset($data['reference_interne']) && !empty($data['reference_interne']) ? $data['reference_interne'] : null;
                $article->unite_id = isset($data['unite_id']) && !empty($data['unite_id']) ? $data['unite_id'] : null;
                $article->taille_id = isset($data['taille_id']) && !empty($data['taille_id']) ? $data['taille_id'] : null;
                $article->rayon_id = isset($data['rayon_id']) && !empty($data['rayon_id']) ? $data['rayon_id'] : null;
                $article->rangee_id = isset($data['rangee_id']) && !empty($data['rangee_id']) ? $data['rangee_id'] : null;
                $article->param_tva_id = $data['param_tva_id'];
                $article->taux_airsi_achat = isset($data['taux_airsi_achat']) && $data['taux_airsi_achat'] != 0 ? $data['taux_airsi_achat'] : null;
                $article->taux_airsi_vente = isset($data['taux_airsi_vente']) && $data['taux_airsi_vente'] != 0 ? $data['taux_airsi_vente'] : null;
                $article->poids_net = isset($data['poids_net']) && !empty($data['poids_net']) ? $data['poids_net'] : null;
                $article->poids_brut = isset($data['poids_brut']) && !empty($data['poids_brut']) ? $data['poids_brut'] : null;
                $article->stock_mini = isset($data['stock_mini']) && !empty($data['stock_mini']) ? $data['stock_mini'] : null;
                $article->stock_max = isset($data['stock_max']) && !empty($data['stock_max']) ? $data['stock_max'] : null;
                $article->volume = isset($data['volume']) && !empty($data['volume']) ? $data['volume'] : null;
                $article->prix_vente_en_gros_base = isset($data['prix_vente_en_gros_base']) && !empty($data['prix_vente_en_gros_base']) ? $data['prix_vente_en_gros_base'] : null;
                $article->prix_vente_demi_gros_base = isset($data['prix_vente_demi_gros_base']) && !empty($data['prix_vente_demi_gros_base']) ? $data['prix_vente_demi_gros_base'] : null;
                $article->prix_pond_ttc = isset($data['prix_pond_ttc']) && !empty($data['prix_pond_ttc']) ? $data['prix_pond_ttc'] : 0;
                $article->stockable = isset($data['stockable']) && !empty($data['stockable']) ? FALSE : TRUE;
                //Ajout de l'image de l'article s'il y a en
                if(isset($data['image_article'])){
                    $image_article = request()->file('image_article');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$image_article->getClientOriginalName()));
                    //Vérification du format de fichier
                    $extensions = array('.png','.jpg', '.jpeg');
                    $extension = strrchr($file_name, '.');
                    //Début des vérifications de sécurité...
                    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                    {
                        return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                    }
                    $path = public_path().'/img/article/';
                    $image_article->move($path,$file_name);
                    $article->image_article = 'img/article/'.$file_name;
                }
                $article->updated_by = Auth::user()->id;
                $article->save();
                $article->fournisseurs()->detach();
                 if(!empty($data['fournisseurs'])){
                    $article->fournisseurs()->sync($data['fournisseurs']);
                 }
                $jsonData["data"] = json_decode($article);
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
     * @param  \App\Article  $article
     * @return Response
     */
    public function destroy(Article $article)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($article){
                try {
                $DepotArticles = DepotArticle::where('article_id', $article->id)->get();

                foreach ($DepotArticles as $articleDepot) {
                    $articleDepot->delete();
                }
                $article->fournisseurs()->detach();
                $article->update(['deleted_by' => Auth::user()->id]);
                $article->delete();
                $jsonData["data"] = json_decode($article);
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
