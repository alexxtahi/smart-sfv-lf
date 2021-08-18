<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Depot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Dépôt d'articles";
       $btnModalAjout = "FALSE";
       return view('parametre.depot.index',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeDepot()
    {
       $depots = DB::table('depots')->select('depots.*')->Where('deleted_at', NULL)->orderBy('depots.libelle_depot', 'ASC')->get();
      
       $jsonData["rows"] = $depots->toArray();
       $jsonData["total"] = $depots->count();
       
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
        if ($request->isMethod('post') && $request->input('libelle_depot')) {

                $data = $request->all(); 

            try {

                $request->validate([
                    'libelle_depot' => 'required',
                    'adresse_depot' => 'required'
                ]);
                $Depot = Depot::where('libelle_depot', $data['libelle_depot'])->first();
                if($Depot!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }
                $depot = new Depot;
                $depot->libelle_depot = $data['libelle_depot'];
                $depot->adresse_depot = $data['adresse_depot'];
                $depot->contact_depot = $data['contact_depot'];
                $depot->created_by = Auth::user()->id;
                $depot->save();
                
                $jsonData["data"] = json_decode($depot);
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
     * @param  \App\Depot  $depot
     * @return Response
     */
    public function update(Request $request, Depot $depot)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
   
        if($depot){
            try {

                $request->validate([
                    'libelle_depot' => 'required',
                    'adresse_depot' => 'required'
                ]);

                $depot->update([
                    'libelle_depot' => $request->get('libelle_depot'),
                    'adresse_depot' => $request->get('adresse_depot'),
                    'contact_depot' => $request->get('contact_depot'),
                    'updated_by' => Auth::user()->id,
                ]);
                
                $jsonData["data"] = json_decode($depot);
                
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
     * @param  \App\Depot  $depot
     * @return Response
     */
    public function destroy(Depot $depot)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($depot){
                try {
               
                $depot->update(['deleted_by' => Auth::user()->id]);
                $depot->delete();
                $jsonData["data"] = json_decode($depot);
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
