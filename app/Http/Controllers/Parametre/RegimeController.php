<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Regime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class RegimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Régime";
       $btnModalAjout = "FALSE";
       return view('parametre.regime.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeRegime()
    {
        $regimes = DB::table('regimes')
                ->select('regimes.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_regime', 'ASC')
                ->get();
       $jsonData["rows"] = $regimes->toArray();
       $jsonData["total"] = $regimes->count();
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
        if ($request->isMethod('post') && $request->input('libelle_regime')) {

                $data = $request->all(); 

            try {

                $request->validate([
                    'libelle_regime' => 'required',
                ]);
                $Regime = Regime::where('libelle_regime', $data['libelle_regime'])->first();
                if($Regime!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $regime = new Regime;
                $regime->libelle_regime = $data['libelle_regime'];
                $regime->created_by = Auth::user()->id;
                $regime->save();
                $jsonData["data"] = json_decode($regime);
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
     * @param  \App\Regime  $regime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Regime $regime)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($regime){
            try {

                $request->validate([
                    'libelle_regime' => 'required',
                ]);
              
                $regime->update([
                    'libelle_regime' => $request->get('libelle_regime'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($regime);
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
     * @param  \App\Regime  $regime
     * @return \Illuminate\Http\Response
     */
    public function destroy(Regime $regime)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($regime){
                try {
               
                $regime->update(['deleted_by' => Auth::user()->id]);
                $regime->delete();
                $jsonData["data"] = json_decode($regime);
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
