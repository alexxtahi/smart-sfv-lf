<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Categorie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function response;
use function view;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Catégorie";
       $btnModalAjout = "FALSE";
       return view('parametre.categorie.index',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout')); 
    }
    
    public function listeCategorie()
    {
       $categories = DB::table('categories')->select('categories.*')->Where('deleted_at', NULL)->orderBy('categories.libelle_categorie', 'ASC')->get();
      
       $jsonData["rows"] = $categories->toArray();
       $jsonData["total"] = $categories->count();
       
        return response()->json($jsonData);
    }

    public function findCategorieByArticle($article){
        $categorie = DB::table('categories')
                ->join('articles','articles.categorie_id','=','categories.id')
                ->select('articles.id as id_article','articles.libelle_article','categories.libelle_categorie','categories.id as id_categorie')
                ->Where([['categories.deleted_at', NULL],['articles.id',$article]])
                ->get();
       $jsonData["rows"] = $categorie->toArray();
       $jsonData["total"] = $categorie->count();
       return response()->json($jsonData);
    }
    
    public function categorieArticleByDepot($depot){
        $categorie = DB::table('categories')
                ->join('articles','articles.categorie_id','=','categories.id')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('categories.libelle_categorie','categories.id as id_categorie')
                ->Where([['categories.deleted_at', NULL],['depot_articles.depot_id',$depot]])
                ->groupBy('categories.id')
                ->get();
       $jsonData["rows"] = $categorie->toArray();
       $jsonData["total"] = $categorie->count();
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
        if ($request->isMethod('post') && $request->input('libelle_categorie')) {

                $data = $request->all(); 

            try {

                $request->validate([
                    'libelle_categorie' => 'required',
                ]);
                $Categorie = Categorie::where('libelle_categorie', $data['libelle_categorie'])->first();
                if($Categorie!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }
                $categorie = new Categorie;
                $categorie->libelle_categorie = $data['libelle_categorie'];
                $categorie->created_by = Auth::user()->id;
                $categorie->save();
                
                $jsonData["data"] = json_decode($categorie);
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
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        $categorie = Categorie::find($id);

        if($categorie){
            try {

                $request->validate([
                    'libelle_categorie' => 'required'
                ]);

                $categorie->update([
                    'libelle_categorie' => $request->get('libelle_categorie'),
                    'updated_by' => Auth::user()->id,
                ]);
                
                $jsonData["data"] = json_decode($categorie);
                
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
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $categorie = Categorie::find($id);
        if($categorie){
            try {
                
                $categorie->update(['deleted_by' => Auth::user()->id]);
                $categorie->delete();
                
                $jsonData["data"] = json_decode($categorie);
                
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
