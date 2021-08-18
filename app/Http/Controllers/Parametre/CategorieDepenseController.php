<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\CategorieDepense;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategorieDepenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Catégorie des dépenses";
       $btnModalAjout = "FALSE";
       return view('parametre.categorie-depense.index',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout')); 
    }
    
     public function listeCategorieDepense()
    {
       $categories = DB::table('categorie_depenses')->select('categorie_depenses.*')->Where('deleted_at', NULL)->orderBy('categorie_depenses.libelle_categorie_depense', 'ASC')->get();
      
       $jsonData["rows"] = $categories->toArray();
       $jsonData["total"] = $categories->count();
       
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
        if ($request->isMethod('post') && $request->input('libelle_categorie_depense')) {

                $data = $request->all(); 

            try {
              
                $categorie = new CategorieDepense;
                $categorie->libelle_categorie_depense = $data['libelle_categorie_depense'];
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
     * @param  \App\CategorieDepense  $categorieDepense
     * @return Response
     */
    public function update(Request $request,$id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        $categorie = CategorieDepense::find($id);

        if($categorie){
            try {

                $categorie->update([
                    'libelle_categorie_depense' => $request->get('libelle_categorie_depense'),
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
     * @param  \App\CategorieDepense  $categorieDepense
     * @return Response
     */
    public function destroy($id)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $categorie = CategorieDepense::find($id);
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
