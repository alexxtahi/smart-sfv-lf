<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\OptionCanal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OptionCanalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Canal";
       $titleControlleur = "Options canal";
       $btnModalAjout = "FALSE";
       return view('canal.options-canal.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }


    public function listeOptionCanal()
    {
        $option_canals = DB::table('option_canals')
                ->select('option_canals.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_option', 'ASC')
                ->get();
       $jsonData["rows"] = $option_canals->toArray();
       $jsonData["total"] = $option_canals->count();
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
        if ($request->isMethod('post') && $request->input('libelle_option')) {

                $data = $request->all(); 

            try {

                $OptionCanal = OptionCanal::where('libelle_option', $data['libelle_option'])->first();
                if($OptionCanal!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $optionCanal = new OptionCanal;
                $optionCanal->libelle_option = $data['libelle_option'];
                $optionCanal->prix_option = $data['prix_option'];
                $optionCanal->created_by = Auth::user()->id;
                $optionCanal->save();
                $jsonData["data"] = json_decode($optionCanal);
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
     * @param  \App\OptionCanal  $optionCanal
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $optionCanal = OptionCanal::find($id);
         $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($optionCanal){
            try {
                
                $optionCanal->update([
                    'libelle_option' => $request->get('libelle_option'),
                    'prix_option' => $request->get('prix_option'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($optionCanal);
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
     * @param  \App\OptionCanal  $optionCanal
     * @return Response
     */
    public function destroy($id)
    {
        $optionCanal = OptionCanal::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($optionCanal){
                try {
               
                $optionCanal->update(['deleted_by' => Auth::user()->id]);
                $optionCanal->delete();
                $jsonData["data"] = json_decode($optionCanal);
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
