<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\TypeCaution;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TypeCautionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Canal";
       $titleControlleur = "Type de caution";
       $btnModalAjout = "FALSE";
       return view('canal.type-caution.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeTypeCaution()
    {
        $type_cautions = DB::table('type_cautions')
                ->select('type_cautions.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_type_caution', 'ASC')
                ->get();
       $jsonData["rows"] = $type_cautions->toArray();
       $jsonData["total"] = $type_cautions->count();
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
        if ($request->isMethod('post') && $request->input('libelle_type_caution')) {

                $data = $request->all(); 

            try {

                $TypeCaution = TypeCaution::where('libelle_type_caution', $data['libelle_type_caution'])->first();
                if($TypeCaution!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $typeCaution = new TypeCaution;
                $typeCaution->libelle_type_caution = $data['libelle_type_caution'];
                $typeCaution->created_by = Auth::user()->id;
                $typeCaution->save();
                $jsonData["data"] = json_decode($typeCaution);
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
     * @param  \App\TypeCaution  $typeCaution
     * @return Response
     */
    public function update(Request $request, TypeCaution $typeCaution)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($typeCaution){
            try {
                
                $typeCaution->update([
                    'libelle_type_caution' => $request->get('libelle_type_caution'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($typeCaution);
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
     * @param  \App\TypeCaution  $typeCaution
     * @return Response
     */
    public function destroy(TypeCaution $typeCaution)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($typeCaution){
                try {
               
                $typeCaution->update(['deleted_by' => Auth::user()->id]);
                $typeCaution->delete();
                $jsonData["data"] = json_decode($typeCaution);
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
