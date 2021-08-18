<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Divers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class DiversController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Divers";
       $btnModalAjout = "FALSE";
       return view('parametre.divers.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeDivers()
    {
        $divers = DB::table('divers')
                ->select('divers.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_divers', 'ASC')
                ->get();
       $jsonData["rows"] = $divers->toArray();
       $jsonData["total"] = $divers->count();
       return response()->json($jsonData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($request->isMethod('post') && $request->input('libelle_divers')) {

                $data = $request->all(); 

            try {

                $request->validate([
                    'libelle_divers' => 'required',
                ]);
                $Divers = Divers::where('libelle_divers', $data['libelle_divers'])->first();
                if($Divers!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $unite = new Divers;
                $unite->libelle_divers = $data['libelle_divers'];
                $unite->created_by = Auth::user()->id;
                $unite->save();
                $jsonData["data"] = json_decode($unite);
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Divers  $divers 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $divers = Divers::find($id);
        if($divers){
            try {

                $request->validate([
                    'libelle_divers' => 'required',
                ]);
                $Divers = Divers::where('libelle_divers', $request->get('libelle_divers'))->first();
                
                if($Divers!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $divers->update([
                    'libelle_divers' => $request->get('libelle_divers'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($divers);
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
     * @param  \App\Divers  $unite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $divers = Divers::find($id);
            if($divers){
                try {
               
                $divers->update(['deleted_by' => Auth::user()->id]);
                $divers->delete();
                $jsonData["data"] = json_decode($divers);
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
