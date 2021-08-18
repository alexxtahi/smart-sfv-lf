<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Abonnement;
use App\Models\Canal\CautionAgence;
use App\Models\Canal\MaterielVendue;
use App\Models\Canal\Rebi;
use App\Models\Canal\VenteMateriel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MaterielVendueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listeMaterielVendu($vente)
    {   $montantTTTC_add = 0;
        $materielVendus = MaterielVendue::with('materiel')
                            ->select('materiel_vendues.*')
                            ->Where([['materiel_vendues.vente_materiel_id', $vente],['materiel_vendues.retourne', 0]])
                            ->get();
        foreach ($materielVendus as $materielVendu){
            $montantTTTC_add = $montantTTTC_add + $materielVendu->prix*$materielVendu->quantite;
        }
        $jsonData["rows"] = $materielVendus->toArray();
        $jsonData["total"] = $materielVendus->count();
        $jsonData["montantTTTC_add"] = $montantTTTC_add;
        return response()->json($jsonData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
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
        if ($request->isMethod('post') && $request->input('materiel_id')) {

                $data = $request->all(); 

            try {
                $vente = VenteMateriel::find($data['vente_id']);
                
                //Vérification de la disponibilité du rebi 
                $total_caution_equip = 0; 
               
                $caution_equipements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 2],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
             
                foreach ($caution_equipements as $caution_equipement){
                    $total_caution_equip = $total_caution_equip + $caution_equipement->montant_depose;
                }
                
                //Regroupement des depots effectués par les clients
                $total_caution_equipement_vendu = 0; $total_caution_abonnement_vendu = 0; $total_caution_reabonnement_vendu = 0;
                $abonnements_clients = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('payement_equipement')
                                        ->get();
            
                foreach ($abonnements_clients as $abonnements_client){
                    $total_caution_equipement_vendu = $total_caution_equipement_vendu + $abonnements_client->payement_equipement;
                }
                
                $montantTTTC_add = 0;
                $materielVendus = MaterielVendue::with('materiel')
                                    ->select('materiel_vendues.*')
                                    ->Where([['materiel_vendues.vente_materiel_id', $vente->id],['materiel_vendues.retourne', 0]])
                                    ->get();
                foreach ($materielVendus as $materielVendu){
                    $montantTTTC_add = $montantTTTC_add + $materielVendu->prix*$materielVendu->quantite;
                }
              
                $caution_equipement_disponible = $total_caution_equip-$total_caution_equipement_vendu;
              
                if((($data['quantite']*$data['prix'])+$montantTTTC_add) > $caution_equipement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des équipements est inférieur au montant demandé", "data" => NULL]);
                }
                
                $materielVendue = new MaterielVendue;
                $materielVendue->materiel_id = $data['materiel_id'];
                $materielVendue->vente_materiel_id = $vente->id;
                $materielVendue->quantite = $data['quantite'];
                $materielVendue->prix = $data['prix'];
                $materielVendue->save();

                if($materielVendue){
                    $rebi = Rebi::where('vente_materiel_id', $vente->id)->first();
                    
                    if($rebi){
                        $rebi->montant_recharge_client = $rebi->montant_recharge_client + ($data['prix'] * $data['quantite']);
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->save();
                    }else{
                        $rebi = new Rebi;
                        $rebi->date_operation = now();
                        $rebi->vente_materiel_id = $vente->id;
                        $rebi->concerne = "Client";
                        $rebi->montant_recharge_client = ($data['prix'] * $data['quantite']);
                        $rebi->created_by = Auth::user()->id;
                        $rebi->save();
                    }
                }
                $jsonData["data"] = json_decode($materielVendue);
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
     * @param  \App\MaterielVendue  $materielVendue
     * @return Response
     */
    public function update(Request $request, $id)
    {
          $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
           $materielVendue = MaterielVendue::find($id);
        if ($materielVendue) {
                $data = $request->all(); 
            try {
                 //Vérification de la disponibilité du rebi 
                $total_caution_equip = 0; 
               
                $caution_equipements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 2],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
             
                foreach ($caution_equipements as $caution_equipement){
                    $total_caution_equip = $total_caution_equip + $caution_equipement->montant_depose;
                }
                
                //Regroupement des depots effectués par les clients
                $total_caution_equipement_vendu = 0; $total_caution_abonnement_vendu = 0; $total_caution_reabonnement_vendu = 0;
                $abonnements_clients = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('payement_equipement')
                                        ->get();
            
                foreach ($abonnements_clients as $abonnements_client){
                    $total_caution_equipement_vendu = $total_caution_equipement_vendu + $abonnements_client->payement_equipement;
                }
                
                $montantTTTC_add = 0;
                $materielVendus = MaterielVendue::with('materiel')
                                    ->select('materiel_vendues.*')
                                    ->Where([['materiel_vendues.vente_materiel_id', $materielVendue->vente_materiel_id],['materiel_vendues.retourne', 0]])
                                    ->get();
                foreach ($materielVendus as $materielVendu){
                    $montantTTTC_add = $montantTTTC_add + $materielVendu->prix*$materielVendu->quantite;
                }
              
                $caution_equipement_disponible = $total_caution_equip-$total_caution_equipement_vendu;
              
                if((($data['quantite']*$data['prix'])+$montantTTTC_add)-($materielVendu->prix*$materielVendu->quantite) > $caution_equipement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des équipements est inférieur au montant demandé", "data" => NULL]);
                }
           
                $venteMateriel = VenteMateriel::find($materielVendue->vente_materiel_id);
                $rebi = Rebi::where('vente_materiel_id',$venteMateriel->id)->first();
                
                if($rebi){
                    $rebi->montant_recharge_client = $rebi->montant_recharge_client - ($materielVendue->prix*$materielVendue->quantite);
                    $rebi->updated_by = Auth::user()->id;
                    $rebi->save();
                }
                
                $materielVendue->materiel_id = $data['materiel_id'];
                $materielVendue->quantite = $data['quantite'];
                $materielVendue->prix = $data['prix'];
                $materielVendue->save();
                
                if($materielVendue) {
                    $rebi->montant_recharge_client = $rebi->montant_recharge_client + ($data['quantite']*$data['prix']);
                    $rebi->updated_by = Auth::user()->id;
                    $rebi->save();
                }

                $jsonData["data"] = json_decode($materielVendue);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\MaterielVendue  $materielVendue
     * @return Response
     */
    public function destroy($id)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $materielVendue = MaterielVendue::find($id);
        if($materielVendue){
            try {
                
                $venteMateriel = VenteMateriel::find($materielVendue->vente_materiel_id);
                $rebi = Rebi::where('vente_materiel_id',$venteMateriel->id)->first();
                
                if($rebi){
                    $rebi->montant_recharge_client = $rebi->montant_recharge_client - ($materielVendue->prix*$materielVendue->quantite);
                    $rebi->updated_by = Auth::user()->id;
                    $rebi->save();
                }
            
                $materielVendue->delete();
                
                $jsonData["data"] = json_decode($materielVendue);
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
