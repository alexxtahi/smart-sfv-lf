<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\SousCategorie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function view;

class SousCategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Sous catégorie";
       $btnModalAjout = "FALSE";
       return view('parametre.sous-categorie.index',compact('categories', 'menuPrincipal', 'titleControlleur', 'btnModalAjout')); 
    }

    public function listeSousCategorie() {
        $sous_categories = SousCategorie::with('categorie')
                ->select('sous_categories.*')
                ->Where('sous_categories.deleted_at', NULL)
                ->orderBy('sous_categories.libelle_sous_categorie', 'ASC')
                ->get();

        $jsonData["rows"] = $sous_categories->toArray();
        $jsonData["total"] = $sous_categories->count();
        return response()->json($jsonData);
    }

    public function listeSousFamilleByCategorie($categorie) {
        $sous_categories = SousCategorie::with('categorie')
                ->select('sous_categories.*')
                ->Where([['sous_categories.deleted_at', NULL], ['sous_categories.categorie_id', $categorie]])
                ->orderBy('sous_categories.libelle_sous_categorie', 'ASC')
                ->get();

        $jsonData["rows"] = $sous_categories->toArray();
        $jsonData["total"] = $sous_categories->count();
        return response()->json($jsonData);
    }
    
    public function sousCategorieArticlesByDepot($categorie,$depot){
        $sous_categories = DB::table('sous_categories')
                ->join('articles','articles.sous_categorie_id','=','sous_categories.id')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('sous_categories.libelle_sous_categorie','sous_categories.id as id_sous_categorie')
                ->Where([['sous_categories.deleted_at', NULL],['depot_articles.depot_id',$depot],['sous_categories.categorie_id',$categorie]])
                ->groupBy('sous_categories.id')
                ->get();
       $jsonData["rows"] = $sous_categories->toArray();
       $jsonData["total"] = $sous_categories->count();
       return response()->json($jsonData);
    }
    public function sousCategorieIdArticlesByDepot($id,$depot){
        $sous_categories = DB::table('sous_categories')
                ->join('articles','articles.sous_categorie_id','=','sous_categories.id')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('sous_categories.libelle_sous_categorie','sous_categories.id as id_sous_categorie')
                ->Where([['sous_categories.deleted_at', NULL],['depot_articles.depot_id',$depot],['sous_categories.id',$id]])
                ->groupBy('sous_categories.id')
                ->get();
       $jsonData["rows"] = $sous_categories->toArray();
       $jsonData["total"] = $sous_categories->count();
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
        if ($request->isMethod('post') && $request->input('libelle_sous_categorie')) {

                $data = $request->all(); 

            try {

                $SousCategorie = SousCategorie::where([['libelle_sous_categorie', $data['libelle_sous_categorie']],['categorie_id',$data['categorie_id']]])->first();
                if($SousCategorie!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }
                $sousCategorie = new SousCategorie;
                $sousCategorie->libelle_sous_categorie = $data['libelle_sous_categorie'];
                $sousCategorie->categorie_id = $data['categorie_id'];
                $sousCategorie->created_by = Auth::user()->id;
                $sousCategorie->save();
                
                $jsonData["data"] = json_decode($sousCategorie);
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
     * @param  \App\SousCategorie  $sousCategorie
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        $sousCategorie = SousCategorie::find($id);

        if($sousCategorie){
            try {

                $sousCategorie->update([
                    'libelle_sous_categorie' => $request->get('libelle_sous_categorie'),
                    'categorie_id' => $request->get('categorie_id'),
                    'updated_by' => Auth::user()->id,
                ]);
                
                $jsonData["data"] = json_decode($sousCategorie);
                
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
     * @param  \App\SousCategorie  $sousCategorie
     * @return Response
     */
    public function destroy($id)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $sousCategorie = SousCategorie::find($id);
        if($sousCategorie){
            try {
                
                $sousCategorie->update(['deleted_by' => Auth::user()->id]);
                $sousCategorie->delete();
                
                $jsonData["data"] = json_decode($sousCategorie);
                
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
