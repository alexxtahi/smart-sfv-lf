<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Materiel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterielController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $menuPrincipal = "Canal";
       $titleControlleur = "Matériel";
       $btnModalAjout = "TRUE";
       return view('canal.materiel.index',compact('btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeMateriel()
    {
       $materiels = DB::table('materiels')
                ->select('materiels.*')
                ->Where('deleted_at', NULL)
                ->orderBy('libelle_materiel', 'ASC')
                ->get();
       $jsonData["rows"] = $materiels->toArray();
       $jsonData["total"] = $materiels->count();
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
        if ($request->isMethod('post') && $request->input('libelle_materiel')) {

                $data = $request->all(); 

            try {

                $Materiel = Materiel::where('libelle_materiel', $data['libelle_materiel'])->first();
                if($Materiel!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }

                $materiel = new Materiel;
                $materiel->libelle_materiel = $data['libelle_materiel'];
//                $materiel->prix_achat_materiel = $data['prix_achat_materiel'];
                $materiel->prix_vente_materiel = isset($data['prix_vente_materiel']) && !empty($data['prix_vente_materiel']) ? $data['prix_vente_materiel'] : null;
                $materiel->code_materiel = isset($data['code_materiel']) && !empty($data['code_materiel']) ? $data['code_materiel'] : null;
                
                //Ajout de l'image du matériel s'il y a en 
                if(isset($data['image_materiel'])){
                    $image_materiel = request()->file('image_materiel');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$image_materiel->getClientOriginalName()));
                    //Vérification du format de fichier
                    $extensions = array('.png','.jpg', '.jpeg');
                    $extension = strrchr($file_name, '.');
                    //Début des vérifications de sécurité...
                    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                    {
                        return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                    }
                    $path = public_path().'/img/article/';
                    $image_materiel->move($path,$file_name);
                    $materiel->image_materiel = 'img/article/'.$file_name;
                }               
                
                $materiel->created_by = Auth::user()->id;
                $materiel->save();
                $jsonData["data"] = json_decode($materiel);
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
     * @param  \App\Materiel  $materiel
     * @return Response
     */
    public function updateMateriel(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        $materiel = Materiel::find($data['idMateriel']);
        
        if($materiel){
            try {
                
                $materiel->libelle_materiel = $data['libelle_materiel'];
//                $materiel->prix_achat_materiel = $data['prix_achat_materiel'];
                $materiel->prix_vente_materiel = isset($data['prix_vente_materiel']) && !empty($data['prix_vente_materiel']) ? $data['prix_vente_materiel'] : null;
                $materiel->code_materiel = isset($data['code_materiel']) && !empty($data['code_materiel']) ? $data['code_materiel'] : null;
                
                //Ajout de l'image du matériel s'il y a en
                if(isset($data['image_materiel'])){
                    $image_materiel = request()->file('image_materiel');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$image_materiel->getClientOriginalName()));
                    //Vérification du format de fichier
                    $extensions = array('.png','.jpg', '.jpeg');
                    $extension = strrchr($file_name, '.');
                    //Début des vérifications de sécurité...
                    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                    {
                        return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                    }
                    $path = public_path().'/img/article/';
                    $image_materiel->move($path,$file_name);
                    $materiel->image_materiel = 'img/article/'.$file_name;
                }  
                $materiel->updated_by = Auth::user()->id;
                $materiel->save();
           
                $jsonData["data"] = json_decode($materiel);
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
     * @param  \App\Materiel  $materiel
     * @return Response
     */
    public function destroy($id)
    {
       $materiel = Materiel::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($materiel){
                try {
               
                $materiel->update(['deleted_by' => Auth::user()->id]);
                $materiel->delete();
                $jsonData["data"] = json_decode($materiel);
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
