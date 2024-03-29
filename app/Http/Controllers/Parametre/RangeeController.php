<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Rangee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class RangeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Rangée";
       $btnModalAjout = "FALSE";
       return view('parametre.rangee.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeRangee()
    {
        $rangees = DB::table('rangees')
                ->select('rangees.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_rangee', 'ASC')
                ->get();
       $jsonData["rows"] = $rangees->toArray();
       $jsonData["total"] = $rangees->count();
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
        if ($request->isMethod('post') && $request->input('libelle_rangee')) {

                $data = $request->all(); 

            try {

                $request->validate([
                    'libelle_rangee' => 'required',
                ]);
                $Rangee = Rangee::where('libelle_rangee', $data['libelle_rangee'])->first();
                if($Rangee!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $rangee = new Rangee;
                $rangee->libelle_rangee = $data['libelle_rangee'];
                $rangee->created_by = Auth::user()->id;
                $rangee->save();
                $jsonData["data"] = json_decode($rangee);
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
     * @param  \App\Rangee  $rangee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rangee $rangee)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($rangee){
            try {

                $request->validate([
                    'libelle_rangee' => 'required',
                ]);
                $Rangee = Rangee::where('libelle_rangee', $request->get('libelle_rangee'))->first();
                
                if($Rangee!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $rangee->update([
                    'libelle_rangee' => $request->get('libelle_rangee'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($rangee);
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
     * @param  \App\Rangee  $rangee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rangee $rangee)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($rangee){
                try {
               
                $rangee->update(['deleted_by' => Auth::user()->id]);
                $rangee->delete();
                $jsonData["data"] = json_decode($rangee);
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
