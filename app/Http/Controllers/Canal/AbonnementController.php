<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Abonnement;
use App\Models\Canal\Agence;
use App\Models\Canal\CautionAgence;
use App\Models\Canal\Reabonnement;
use App\Models\Canal\Rebi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbonnementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $type_pieces = DB::table('type_pieces')->select('type_pieces.*')->Where('deleted_at', NULL)->orderBy('libelle_type_piece', 'ASC')->get();
       $localites = DB::table('localites')->select('localites.*')->Where('deleted_at', NULL)->orderBy('libelle_localite', 'ASC')->get();
       $nations = DB::table('nations')->select('nations.*')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'ASC')->get();
       $type_abonnements = DB::table('type_abonnements')->select('type_abonnements.*')->Where('deleted_at', NULL)->orderBy('libelle_type_abonnement', 'ASC')->get();
       $agences = DB::table('agences')->select('agences.*')->Where('deleted_at', NULL)->orderBy('libelle_agence', 'ASC')->get();
       $options = DB::table('option_canals')->select('option_canals.*')->Where('deleted_at', NULL)->get();
       $abonnes = DB::table('abonnes')->select('abonnes.id','abonnes.full_name_abonne','abonnes.contact1')->Where('abonnes.deleted_at', NULL)->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Abonnement";
       (Auth::user()->role=='Agence') ? $btnModalAjout = "TRUE" : $btnModalAjout = "FALSE";
       return view('canal.abonnement.index',compact('abonnes','type_abonnements','agences','options','localites','nations','type_pieces', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeAbonnement() {
        
        if(Auth::user()->role=='Agence'){
            $abonnements = Abonnement::with('type_abonnement','abonne','option_canals','agence')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id',Auth::user()->agence_id]])
                           ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }else{
            $abonnements = Abonnement::with('type_abonnement','abonne','option_canals','agence')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->Where('abonnements.deleted_at', NULL)
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $abonnements->toArray();
        $jsonData["total"] = $abonnements->count();
        return response()->json($jsonData);
    }
    public function listeAbonnementByNumero($numero) {
        if(Auth::user()->role=='Agence'){
             $abonnements = Abonnement::with('type_abonnement','abonne','option_canals','agence')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id],['abonnements.numero_abonnement','like','%'.$numero.'%']])
                            ->orWhere([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id],['abonnes.full_name_abonne','like','%'.$numero.'%']])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }else{
             $abonnements = Abonnement::with('type_abonnement','abonne','option_canals','agence')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.numero_abonnement','like','%'.$numero.'%']])
                            ->orWhere([['abonnements.deleted_at', NULL],['abonnes.full_name_abonne','like','%'.$numero.'%']])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $abonnements->toArray();
        $jsonData["total"] = $abonnements->count();
        return response()->json($jsonData);
    }
    public function listeAbonnementByAgence($agence) {
        if(Auth::user()->role=='Agence'){
             $abonnements = Abonnement::with('type_abonnement','abonne','option_canals','agence')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id], ['abonnements.agence_id', $agence]])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }else{
             $abonnements = Abonnement::with('type_abonnement','abonne','option_canals','agence')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id', $agence]])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $abonnements->toArray();
        $jsonData["total"] = $abonnements->count();
        return response()->json($jsonData);
    }
    public function listeAbonnementByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        if(Auth::user()->role=='Agence'){
            $abonnements = Abonnement::with('type_abonnement', 'abonne', 'option_canals')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->whereDate('abonnements.date_abonnement', '>=', $date1)
                            ->whereDate('abonnements.date_abonnement', '<=', $date2)
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }else{
            $abonnements = Abonnement::with('type_abonnement', 'abonne', 'option_canals')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->whereDate('abonnements.date_abonnement', '>=', $date1)
                            ->whereDate('abonnements.date_abonnement', '<=', $date2)
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->orderBy('abonnements.id', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $abonnements->toArray();
        $jsonData["total"] = $abonnements->count();
        return response()->json($jsonData);
    }
    public function listeAbonnementByPeriodeAgence($debut,$fin,$agence){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        if(Auth::user()->role=='Agence'){
            $abonnements = Abonnement::with('type_abonnement', 'abonne', 'option_canals')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->whereDate('abonnements.date_abonnement', '>=', $date1)
                            ->whereDate('abonnements.date_abonnement', '<=', $date2)
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }else{
            $abonnements = Abonnement::with('type_abonnement', 'abonne', 'option_canals')
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->whereDate('abonnements.date_abonnement', '>=', $date1)
                            ->whereDate('abonnements.date_abonnement', '<=', $date2)
                            ->Where([['abonnements.deleted_at', NULL],['abonnements.agence_id', $agence]])
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $abonnements->toArray();
        $jsonData["total"] = $abonnements->count();
        return response()->json($jsonData);
    }
    
    public function getInfosAbonnement($id){
        $abonnements = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.id', $id]])
                            ->join('abonnes', 'abonnes.id', 'abonnements.abonne_id')
                            ->select('abonnes.full_name_abonne','abonnes.contact1',DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->get();
        $jsonData["rows"] = $abonnements->toArray();
        $jsonData["total"] = $abonnements->count();
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
        if ($request->isMethod('post') && $request->input('abonne_id')) {
                $data = $request->all(); 
            try {
                
                //Vérification de la disponibilité du rebi
                $total_caution_abonn = 0; $total_caution_equip = 0; 
                
                $caution_abonnements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 1],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
                $caution_equipements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 2],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
                
                foreach ($caution_abonnements as $caution_abonnement){
                    $total_caution_abonn = $total_caution_abonn + $caution_abonnement->montant_depose;
                }
                foreach ($caution_equipements as $caution_equipement){
                    $total_caution_equip = $total_caution_equip + $caution_equipement->montant_depose;
                }
                
                //Regroupement des depots effectués par les clients
                $total_caution_equipement_vendu = 0; $total_caution_abonnement_vendu = 0; $total_caution_reabonnement_vendu = 0;
                $abonnements_clients = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('payement_abonnement','payement_equipement')
                                        ->get();
                $reabonnements_clients = Reabonnement::where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('montant_reabonnement')
                                        ->get();
                foreach ($abonnements_clients as $abonnements_client){
                    $total_caution_abonnement_vendu = $total_caution_abonnement_vendu + $abonnements_client->payement_abonnement;
                    $total_caution_equipement_vendu = $total_caution_equipement_vendu + $abonnements_client->payement_equipement;
                }
                foreach ($reabonnements_clients as $reabonnements_client){
                    $total_caution_reabonnement_vendu = $total_caution_reabonnement_vendu + $reabonnements_client->montant_reabonnement;
                }
                $caution_abonnement_disponible = $total_caution_abonn-($total_caution_abonnement_vendu+$total_caution_reabonnement_vendu);
                $caution_equipement_disponible = $total_caution_equip-$total_caution_equipement_vendu;
               
                if($data['payement_abonnement'] > $caution_abonnement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des abonnements est inférieur au montant de recharge demandé", "data" => NULL]);
                }
                if($data['payement_equipement'] > $caution_equipement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des équipements est inférieur au montant demandé", "data" => NULL]);
                }
                
                //Creation de l'abonnement
                $abonnement = new Abonnement;
                $abonnement->numero_abonnement = $data['numero_abonnement'];
                $abonnement->numero_decodeur = $data['numero_decodeur'];
                $abonnement->adresse_decodeur = $data['adresse_decodeur'];
                $abonnement->date_debut = Carbon::createFromFormat('d-m-Y', $data['date_debut']);
                $abonnement->date_abonnement = Carbon::createFromFormat('d-m-Y', $data['date_abonnement']);
                $abonnement->duree = $data['duree'];
                $abonnement->payement_abonnement = $data['payement_abonnement'];
                $abonnement->payement_equipement = $data['payement_equipement'];
                $abonnement->type_abonnement_id = $data['type_abonnement_id'];
                $abonnement->abonne_id = $data['abonne_id'];
                $abonnement->agence_id = Auth::user()->agence_id;
                $abonnement->created_by = Auth::user()->id;
                $abonnement->save();
                
                //Enregistrement dans rebi
                if($abonnement){
                    $rebi =  new Rebi;
                    $rebi->date_operation = now();
                    $rebi->concerne = "Client";
                    $rebi->abonnement_id = $abonnement->id;
                    $rebi->montant_recharge_client = ($abonnement->payement_equipement + $abonnement->payement_abonnement);
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();
                }
                
                if(!empty($data['options'])){
                    $abonnement->option_canals()->sync($data['options']); 
                }
                $jsonData["data"] = json_decode($abonnement);
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
     * @param  \App\Abonnement  $abonnement
     * @return Response
     */
    public function update(Request $request, Abonnement $abonnement)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        if($abonnement){
            try {
                //Vérification de la disponibilité du rebi
                $total_caution_abonn = 0; $total_caution_equip = 0; 
                
                $caution_abonnements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 1],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
                $caution_equipements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 2],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
                
                foreach ($caution_abonnements as $caution_abonnement){
                    $total_caution_abonn = $total_caution_abonn + $caution_abonnement->montant_depose;
                }
                foreach ($caution_equipements as $caution_equipement){
                    $total_caution_equip = $total_caution_equip + $caution_equipement->montant_depose;
                }
                
                //Regroupement des depots effectués par les clients
                $total_caution_equipement_vendu = 0; $total_caution_abonnement_vendu = 0; $total_caution_reabonnement_vendu = 0;
                $abonnements_clients = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('payement_abonnement','payement_equipement')
                                        ->get();
                $reabonnements_clients = Reabonnement::where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('montant_reabonnement')
                                        ->get();
                foreach ($abonnements_clients as $abonnements_client){
                    $total_caution_abonnement_vendu = $total_caution_abonnement_vendu + $abonnements_client->payement_abonnement;
                    $total_caution_equipement_vendu = $total_caution_equipement_vendu + $abonnements_client->payement_equipement;
                }
                foreach ($reabonnements_clients as $reabonnements_client){
                    $total_caution_reabonnement_vendu = $total_caution_reabonnement_vendu + $reabonnements_client->montant_reabonnement;
                }
                $caution_abonnement_disponible = ($total_caution_abonn+$abonnement->payement_abonnement)-($total_caution_abonnement_vendu+$total_caution_reabonnement_vendu);
                $caution_equipement_disponible = ($total_caution_equip+$abonnement->payement_equipement)-($total_caution_equipement_vendu);
               
                if($data['payement_abonnement'] > $caution_abonnement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des abonnements est inférieur au montant de recharge demandé", "data" => NULL]);
                }
                if($data['payement_equipement'] > $caution_equipement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des équipements est inférieur au montant demandé", "data" => NULL]);
                }
                
                $abonnement->numero_abonnement = $data['numero_abonnement'];
                $abonnement->numero_decodeur = $data['numero_decodeur'];
                $abonnement->adresse_decodeur = $data['adresse_decodeur'];
                $abonnement->date_debut = Carbon::createFromFormat('d-m-Y', $data['date_debut']);
                $abonnement->date_abonnement = Carbon::createFromFormat('d-m-Y', $data['date_abonnement']);
                $abonnement->duree = $data['duree'];
                $abonnement->payement_abonnement = $data['payement_abonnement'];
                $abonnement->payement_equipement = $data['payement_equipement'];
                $abonnement->type_abonnement_id = $data['type_abonnement_id'];
                $abonnement->abonne_id = $data['abonne_id'];
                $abonnement->updated_by = Auth::user()->id;
                $abonnement->save();
                
                if($abonnement){
                    $rebi = Rebi::where('abonnement_id',$abonnement->id)->first();
                    if($rebi!=null){
                        $rebi->montant_recharge_client = ($abonnement->payement_equipement + $abonnement->payement_abonnement);
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->save();
                    }
                }
                $abonnement->option_canals()->detach();
                if(!empty($data['options'])){
                    $abonnement->option_canals()->sync($data['options']); 
                }
                $jsonData["data"] = json_decode($abonnement);
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
     * @param  \App\Abonnement  $abonnement
     * @return Response
     */
    public function destroy(Abonnement $abonnement)
    {
       
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($abonnement){
                try {
                    $abonnement->option_canals()->detach();
                    $rebi = Rebi::where('abonnement_id',$abonnement->id)->first();
                    if($rebi!=null){
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->delete();
                    }
                $abonnement->option_canals()->detach();
                $abonnement->update(['deleted_by' => Auth::user()->id]);
                $abonnement->delete();
                $jsonData["data"] = json_decode($abonnement);
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
    
    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    
    // ***** Les Etats ***** //
    
    //Ticket abonnement
    public function ticketAbonnementPdf($id){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->ticketAbonnement($id));
        return $pdf->stream('ticket_abonnement.pdf');
    }
    
    public function ticketAbonnement($id){
        $abonnement = Abonnement::with('option_canals')
                                    ->join('abonnes','abonnes.id','abonnements.abonne_id')
                                    ->join('type_abonnements','type_abonnements.id','abonnements.type_abonnement_id')
                                    ->join('agences','agences.id','abonnements.agence_id')
                                    ->select('abonnements.*','abonnes.full_name_abonne','abonnes.civilite',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                                    ->where('abonnements.id',$id)
                                    ->first();
                            dd($abonnement);
    }

        //Liste des abonnements
    public function listeAbonnementPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeAbonnements());
        return $pdf->stream('liste_abonnements.pdf');
    }
    public function listeAbonnements(){
         $datas = Abonnement::where('abonnements.deleted_at', NULL)
                            ->join('agences','agences.id','abonnements.agence_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->join('type_abonnements','type_abonnements.id','abonnements.type_abonnement_id')
                            ->select('abonnements.*','type_abonnements.libelle_type_abonnement','abonnes.full_name_abonne','agences.libelle_agence',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des abonnements</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Nom aboné</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date abonnement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Offre</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date début</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Durée</th>';
                           if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                                $outPut .='<th cellspacing="0" border="2" width="25%" align="center">Agence</th>';
                            }
                  $outPut .='</tr>
                        </div>';
        $total = 0;  
       foreach ($datas as $data){
           $total = $total + 1; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->full_name_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_abonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>';
                            if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                            $outPut .='<td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>';
                            }   
                      $outPut .='</tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' Abonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des abonnements par agence
    public function listeAbonnementByAgencePdf($agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeAbonnementByAgences($agence));
        $info_agence = Agence::find($agence);
        return $pdf->stream('liste_abonnement_agence_'.$info_agence->libelle_agence.'.pdf');
    }
    public function listeAbonnementByAgences($agence){
        $info_agence = Agence::find($agence);
         $datas = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id',$agence]])
                            ->join('agences','agences.id','abonnements.agence_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->join('type_abonnements','type_abonnements.id','abonnements.type_abonnement_id')
                            ->select('abonnements.*','type_abonnements.libelle_type_abonnement','abonnes.full_name_abonne','agences.libelle_agence',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des abonnements de l\'agence '.$info_agence->libelle_agence.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Nom aboné</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date abonnement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Offre</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date début</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Durée</th>
                        </tr>
                        </div>';
        $total = 0;  
       foreach ($datas as $data){
           $total = $total + 1; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->full_name_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_abonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>
                         </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' Abonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des abonnements par période
    public function listeAbonnementByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeAbonnementByPeriodes($debut,$fin));
        return $pdf->stream('liste_abonnements_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeAbonnementByPeriodes($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas = Abonnement::where('abonnements.deleted_at', NULL)
                            ->join('agences','agences.id','abonnements.agence_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->join('type_abonnements','type_abonnements.id','abonnements.type_abonnement_id')
                            ->whereDate('abonnements.date_abonnement', '>=', $date1)
                            ->whereDate('abonnements.date_abonnement', '<=', $date2)
                            ->select('abonnements.*','type_abonnements.libelle_type_abonnement','abonnes.full_name_abonne','agences.libelle_agence',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des abonnements du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Nom aboné</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date abonnement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Offre</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date début</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Durée</th>';
                           if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                                $outPut .='<th cellspacing="0" border="2" width="25%" align="center">Agence</th>';
                            }
                  $outPut .='</tr>
                        </div>';
        $total = 0;  
       foreach ($datas as $data){
           $total = $total + 1; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->full_name_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_abonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>';
                            if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                            $outPut .='<td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>';
                            }   
                      $outPut .='</tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' Abonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des abonnements par agence sur une période
    public function listeAbonnementByPeriodeAgencePdf($debut,$fin,$agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeAbonnementByPeriodeAgences($debut,$fin,$agence));
        $info_agence = Agence::find($agence);
        return $pdf->stream('liste_abonnement_agence_'.$info_agence->libelle_agence.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeAbonnementByPeriodeAgences($debut,$fin,$agence){
        $info_agence = Agence::find($agence);
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id',$agence]])
                            ->join('agences','agences.id','abonnements.agence_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->join('type_abonnements','type_abonnements.id','abonnements.type_abonnement_id')
                            ->whereDate('abonnements.date_abonnement', '>=', $date1)
                            ->whereDate('abonnements.date_abonnement', '<=', $date2)
                            ->select('abonnements.*','type_abonnements.libelle_type_abonnement','abonnes.full_name_abonne','agences.libelle_agence',DB::raw('DATE_FORMAT(abonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(abonnements.date_abonnement, "%d-%m-%Y") as date_abonnements'))
                            ->orderBy('abonnements.date_abonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des abonnements de l\'agence '.$info_agence->libelle_agence.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Nom aboné</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date abonnement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Offre</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date début</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Durée</th>
                            </tr>
                        </div>';
        $total = 0;  
       foreach ($datas as $data){
           $total = $total + 1; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->full_name_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_abonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>
                         </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' Abonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Header and footer des pdf
    public function header(){
        $header = '<html>
                    <head>
                        <style>
                            @page{
                                margin: 100px 25px;
                                }
                            header{
                                    position: absolute;
                                    top: -60px;
                                    left: 0px;
                                    right: 0px;
                                    height:20px;
                                }
                            .container-table{        
                                            margin:80px 0;
                                            width: 100%;
                                        }
                            .fixed-footer{.
                                width : 100%;
                                position: fixed; 
                                bottom: -28; 
                                left: 0px; 
                                right: 0px;
                                height: 50px; 
                                text-align:center;
                            }
                            .fixed-footer-right{
                                position: absolute; 
                                bottom: -150; 
                                height: 0; 
                                font-size:13px;
                                float : right;
                            }
                            .page-number:before {
                                            
                            }
                        </style>
                    </head>
    /
    <script type="text/php">
        if (isset($pdf)){
            $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
        <body>
        <header>
        <p style="margin:0; position:left;">
            <img src='.$this->infosConfig()->logo.' width="200" height="160"/>
        </p>
        </header>';     
        return $header;
    }
    public function footer(){
        $footer ="<div class='fixed-footer'>
                        <div class='page-number'></div>
                    </div>
                    <div class='fixed-footer-right'>
                     <i> Editer le ".date('d-m-Y')."</i>
                    </div>
            </body>
        </html>";
        return $footer;
    }
}
