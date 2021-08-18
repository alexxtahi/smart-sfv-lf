<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Rebi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RebiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $type_cautions = DB::table('type_cautions')->select('type_cautions.*')->Where('deleted_at', NULL)->orderBy('libelle_type_caution', 'ASC')->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Liste des rechargements effectués dans le mois en cours";
       $btnModalAjout = "FALSE";
       return view('canal.rebis.index',compact('type_cautions','btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }
    
    public function rebisVueAgence(){
       $type_cautions = DB::table('type_cautions')->select('type_cautions.*')->Where('deleted_at', NULL)->orderBy('libelle_type_caution', 'ASC')->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Liste des ventes et rechargements effectués dans le mois en cours";
       $btnModalAjout = "FALSE";
       return view('canal.rebis.agence',compact('type_cautions','btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }
    
    public function vueMouvementVente(){
        $agences = DB::table('agences')->select('agences.*')->Where('deleted_at', NULL)->orderBy('libelle_agence', 'ASC')->get();
        $menuPrincipal = "Canal";
        $titleControlleur = "Liste des mouvements de vente du jour";
        $btnModalAjout = "FALSE";
        return view('canal.rebis.mouvement-vente',compact('agences','btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeRebi()
    {   $month = date("m");
        $total_rechargement = 0; $total_distribue = 0;
        $cautions = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','!=','Client']])
                    ->leftJoin('demande_approvi_canals','demande_approvi_canals.id','rebis.demande_approvi_canal_id')
                    ->leftJoin('caution_agences','caution_agences.id','rebis.caution_agence_id')
                    ->leftJoin('agences','agences.id','caution_agences.agence_id')
                    ->leftJoin('type_cautions as CautionAgence','CautionAgence.id','caution_agences.type_caution_id')
                    ->leftJoin('type_cautions as CautionCanal','CautionCanal.id','demande_approvi_canals.type_caution_id')
                    ->select('rebis.*','CautionAgence.libelle_type_caution as libelle_caution_agence','CautionCanal.libelle_type_caution as libelle_caution_canal','agences.numero_identifiant_agence','agences.libelle_agence',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                    ->whereMonth('rebis.date_operation','=', $month)
                    ->orderBy('rebis.date_operation', 'DESC')
                    ->get();
        foreach ($cautions as $caution){
            $total_rechargement = $total_rechargement + $caution->montant_recharge;
            $total_distribue = $total_distribue + $caution->montant_recharge_agence;
        }
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_distribue"] = $total_distribue;
        return response()->json($jsonData);
    }
    
    public function listeRebiByPeriode($debut,$fin)
    {   $total_rechargement = 0; $total_distribue = 0;
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $cautions = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','!=','Client']])
                    ->leftJoin('demande_approvi_canals','demande_approvi_canals.id','rebis.demande_approvi_canal_id')
                    ->leftJoin('caution_agences','caution_agences.id','rebis.caution_agence_id')
                    ->leftJoin('agences','agences.id','caution_agences.agence_id')
                    ->leftJoin('type_cautions as CautionAgence','CautionAgence.id','caution_agences.type_caution_id')
                    ->leftJoin('type_cautions as CautionCanal','CautionCanal.id','demande_approvi_canals.type_caution_id')
                    ->select('rebis.*','CautionAgence.libelle_type_caution as libelle_caution_agence','CautionCanal.libelle_type_caution as libelle_caution_canal','agences.numero_identifiant_agence','agences.libelle_agence',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                    ->whereDate('rebis.date_operation','>=',$date1)
                    ->whereDate('rebis.date_operation','<=', $date2)
                    ->orderBy('rebis.date_operation', 'DESC')
                    ->get();
        foreach($cautions as $caution){
            $total_rechargement = $total_rechargement + $caution->montant_recharge;
            $total_distribue = $total_distribue + $caution->montant_recharge_agence;
        }
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_distribue"] = $total_distribue;
        return response()->json($jsonData);
    }

    public function listeRebiByTypeCaution($typeCaution)
    {   
        $total_rechargement = 0; $total_distribue = 0;
        $cautions = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','!=','Client']])
                    ->leftJoin('demande_approvi_canals','demande_approvi_canals.id','rebis.demande_approvi_canal_id')
                    ->leftJoin('caution_agences','caution_agences.id','rebis.caution_agence_id')
                    ->leftJoin('agences','agences.id','caution_agences.agence_id')
                    ->leftJoin('type_cautions as CautionAgence','CautionAgence.id','caution_agences.type_caution_id')
                    ->leftJoin('type_cautions as CautionCanal','CautionCanal.id','demande_approvi_canals.type_caution_id')
                    ->where('CautionCanal.id',$typeCaution)                    
                    ->orWhere('CautionAgence.id',$typeCaution)                    
                    ->select('rebis.*','CautionAgence.libelle_type_caution as libelle_caution_agence','CautionCanal.libelle_type_caution as libelle_caution_canal','agences.numero_identifiant_agence','agences.libelle_agence',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                    ->orderBy('rebis.date_operation', 'DESC')
                    ->get();
        foreach ($cautions as $caution){
            $total_rechargement = $total_rechargement + $caution->montant_recharge;
            $total_distribue = $total_distribue + $caution->montant_recharge_agence;
        }
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_distribue"] = $total_distribue;
        return response()->json($jsonData);
    }
    
    public function listeMouvementVente(){
        $total_rechargement = 0; $total_timbre = 0;
        $aujour_hui = date('Y-m-d');
        $ventes = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->whereDate('rebis.date_operation', $aujour_hui)                    
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','vente_materiels.id as idMateriel','reabonnements.id as idReabonnement','abonnements.id as idAbonnement',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        foreach ($ventes as $vente){
            $total_rechargement = $total_rechargement + $vente->montant_recharge_client;
            $vente->montant_recharge_client > 5000 ? $total_timbre = $total_timbre + 100 : $total_timbre = $total_timbre + 0;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_timbre"] = $total_timbre;
        return response()->json($jsonData);
    }
    
    public function listeMouvementVenteByAgence($agence){
        $total_rechargement = 0; $total_timbre=0;
        $ventes = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->where('agenceReabonnement.id',$agence)
                        ->orWhere('agenceAbonnement.id',$agence)
                        ->orWhere('agenceMateriel.id',$agence)
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','vente_materiels.id as idMateriel','reabonnements.id as idReabonnement','abonnements.id as idAbonnement',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        foreach ($ventes as $vente){
            $total_rechargement = $total_rechargement + $vente->montant_recharge_client;
            $vente->montant_recharge_client > 5000 ? $total_timbre = $total_timbre + 100 : $total_timbre = $total_timbre + 0;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_timbre"] = $total_timbre;
        return response()->json($jsonData);
    }

    public function listeMouvementVenteByPeriode($debut,$fin){
        $total_rechargement = 0; $total_timbre = 0;
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->whereDate('rebis.date_operation','>=',$date1)
                        ->whereDate('rebis.date_operation','<=', $date2)                  
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','vente_materiels.id as idMateriel','reabonnements.id as idReabonnement','abonnements.id as idAbonnement',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        foreach ($ventes as $vente){
            $total_rechargement = $total_rechargement + $vente->montant_recharge_client;
            $vente->montant_recharge_client > 5000 ? $total_timbre = $total_timbre + 100 : $total_timbre = $total_timbre + 0;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_timbre"] = $total_timbre;
        return response()->json($jsonData);
    }
    
    public function listeMouvementVenteByAgencePeriode($agence,$debut,$fin){
        $total_rechargement = 0; $total_timbre = 0;
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->whereDate('rebis.date_operation','>=',$date1)
                        ->whereDate('rebis.date_operation','<=', $date2)
                        ->where('agenceReabonnement.id',$agence)
                        ->orWhere('agenceAbonnement.id',$agence)
                        ->orWhere('agenceMateriel.id',$agence)
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','vente_materiels.id as idMateriel','reabonnements.id as idReabonnement','abonnements.id as idAbonnement',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        foreach ($ventes as $vente){
            $total_rechargement = $total_rechargement + $vente->montant_recharge_client;
            $vente->montant_recharge_client > 5000 ? $total_timbre = $total_timbre + 100 : $total_timbre = $total_timbre + 0;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["total_rechargement"] = $total_rechargement;
        $jsonData["total_timbre"] = $total_timbre;
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
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  \App\Rebi  $rebi
     * @return Response
     */
    public function update(Request $request, Rebi $rebi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rebi  $rebi
     * @return Response
     */
    public function destroy(Rebi $rebi)
    {
        //
    }
    
    
    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    
    
    // ***** Les Etats ***** //
    
    //Liste rebi pour le mois en cours
    public function listeRebisPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeRebiss());
        return $pdf->stream('liste_rebis.pdf');
    }
    public function listeRebiss(){
        $month = date("m");
        
         $datas =  Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','!=','Client']])
                    ->leftJoin('demande_approvi_canals','demande_approvi_canals.id','rebis.demande_approvi_canal_id')
                    ->leftJoin('caution_agences','caution_agences.id','rebis.caution_agence_id')
                    ->leftJoin('agences','agences.id','caution_agences.agence_id')
                    ->leftJoin('type_cautions as CautionAgence','CautionAgence.id','caution_agences.type_caution_id')
                    ->leftJoin('type_cautions as CautionCnal','CautionCnal.id','demande_approvi_canals.type_caution_id')
                    ->select('rebis.*','CautionAgence.libelle_type_caution as libelle_caution_agence','CautionCnal.libelle_type_caution as libelle_caution_canal','agences.numero_identifiant_agence',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                    ->whereMonth('rebis.date_operation','=', $month)
                    ->orderBy('rebis.date_operation', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des rechargements effectués pour le mois en cours </h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_distribue = 0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge; 
           $total_distribue = $total_distribue + $data->montant_recharge_agence; 
           
           if($data->concerne=='Agence'){
               $montant = $data->montant_recharge_agence;
               $concerne = "Rechargement de l'agence ".$data->numero_identifiant_agence." pour ".$data->libelle_caution_agence;
           }
           if($data->concerne=='Canal'){
               $montant = $data->montant_recharge;
               $concerne = "Rechargement chez Canal pour ".$data->libelle_caution_canal;
           }
                   
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($montant, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Rechargement :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Distribution :<b> '.number_format($total_distribue, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Disponible :<b> '.number_format($total_rechargement-$total_distribue, 0, ',', ' ').'</b> F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Liste rebi sur une période
    public function listeRebisByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeRebisByPeriodes($debut,$fin));
        return $pdf->stream('liste_rebis_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeRebisByPeriodes($debut,$fin){   
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas =  Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','!=','Client']])
                    ->leftJoin('demande_approvi_canals','demande_approvi_canals.id','rebis.demande_approvi_canal_id')
                    ->leftJoin('caution_agences','caution_agences.id','rebis.caution_agence_id')
                    ->leftJoin('agences','agences.id','caution_agences.agence_id')
                    ->leftJoin('type_cautions as CautionAgence','CautionAgence.id','caution_agences.type_caution_id')
                    ->leftJoin('type_cautions as CautionCnal','CautionCnal.id','demande_approvi_canals.type_caution_id')
                    ->select('rebis.*','CautionAgence.libelle_type_caution as libelle_caution_agence','CautionCnal.libelle_type_caution as libelle_caution_canal','agences.numero_identifiant_agence',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                    ->whereDate('rebis.date_operation','>=',$date1)
                    ->whereDate('rebis.date_operation','<=', $date2)
                    ->orderBy('rebis.date_operation', 'DESC')
                    ->get();
         
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des rechargements effectués du '.$debut.' au '.$fin.' </h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_distribue = 0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge; 
           $total_distribue = $total_distribue + $data->montant_recharge_agence; 
           
           if($data->concerne=='Agence'){
               $montant = $data->montant_recharge_agence;
               $concerne = "Rechargement de l'agence ".$data->numero_identifiant_agence." pour ".$data->libelle_caution_agence;
           }
           if($data->concerne=='Canal'){
               $montant = $data->montant_recharge;
               $concerne = "Rechargement chez Canal pour ".$data->libelle_caution_canal;
           }
                   
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($montant, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Rechargement :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Distribution :<b> '.number_format($total_distribue, 0, ',', ' ').'</b> F CFA</b><br/>';
        //$outPut.='<br/> Disponible :<b> '.number_format($total_rechargement-$total_distribue, 0, ',', ' ').'</b> F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste de rebi par type de caution
    public function listeRebiByTypeCautionPdf($typeCaution){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeRebiByTypeCautions($typeCaution));
        $info_type_caution = \App\Models\Canal\TypeCaution::find($typeCaution);
        return $pdf->stream('liste_rebis_pour_'.$info_type_caution->libelle_type_caution.'.pdf');
    }
    public function listeRebiByTypeCautions($typeCaution){
        $info_type_caution = \App\Models\Canal\TypeCaution::find($typeCaution);
        $datas =  Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','!=','Client']])
                    ->leftJoin('demande_approvi_canals','demande_approvi_canals.id','rebis.demande_approvi_canal_id')
                    ->leftJoin('caution_agences','caution_agences.id','rebis.caution_agence_id')
                    ->leftJoin('agences','agences.id','caution_agences.agence_id')
                    ->leftJoin('type_cautions as CautionAgence','CautionAgence.id','caution_agences.type_caution_id')
                    ->leftJoin('type_cautions as CautionCanal','CautionCanal.id','demande_approvi_canals.type_caution_id')
                    ->where('CautionCanal.id',$typeCaution)                    
                    ->orWhere('CautionAgence.id',$typeCaution)                    
                    ->select('rebis.*','CautionAgence.libelle_type_caution as libelle_caution_agence','CautionCanal.libelle_type_caution as libelle_caution_canal','agences.numero_identifiant_agence',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                    ->orderBy('rebis.date_operation', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des rechargements effectués pour '.$info_type_caution->libelle_type_caution.' </h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_distribue = 0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge; 
           $total_distribue = $total_distribue + $data->montant_recharge_agence; 
           
           if($data->concerne=='Agence'){
               $montant = $data->montant_recharge_agence;
               $concerne = "Rechargement de l'agence ".$data->numero_identifiant_agence." pour ".$data->libelle_caution_agence;
           }
           if($data->concerne=='Canal'){
               $montant = $data->montant_recharge;
               $concerne = "Rechargement chez Canal pour ".$data->libelle_caution_canal;
           }
                   
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($montant, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Rechargement :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Distribution :<b> '.number_format($total_distribue, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Disponible :<b> '.number_format($total_rechargement-$total_distribue, 0, ',', ' ').'</b> F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    
    //Liste mouvement des ventes
    public function listeMouvementVentePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeMouvementVentes());
        return $pdf->stream('liste_ventes.pdf');
    }
    public function listeMouvementVentes(){
        $aujour_hui= date("Y-m-d");
        $datas = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->whereDate('rebis.date_operation', $aujour_hui)                    
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','reabonnements.id as idReabonnement','abonnements.id as idAbonnement','vente_materiels.id as idMateriel',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvent de vente du jour '.date("d-m-Y").'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Timbre</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_timbre = 0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge_client;
           $data->montant_recharge_client > 5000 ? $timbre = 100 : $timbre =0;
           $total_timbre = $total_timbre + $timbre;
           if($data->idReabonnement!=null){
               $concerne = "Réabonnement effectuer par ".$data->libelle_agence_reabonnement;
           }
           if($data->idAbonnement!=null){
               $concerne = "Abonnement effectuer par ".$data->libelle_agence_abonnement;
           }
           if($data->idMateriel!=null){
               $concerne = "Vente de matériel effectuer par ".$data->libelle_agence_materiel;
           }
                   
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_recharge_client, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($timbre, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Total :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Total timbre :<b> '.number_format($total_timbre, 0, ',', ' ').'</b> F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    //Liste mouvement des ventes par agence
    public function listeMouvementVenteByAgencePdf($agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeMouvementVenteByAgences($agence));
        $infor_agence = \App\Models\Canal\Agence::find($agence);
        return $pdf->stream('liste_ventes_de_'.$infor_agence->libelle_agence.'.pdf');
    }
    public function listeMouvementVenteByAgences($agence){
     
        $infor_agence = \App\Models\Canal\Agence::find($agence);
        
        $datas = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->where('agenceReabonnement.id',$agence)
                        ->orWhere('agenceAbonnement.id',$agence)    
                        ->orWhere('agenceMateriel.id',$agence)  
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','reabonnements.id as idReabonnement','abonnements.id as idAbonnement','vente_materiels.id as idMateriel',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvent de vente de l\'agence '.$infor_agence->libelle_agence.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Timbre</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_timbre=0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge_client;
           
           if($data->idReabonnement!=null){
               $concerne = "Réabonnement effectuer par ".$data->libelle_agence_reabonnement;
           }
           if($data->idAbonnement!=null){
               $concerne = "Abonnement effectuer par ".$data->libelle_agence_abonnement;
           }
           if($data->idMateriel!=null){
               $concerne = "Vente de matériel effectuer par ".$data->libelle_agence_materiel;
           }
                   
           $data->montant_recharge_client > 5000 ? $timbre = 100 : $timbre =0;
           $total_timbre = $total_timbre + $timbre;
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_recharge_client, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($timbre, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Total :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Total timbre :<b> '.number_format($total_timbre, 0, ',', ' ').'</b> F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste mouvement des ventes par période
    public function listeMouvementVenteByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeMouvementVenteByPeriodes($debut,$fin));
      
        return $pdf->stream('liste_ventes_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeMouvementVenteByPeriodes($debut,$fin){
     
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        
        $datas = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->whereDate('rebis.date_operation','>=',$date1)
                        ->whereDate('rebis.date_operation','<=', $date2)        
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','reabonnements.id as idReabonnement','abonnements.id as idAbonnement','vente_materiels.id as idMateriel',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvent de vente du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Timbre</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_timbre = 0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge_client;
           
           if($data->idReabonnement!=null){
               $concerne = "Réabonnement effectuer par ".$data->libelle_agence_reabonnement;
           }
           if($data->idAbonnement!=null){
               $concerne = "Abonnement effectuer par ".$data->libelle_agence_abonnement;
           }
           if($data->idMateriel!=null){
               $concerne = "Vente de matériel effectuer par ".$data->libelle_agence_materiel;
           }
           $data->montant_recharge_client > 5000 ? $timbre = 100 : $timbre =0;
           $total_timbre = $total_timbre + $timbre;
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_recharge_client, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($timbre, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Total :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Total timbre :<b> '.number_format($total_timbre, 0, ',', ' ').'</b> F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Liste mouvement des ventes d'une agence sur une période
    public function listeMouvementVenteByAgencePeriodePdf($agence,$debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeMouvementVenteByAgencePeriodes($agence,$debut,$fin));
        $infor_agence = \App\Models\Canal\Agence::find($agence);
        return $pdf->stream('liste_ventes_du_'.$debut.'_au_'.$fin.'_de_'.$infor_agence->libelle_agence.'.pdf');
    }
    public function listeMouvementVenteByAgencePeriodes($agence,$debut,$fin){
     
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $infor_agence = \App\Models\Canal\Agence::find($agence);
        
        $datas = Rebi::where([['rebis.deleted_at', NULL],['rebis.concerne','=','Client']])
                        ->leftJoin('abonnements','abonnements.id','rebis.abonnement_id')
                        ->leftJoin('reabonnements','reabonnements.id','rebis.reabonnement_id')
                        ->leftJoin('vente_materiels','vente_materiels.id','rebis.vente_materiel_id')
                        ->leftJoin('agences as agenceReabonnement','agenceReabonnement.id','reabonnements.agence_id')
                        ->leftJoin('agences as agenceAbonnement','agenceAbonnement.id','abonnements.agence_id')
                        ->leftJoin('agences as agenceMateriel','agenceMateriel.id','vente_materiels.agence_id')
                        ->whereDate('rebis.date_operation','>=',$date1)
                        ->whereDate('rebis.date_operation','<=', $date2)  
                        ->where('agenceReabonnement.id',$agence)
                        ->orWhere('agenceAbonnement.id',$agence)   
                        ->orWhere('agenceMateriel.id',$agence)   
                        ->select('rebis.*','agenceMateriel.libelle_agence as libelle_agence_materiel','agenceReabonnement.libelle_agence as libelle_agence_reabonnement','agenceAbonnement.libelle_agence as libelle_agence_abonnement','reabonnements.id as idReabonnement','abonnements.id as idAbonnement','vente_materiels.id as idMateriel',DB::raw('DATE_FORMAT(rebis.date_operation, "%d-%m-%Y") as date_operations'))
                        ->orderBy('rebis.date_operation', 'DESC')
                        ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des mouvent de vente de l\'agence '.$infor_agence->libelle_agence.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="65%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Timbre</th>
                        </tr>
                    </div>';
       $total_rechargement = 0; $total_timbre =0;
       foreach ($datas as $data){
           $total_rechargement = $total_rechargement + $data->montant_recharge_client;
           
           if($data->idReabonnement!=null){
               $concerne = "Réabonnement effectuer par ".$data->libelle_agence_reabonnement;
           }
           if($data->idAbonnement!=null){
               $concerne = "Abonnement effectuer par ".$data->libelle_agence_abonnement;
           }
           if($data->idMateriel!=null){
               $concerne = "Vente de matériel effectuer par ".$data->libelle_agence_materiel;
           }
            $data->montant_recharge_client > 5000 ? $timbre = 100 : $timbre =0;
            $total_timbre = $total_timbre + $timbre;      
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_operations.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_recharge_client, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($timbre, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Total :<b> '.number_format($total_rechargement, 0, ',', ' ').'</b> F CFA</b><br/>';
        $outPut.='<br/> Total timbre :<b> '.number_format($total_timbre, 0, ',', ' ').'</b> F CFA</b>';
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
