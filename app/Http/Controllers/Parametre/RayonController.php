<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Parametre\Rayon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class RayonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Rayon";
       $btnModalAjout = "FALSE";
       return view('parametre.rayon.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeRayon()
    {
        $rayons = DB::table('rayons')
                ->select('rayons.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_rayon', 'ASC')
                ->get();
       $jsonData["rows"] = $rayons->toArray();
       $jsonData["total"] = $rayons->count();
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
        if ($request->isMethod('post') && $request->input('libelle_rayon')) {

                $data = $request->all(); 

            try {

                $request->validate([
                    'libelle_rayon' => 'required',
                ]);
                $Rayon = Rayon::where('libelle_rayon', $data['libelle_rayon'])->first();
                if($Rayon!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $rayon = new Rayon;
                $rayon->libelle_rayon = $data['libelle_rayon'];
                $rayon->created_by = Auth::user()->id;
                $rayon->save();
                $jsonData["data"] = json_decode($rayon);
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
     * @param  \App\Rayon  $rayon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rayon $rayon)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($rayon){
            try {

                $request->validate([
                    'libelle_rayon' => 'required',
                ]);
                $Rayon = Rayon::where('libelle_rayon', $request->get('libelle_rayon'))->first();
                
                if($Rayon!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $rayon->update([
                    'libelle_rayon' => $request->get('libelle_rayon'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($rayon);
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
     * @param  \App\Rayon  $rayon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rayon $rayon)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($rayon){
                try {
               
                $rayon->update(['deleted_by' => Auth::user()->id]);
                $rayon->delete();
                $jsonData["data"] = json_decode($rayon);
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
