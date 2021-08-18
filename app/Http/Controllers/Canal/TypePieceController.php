<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\TypePiece;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TypePieceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Canal";
       $titleControlleur = "Type de pièce";
       $btnModalAjout = "FALSE";
       return view('canal.type-piece.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeTypePiece()
    {
        $type_pieces = DB::table('type_pieces')
                ->select('type_pieces.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_type_piece', 'ASC')
                ->get();
       $jsonData["rows"] = $type_pieces->toArray();
       $jsonData["total"] = $type_pieces->count();
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
        if ($request->isMethod('post') && $request->input('libelle_type_piece')) {

                $data = $request->all(); 

            try {

                $TypePiece = TypePiece::where('libelle_type_piece', $data['libelle_type_piece'])->first();
                if($TypePiece!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $typePiece = new TypePiece;
                $typePiece->libelle_type_piece = $data['libelle_type_piece'];
                $typePiece->created_by = Auth::user()->id;
                $typePiece->save();
                $jsonData["data"] = json_decode($typePiece);
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
     * @param  \App\TypePiece  $typePiece
     * @return Response
     */
    public function update(Request $request, TypePiece $typePiece)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($typePiece){
            try {
                
                $typePiece->update([
                    'libelle_type_piece' => $request->get('libelle_type_piece'),
                    'updated_by' => Auth::user()->id,
                ]);
                $jsonData["data"] = json_decode($typePiece);
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
     * @param  \App\TypePiece  $typePiece
     * @return Response
     */
    public function destroy($id)
    {
        $typePiece = TypePiece::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($typePiece){
                try {
               
                $typePiece->update(['deleted_by' => Auth::user()->id]);
                $typePiece->delete();
                $jsonData["data"] = json_decode($typePiece);
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
