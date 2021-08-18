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

class ReabonnementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $type_abonnements = DB::table('type_abonnements')->select('type_abonnements.*')->Where('deleted_at', NULL)->orderBy('libelle_type_abonnement', 'ASC')->get();
       $agences = DB::table('agences')->select('agences.*')->Where('deleted_at', NULL)->orderBy('libelle_agence', 'ASC')->get();
       $options = DB::table('option_canals')->select('option_canals.*')->Where('deleted_at', NULL)->get();
       $abonnements = DB::table('abonnements')->select('abonnements.id','abonnements.numero_abonnement')->Where('abonnements.deleted_at', NULL)->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Réabonnement";
       (Auth::user()->role=='Agence') ? $btnModalAjout = "TRUE" : $btnModalAjout = "FALSE";
       return view('canal.reabonnement.index',compact('abonnements','agences','options','type_abonnements', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeReabonnement() {
        
        if(Auth::user()->role=='Agence'){
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id',Auth::user()->agence_id]])
                           ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }else{
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->Where('reabonnements.deleted_at', NULL)
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $reabonnements->toArray();
        $jsonData["total"] = $reabonnements->count();
        return response()->json($jsonData);
    }
    public function listeReabonnementByNumero($numero) {
        if(Auth::user()->role=='Agence'){
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id],['abonnements.numero_abonnement','like','%'.$numero.'%']])
                            ->orWhere([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id],['abonnements.numero_decodeur','like','%'.$numero.'%']])
                            ->orWhere([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id],['abonnes.full_name_abonne','like','%'.$numero.'%']])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }else{
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                             ->Where([['reabonnements.deleted_at', NULL],['abonnements.numero_abonnement','like','%'.$numero.'%']])
                            ->orWhere([['reabonnements.deleted_at', NULL],['abonnements.numero_decodeur','like','%'.$numero.'%']])
                            ->orWhere([['reabonnements.deleted_at', NULL],['abonnes.full_name_abonne','like','%'.$numero.'%']])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $reabonnements->toArray();
        $jsonData["total"] = $reabonnements->count();
        return response()->json($jsonData);
    }
    public function listeReabonnementByAgence($agence) {
        if(Auth::user()->role=='Agence'){
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id], ['reabonnements.agence_id', $agence]])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }else{
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', $agence]])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $reabonnements->toArray();
        $jsonData["total"] = $reabonnements->count();
        return response()->json($jsonData);
    }
    public function listeReabonnementByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        if(Auth::user()->role=='Agence'){
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                             ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->whereDate('reabonnements.date_reabonnement', '>=', $date1)
                            ->whereDate('reabonnements.date_reabonnement', '<=', $date2)
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id]])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }else{
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                             ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->whereDate('reabonnements.date_reabonnement', '>=', $date1)
                            ->whereDate('reabonnements.date_reabonnement', '<=', $date2)
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->orderBy('reabonnements.id', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $reabonnements->toArray();
        $jsonData["total"] = $reabonnements->count();
        return response()->json($jsonData);
    }
    public function listeReabonnementByPeriodeAgence($debut,$fin,$agence){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        if(Auth::user()->role=='Agence'){
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                             ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->whereDate('reabonnements.date_reabonnement', '>=', $date1)
                            ->whereDate('reabonnements.date_reabonnement', '<=', $date2)
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id]])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }else{
            $reabonnements = Reabonnement::with('abonnement','type_abonnement','option_canals','agence')
                             ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('abonnes','abonnes.id','abonnements.abonne_id')
                            ->select('reabonnements.*','abonnes.full_name_abonne',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->whereDate('reabonnements.date_reabonnement', '>=', $date1)
                            ->whereDate('reabonnements.date_reabonnement', '<=', $date2)
                            ->Where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', $agence]])
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        }
        
        $jsonData["rows"] = $reabonnements->toArray();
        $jsonData["total"] = $reabonnements->count();
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
        if ($request->isMethod('post') && $request->input('abonnement_id')) {
                $data = $request->all(); 
            try {
                
                //Vérification de la disponibilité du rebi
                $total_caution_abonn = 0; 
                
                $caution_reabonnements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 1],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
      
                
                foreach ($caution_reabonnements as $caution_reabonnement){
                    $total_caution_abonn = $total_caution_abonn + $caution_reabonnement->montant_depose;
                }
              
                //Regroupement des depots effectués par les clients
                $total_caution_abonnement_vendu = 0; $total_caution_reabonnement_vendu = 0;
                $reabonnements_clients = Reabonnement::where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('montant_reabonnement')
                                        ->get();
                $abonnements_clients = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('payement_abonnement')
                                        ->get();
                foreach ($reabonnements_clients as $reabonnements_client){
                    $total_caution_reabonnement_vendu = $total_caution_reabonnement_vendu + $reabonnements_client->montant_reabonnement;
                }
                foreach ($abonnements_clients as $abonnements_client){
                    $total_caution_abonnement_vendu = $total_caution_abonnement_vendu + $abonnements_client->payement_abonnement;
                }
                
                $caution_abonnement_disponible = $total_caution_abonn-($total_caution_abonnement_vendu+$total_caution_reabonnement_vendu);
               
                if($data['montant_reabonnement'] > $caution_abonnement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des réabonnements est inférieur au montant de recharge demandé", "data" => NULL]);
                }
                
                //Creation de l'reabonnement
                $reabonnement = new Reabonnement;
                $reabonnement->abonnement_id = $data['abonnement_id'];
                $reabonnement->date_debut = Carbon::createFromFormat('d-m-Y', $data['date_debut']);
                $reabonnement->date_reabonnement = Carbon::createFromFormat('d-m-Y', $data['date_reabonnement']);
                $reabonnement->duree = $data['duree'];
                $reabonnement->type_abonnement_id = $data['type_abonnement_id'];
                $reabonnement->montant_reabonnement = $data['montant_reabonnement'];
                $reabonnement->agence_id = Auth::user()->agence_id;
                $reabonnement->created_by = Auth::user()->id;
                $reabonnement->save();
                
                //Enregistrement dans rebi
                if($reabonnement){
                    $rebi =  new Rebi;
                    $rebi->date_operation = now();
                    $rebi->concerne = "Client";
                    $rebi->reabonnement_id = $reabonnement->id;
                    $rebi->montant_recharge_client = $reabonnement->montant_reabonnement;
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();
                }
                
                if(!empty($data['options'])){
                    $reabonnement->option_canals()->sync($data['options']); 
                }
                $jsonData["data"] = json_decode($reabonnement);
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
     * @param  \App\Reabonnement  $reabonnement
     * @return Response
     */
    public function update(Request $request, Reabonnement $reabonnement)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        if($reabonnement){
            try {
              
                //Vérification de la disponibilité du rebi
                $total_caution_abonn = 0; 
                
                $caution_reabonnements =  CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', 1],['caution_agences.agence_id', Auth::user()->agence_id]]) 
                                                    ->select('montant_depose')
                                                    ->get();
      
                
                foreach ($caution_reabonnements as $caution_reabonnement){
                    $total_caution_abonn = $total_caution_abonn + $caution_reabonnement->montant_depose;
                }
              
                //Regroupement des depots effectués par les clients
                $total_caution_abonnement_vendu = 0; $total_caution_reabonnement_vendu = 0;
                $reabonnements_clients = Reabonnement::where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('montant_reabonnement')
                                        ->get();
                $abonnements_clients = Abonnement::where([['abonnements.deleted_at', NULL],['abonnements.agence_id', Auth::user()->agence_id]]) 
                                        ->select('payement_abonnement')
                                        ->get();
                foreach ($reabonnements_clients as $reabonnements_client){
                    $total_caution_reabonnement_vendu = $total_caution_reabonnement_vendu + $reabonnements_client->montant_reabonnement;
                }
                foreach ($abonnements_clients as $abonnements_client){
                    $total_caution_abonnement_vendu = $total_caution_abonnement_vendu + $abonnements_client->payement_abonnement;
                }
                
                $caution_abonnement_disponible = ($total_caution_abonn+$reabonnements_client->montant_reabonnement)-($total_caution_abonnement_vendu+$total_caution_reabonnement_vendu);
               
                if($data['montant_reabonnement'] > $caution_abonnement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des réabonnements est inférieur au montant de recharge demandé", "data" => NULL]);
                }
                
                $reabonnement->abonnement_id = $data['abonnement_id'];
                $reabonnement->date_debut = Carbon::createFromFormat('d-m-Y', $data['date_debut']);
                $reabonnement->date_reabonnement = Carbon::createFromFormat('d-m-Y', $data['date_reabonnement']);
                $reabonnement->duree = $data['duree'];
                $reabonnement->type_abonnement_id = $data['type_abonnement_id'];
                $reabonnement->montant_reabonnement = $data['montant_reabonnement'];
                $reabonnement->updated_by = Auth::user()->id;
                $reabonnement->save();
                
                if($reabonnement){
                    $rebi = Rebi::where('reabonnement_id',$reabonnement->id)->first();
                    if($rebi!=null){
                        $rebi->montant_recharge_client = $reabonnement->montant_reabonnement;
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->save();
                    }else{
                        $rebi =  new Rebi;
                        $rebi->date_operation = now();
                        $rebi->concerne = "Client";
                        $rebi->reabonnement_id = $reabonnement->id;
                        $rebi->montant_recharge_client = $reabonnement->montant_reabonnement;
                        $rebi->created_by = Auth::user()->id;
                        $rebi->save();
                    }
                }
                $reabonnement->option_canals()->detach();
                if(!empty($data['options'])){
                    $reabonnement->option_canals()->sync($data['options']); 
                }
                $jsonData["data"] = json_decode($reabonnement);
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
     * @param  \App\Reabonnement  $reabonnement
     * @return Response
     */
    public function destroy(Reabonnement $reabonnement)
    {
       
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($reabonnement){
                try {
                    $reabonnement->option_canals()->detach();
                    $rebi = Rebi::where('reabonnement_id',$reabonnement->id)->first();
                    if($rebi!=null){
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->delete();
                    }
                $reabonnement->option_canals()->detach();
                $reabonnement->update(['deleted_by' => Auth::user()->id]);
                $reabonnement->delete();
                $jsonData["data"] = json_decode($reabonnement);
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
    
    //Liste des réabonnements
    public function listeReabonnementPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReabonnements());
        return $pdf->stream('liste_reabonnements.pdf');
    }
    public function listeReabonnements(){
         $datas = Reabonnement::where('reabonnements.deleted_at', NULL)
                            ->join('agences','agences.id','reabonnements.agence_id')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('type_abonnements','type_abonnements.id','reabonnements.type_abonnement_id')
                            ->select('reabonnements.*','type_abonnements.libelle_type_abonnement','abonnements.numero_abonnement','agences.libelle_agence',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des réabonnements</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date réabonnement</th>
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
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_reabonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_abonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>';
                            if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                            $outPut .='<td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>';
                            }   
                      $outPut .='</tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' réabonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des réabonnements par agence
    public function listeReabonnementByAgencePdf($agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReabonnementByAgences($agence));
        $info_agence = Agence::find($agence);
        return $pdf->stream('liste_reabonnement_agence_'.$info_agence->libelle_agence.'.pdf');
    }
    public function listeReabonnementByAgences($agence){
        $info_agence = Agence::find($agence);
         $datas = Reabonnement::where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id',$agence]])
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('type_abonnements','type_abonnements.id','reabonnements.type_abonnement_id')
                            ->select('reabonnements.*','type_abonnements.libelle_type_abonnement','abonnements.numero_abonnement',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des réabonnements de l\'agence '.$info_agence->libelle_agence.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date réabonnement</th>
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
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_reabonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_reabonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>
                         </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' réabonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des réabonnements par période
    public function listeReabonnementByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReabonnementByPeriodes($debut,$fin));
        return $pdf->stream('liste_reabonnements_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeReabonnementByPeriodes($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas = Reabonnement::where('reabonnements.deleted_at', NULL)
                           ->join('agences','agences.id','reabonnements.agence_id')
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('type_abonnements','type_abonnements.id','reabonnements.type_abonnement_id')
                            ->select('reabonnements.*','type_abonnements.libelle_type_abonnement','abonnements.numero_abonnement','agences.libelle_agence',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->whereDate('reabonnements.date_reabonnement', '>=', $date1)
                            ->whereDate('reabonnements.date_reabonnement', '<=', $date2)
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des réabonnements du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° reabonnement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date réabonnement</th>
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
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_reabonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_reabonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>';
                            if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                            $outPut .='<td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>';
                            }   
                      $outPut .='</tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' réabonnment(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des réabonnements par agence sur une période
    public function listeReabonnementByPeriodeAgencePdf($debut,$fin,$agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReabonnementByPeriodeAgences($debut,$fin,$agence));
        $info_agence = Agence::find($agence);
        return $pdf->stream('liste_reabonnement_agence_'.$info_agence->libelle_agence.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeReabonnementByPeriodeAgences($debut,$fin,$agence){
        $info_agence = Agence::find($agence);
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas = Reabonnement::where([['reabonnements.deleted_at', NULL],['reabonnements.agence_id',$agence]])
                            ->join('abonnements','abonnements.id','reabonnements.abonnement_id')
                            ->join('type_abonnements','type_abonnements.id','reabonnements.type_abonnement_id')
                            ->select('reabonnements.*','type_abonnements.libelle_type_abonnement','abonnements.numero_abonnement',DB::raw('DATE_FORMAT(reabonnements.date_debut, "%d-%m-%Y") as date_debuts'),DB::raw('DATE_FORMAT(reabonnements.date_reabonnement, "%d-%m-%Y") as date_reabonnements'))
                            ->whereDate('reabonnements.date_reabonnement', '>=', $date1)
                            ->whereDate('reabonnements.date_reabonnement', '<=', $date2)
                            ->orderBy('reabonnements.date_reabonnement', 'DESC')
                            ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des réabonnements de l\'agence '.$info_agence->libelle_agence.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° abonnement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date réabonnement</th>
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
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_reabonnements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_reabonnement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_debuts.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->duree.'</td>
                         </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' réabonnment(s)</b>';
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
