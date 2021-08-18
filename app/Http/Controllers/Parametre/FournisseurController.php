<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Boutique\BonCommande;
use App\Models\Parametre\Fournisseur;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $banques = DB::table('banques')->Where('deleted_at', NULL)->orderBy('libelle_banque', 'asc')->get();
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Fournisseur";
       $btnModalAjout = "TRUE";
       return view('parametre.fournisseur.index',compact('nations','banques','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeFournisseur()
    {
        $fournisseurs = Fournisseur::with('nation','banque')
                ->select('fournisseurs.*')
                ->Where('deleted_at', NULL)
                ->orderBy('full_name_fournisseur', 'ASC')
                ->get();
       $jsonData["rows"] = $fournisseurs->toArray();
       $jsonData["total"] = $fournisseurs->count();
       return response()->json($jsonData);
    }
    
    public function listeFournisseurByNation($nation){
         $fournisseurs = Fournisseur::with('nation','banque')
                ->select('fournisseurs.*')
                ->Where([['deleted_at', NULL],['fournisseurs.nation_id',$nation]])
                ->orderBy('full_name_fournisseur', 'ASC')
                ->get();
       $jsonData["rows"] = $fournisseurs->toArray();
       $jsonData["total"] = $fournisseurs->count();
       return response()->json($jsonData);
    }
    public function findFournisseurByCommande($commande){
         $fournisseur = BonCommande::with('fournisseur')
                ->select('bon_commandes.*')
                ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.id',$commande]])
                ->get();
        $jsonData["rows"] = $fournisseur->toArray();
        $jsonData["total"] = $fournisseur->count();
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
        if ($request->isMethod('post') && $request->input('full_name_fournisseur')) {

                $data = $request->all(); 

            try {
                $Fournisseur = Fournisseur::where('full_name_fournisseur', $data['full_name_fournisseur'])->first();
                if($Fournisseur!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base", "data" => NULL]);
                }
                $fournisseur = new Fournisseur;
                //Création code du fournisseur
                $maxIdTable = DB::table('fournisseurs')->max('id');
                $idFournisseur = $maxIdTable + 1;
                $caractere_speciaux = array("'","-"," ");
                $code_fournisseur = '401'.substr(strtoupper(str_replace($caractere_speciaux,'', $data['full_name_fournisseur'])), 0, 3).$idFournisseur;
            
                $fournisseur->code_fournisseur = $code_fournisseur;
                $fournisseur->full_name_fournisseur = $data['full_name_fournisseur'];
                $fournisseur->contact_fournisseur = $data['contact_fournisseur'];
                $fournisseur->nation_id = $data['nation_id'];
                $fournisseur->email_fournisseur = isset($data['email_fournisseur']) && !empty($data['email_fournisseur']) ? $data['email_fournisseur'] : null;
                $fournisseur->banque_id = isset($data['banque_id']) && !empty($data['banque_id']) ? $data['banque_id'] : null;
                $fournisseur->compte_banque_fournisseur = isset($data['compte_banque_fournisseur']) && !empty($data['compte_banque_fournisseur']) ? $data['compte_banque_fournisseur'] : null;
                $fournisseur->compte_contribuable_fournisseur = isset($data['compte_contribuable_fournisseur']) && !empty($data['compte_contribuable_fournisseur']) ? $data['compte_contribuable_fournisseur'] : null;
                $fournisseur->boite_postale_fournisseur = isset($data['boite_postale_fournisseur']) && !empty($data['boite_postale_fournisseur']) ? $data['boite_postale_fournisseur'] : null;
                $fournisseur->adresse_fournisseur = isset($data['adresse_fournisseur']) && !empty($data['adresse_fournisseur']) ? $data['adresse_fournisseur'] : null;
                $fournisseur->fax_fournisseur = isset($data['fax_fournisseur']) && !empty($data['fax_fournisseur']) ? $data['fax_fournisseur'] : null;
                $fournisseur->created_by = Auth::user()->id;
                $fournisseur->save();
                $jsonData["data"] = json_decode($fournisseur);
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
     * @param  \App\Fournisseur  $fournisseur
     * @return Response
     */
    public function update(Request $request, Fournisseur $fournisseur)
    {
         $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($fournisseur){
            $data = $request->all(); 
            try {
                
                $fournisseur->full_name_fournisseur = $data['full_name_fournisseur'];
                $fournisseur->contact_fournisseur = $data['contact_fournisseur'];
                $fournisseur->nation_id = $data['nation_id'];
                $fournisseur->banque_id = isset($data['banque_id']) && !empty($data['banque_id']) ? $data['banque_id'] : null;
                $fournisseur->compte_banque_fournisseur = isset($data['compte_banque_fournisseur']) && !empty($data['compte_banque_fournisseur']) ? $data['compte_banque_fournisseur'] : null;
                $fournisseur->compte_contribuable_fournisseur = isset($data['compte_contribuable_fournisseur']) && !empty($data['compte_contribuable_fournisseur']) ? $data['compte_contribuable_fournisseur'] : null;
                $fournisseur->email_fournisseur = isset($data['email_fournisseur']) && !empty($data['email_fournisseur']) ? $data['email_fournisseur'] : null;
                $fournisseur->boite_postale_fournisseur = isset($data['boite_postale_fournisseur']) && !empty($data['boite_postale_fournisseur']) ? $data['boite_postale_fournisseur'] : null;
                $fournisseur->adresse_fournisseur = isset($data['adresse_fournisseur']) && !empty($data['adresse_fournisseur']) ? $data['adresse_fournisseur'] : null;
                $fournisseur->fax_fournisseur = isset($data['fax_fournisseur']) && !empty($data['fax_fournisseur']) ? $data['fax_fournisseur'] : null;
                $fournisseur->updated_by = Auth::user()->id;
                $fournisseur->save();
                
                $jsonData["data"] = json_decode($fournisseur);
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
     * @param  \App\Fournisseur  $fournisseur
     * @return Response
     */
    public function destroy(Fournisseur $fournisseur)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($fournisseur){
                try {
               
                $fournisseur->update(['deleted_by' => Auth::user()->id]);
                $fournisseur->delete();
                $jsonData["data"] = json_decode($fournisseur);
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
