<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Localite;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocaliteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Canal";
       $titleControlleur = "Localité";
       $btnModalAjout = "FALSE";
       return view('canal.localite.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }


    public function listeLocalite()
    {
        $localites = DB::table('localites')
                ->select('localites.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_localite', 'ASC')
                ->get();
       $jsonData["rows"] = $localites->toArray();
       $jsonData["total"] = $localites->count();
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
        if ($request->isMethod('post') && $request->input('libelle_localite')) {

                $data = $request->all(); 

            try {

                $Localite = Localite::where('libelle_localite', $data['libelle_localite'])->first();
                if($Localite!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $localite = new Localite;
                $localite->libelle_localite = $data['libelle_localite'];
                $localite->created_by = Auth::user()->id;
                $localite->save();
                $jsonData["data"] = json_decode($localite);
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
     * @param  \App\Localite  $localite
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $localite = Localite::find($id);
         $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($localite){
            try {
                
                $localite->update([
                    'libelle_localite' => $request->get('libelle_localite'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($localite);
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
     * @param  \App\Localite  $localite
     * @return Response
     */
    public function destroy($id)
    {
        $localite = Localite::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($localite){
                try {
               
                $localite->update(['deleted_by' => Auth::user()->id]);
                $localite->delete();
                $jsonData["data"] = json_decode($localite);
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
