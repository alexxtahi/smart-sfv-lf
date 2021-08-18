<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\TypeAbonnement;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TypeAbonnementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Canal";
       $titleControlleur = "Offre Canal";
       $btnModalAjout = "FALSE";
       return view('canal.type-abonnement.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeTypeAbonnement()
    {
        $type_abonnements = DB::table('type_abonnements')
                ->select('type_abonnements.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_type_abonnement', 'ASC')
                ->get();
       $jsonData["rows"] = $type_abonnements->toArray();
       $jsonData["total"] = $type_abonnements->count();
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
        if ($request->isMethod('post') && $request->input('libelle_type_abonnement')) {

                $data = $request->all(); 

            try {

                $TypeAbonnement = TypeAbonnement::where('libelle_type_abonnement', $data['libelle_type_abonnement'])->first();
                if($TypeAbonnement!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $typeAbonnement = new TypeAbonnement;
                $typeAbonnement->libelle_type_abonnement = $data['libelle_type_abonnement'];
                $typeAbonnement->prix_type_abonnement = $data['prix_type_abonnement'];
                $typeAbonnement->created_by = Auth::user()->id;
                $typeAbonnement->save();
                $jsonData["data"] = json_decode($typeAbonnement);
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
     * @param  \App\TypeAbonnement  $typeAbonnement
     * @return Response
     */
    public function update(Request $request, TypeAbonnement $typeAbonnement)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($typeAbonnement){
            try {
                
                $typeAbonnement->update([
                    'libelle_type_abonnement' => $request->get('libelle_type_abonnement'),
                    'prix_type_abonnement' => $request->get('prix_type_abonnement'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($typeAbonnement);
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
     * @param  \App\TypeAbonnement  $typeAbonnement
     * @return Response
     */
    public function destroy(TypeAbonnement $typeAbonnement)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($typeAbonnement){
                try {
               
                $typeAbonnement->update(['deleted_by' => Auth::user()->id]);
                $typeAbonnement->delete();
                $jsonData["data"] = json_decode($typeAbonnement);
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
