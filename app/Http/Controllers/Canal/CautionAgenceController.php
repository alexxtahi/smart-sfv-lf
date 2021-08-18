<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Agence;
use App\Models\Canal\CautionAgence;
use App\Models\Canal\DemandeApproviCanal;
use App\Models\Canal\Rebi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CautionAgenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $agences = DB::table('agences')->select('agences.*')->Where('deleted_at', NULL)->orderBy('libelle_agence', 'ASC')->get();
       $type_cautions = DB::table('type_cautions')->select('type_cautions.*')->Where('deleted_at', NULL)->orderBy('libelle_type_caution', 'ASC')->get();
       $moyen_reglements = DB::table('moyen_reglements')->select('moyen_reglements.*')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'ASC')->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Caution Agence versée pour le mois en cours";
       $btnModalAjout = "TRUE";
       return view('canal.caution-agence.index',compact('agences', 'type_cautions','moyen_reglements', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }
    
    public function cautionAgenceVueAgence(){
       $agence = Auth::user()->agence_id;
       $type_cautions = DB::table('type_cautions')->select('type_cautions.*')->Where('deleted_at', NULL)->orderBy('libelle_type_caution', 'ASC')->get();
       $moyen_reglements = DB::table('moyen_reglements')->select('moyen_reglements.*')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'ASC')->get();

       $menuPrincipal = "Canal";
       $titleControlleur = "Caution versée pour le mois en cours";
       $btnModalAjout = "TRUE";
       return view('canal.caution-agence.vu-agence',compact('type_cautions','agence','moyen_reglements', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeCautionAgence()
    {   
            $month = date("m");
      
            $cautions = CautionAgence::with('type_caution','moyen_reglement','agence')
                                        ->Where('caution_agences.deleted_at', NULL) 
                                        ->select('caution_agences.*',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                                        ->whereMonth('caution_agences.date_versement','=', $month)
                                        ->orderBy('caution_agences.confirmer', 'ASC')
                                        ->orderBy('caution_agences.date_versement', 'DESC')
                                        ->get();
       
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
        return response()->json($jsonData);
    }
    
    public function listeCautionAgenceByAgence($agence){
        $cautions = CautionAgence::with('type_caution','moyen_reglement','agence')
                    ->Where([['caution_agences.deleted_at', NULL],['caution_agences.agence_id',$agence]]) 
                    ->select('caution_agences.*',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                    ->orderBy('caution_agences.confirmer', 'ASC')
                    ->orderBy('caution_agences.date_versement', 'DESC')
                    ->get();
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
        return response()->json($jsonData);
    }

    public function listeCautionAgenceByPeriode($debut,$fin){   
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
      
           $cautions = CautionAgence::with('type_caution','moyen_reglement','agence')
                                        ->Where('caution_agences.deleted_at', NULL) 
                                        ->whereDate('caution_agences.date_versement','>=',$date1)
                                        ->whereDate('caution_agences.date_versement','<=', $date2)
                                        ->select('caution_agences.*',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                                        ->orderBy('caution_agences.date_versement', 'DESC')
                                        ->get(); 
        
        
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
        return response()->json($jsonData);
    }
    
    public function listeCautionAgenceByAgencePeriode($agence,$debut,$fin){   
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $cautions = CautionAgence::with('type_caution','moyen_reglement','agence')
                    ->Where([['caution_agences.deleted_at', NULL],['caution_agences.agence_id',$agence]]) 
                    ->whereDate('caution_agences.date_versement','>=',$date1)
                    ->whereDate('caution_agences.date_versement','<=', $date2)
                    ->select('caution_agences.*',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                    ->orderBy('caution_agences.date_versement', 'DESC')
                    ->get();
        $jsonData["rows"] = $cautions->toArray();
        $jsonData["total"] = $cautions->count();
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
        if ($request->isMethod('post') && $request->input('montant_depose') && $request->input('agence_id')) {

                $data = $request->all(); 

            try {
                
                if(isset($data['confirmer']) && !empty($data['confirmer'])){
               
                    //Vérification de la disponibilité du rebi qui concerne ce type de caution
                    $total_rechargement = 0; $total_distribue = 0;
                    $demandeApprs =  DemandeApproviCanal::where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id', $data['type_caution_id']]]) 
                                                   ->select('demande_approvi_canals.*')
                                                   ->get();
                    foreach ($demandeApprs as $demandeAppr){
                        $total_rechargement = $total_rechargement + $demandeAppr->montant_depose;
                    }

                    $demandeApprAgences = CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', $data['type_caution_id']]]) 
                                            ->select('caution_agences.*')
                                            ->get();
                    foreach ($demandeApprAgences as $demandeApprAgence){
                        $total_distribue = $total_distribue + $demandeApprAgence->montant_depose;
                    }

                    $disponible = $total_rechargement-$total_distribue;

                    if($data['montant_depose'] > $disponible){
                        return response()->json(["code" => 0, "msg" => "Le montant disponible pour ce type de caution est inférieur au montant de recharge demandé", "data" => NULL]);
                    }
                }
                $cautionAgence = new CautionAgence;
                $cautionAgence->deposant = $data['deposant'];
                $cautionAgence->date_versement = Carbon::createFromFormat('d-m-Y', $data['date_versement']);
                $cautionAgence->montant_depose = $data['montant_depose'];
                $cautionAgence->type_caution_id = $data['type_caution_id'];
                $cautionAgence->moyen_reglement_id = $data['moyen_reglement_id'];
                $cautionAgence->agence_id = $data['agence_id'];
                $cautionAgence->confirmer = isset($data['confirmer']) && !empty($data['confirmer']) ? TRUE : FALSE;
                $cautionAgence->reference_versement = isset($data['reference_versement']) && !empty($data['reference_versement']) ? $data['reference_versement'] : null;
                
                //Ajout de l'image du matériel s'il y a en 
                if(isset($data['recu_versement'])){
                    $recu_versement = request()->file('recu_versement');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$recu_versement->getClientOriginalName()));
                    //Vérification du format de fichier
                    $extensions = array('.png','.jpg', '.jpeg');
                    $extension = strrchr($file_name, '.');
                    //Début des vérifications de sécurité...
                    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                    {
                        return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                    }
                    $path = public_path().'/img/recu/';
                    $recu_versement->move($path,$file_name);
                    $cautionAgence->recu_versement = 'img/recu/'.$file_name;
                }   
               
                $cautionAgence->created_by = Auth::user()->id;
                $cautionAgence->save();
                
                if($cautionAgence && $cautionAgence->confirmer == 1){
                    $rebi =  new Rebi;
                    $rebi->date_operation = now();
                    $rebi->concerne = "Agence";
                    $rebi->caution_agence_id = $cautionAgence->id;
                    $rebi->montant_recharge_agence = $cautionAgence->montant_depose;
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();
                }
                $jsonData["data"] = json_decode($cautionAgence);
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
    
    
    public function demandeCaution(Request $request){
        $jsonData = ["code" => 1, "msg" => "Demande effectuée avec succès."];
        
        if ($request->isMethod('post') && $request->input('montant_depose')) {

                $data = $request->all(); 

            try {
                
                    $cautionAgence = new CautionAgence;
                    $cautionAgence->deposant = $data['deposant'];
                    $cautionAgence->date_versement = Carbon::createFromFormat('d-m-Y', $data['date_versement']);
                    $cautionAgence->montant_depose = $data['montant_depose'];
                    $cautionAgence->type_caution_id = $data['type_caution_id'];
                    $cautionAgence->moyen_reglement_id = $data['moyen_reglement_id'];
                    $cautionAgence->agence_id = Auth::user()->agence_id;
                    $cautionAgence->reference_versement = isset($data['reference_versement']) && !empty($data['reference_versement']) ? $data['reference_versement'] : null;
                
                    //Ajout de l'image du matériel s'il y a en 
                    if(isset($data['recu_versement'])){
                        $recu_versement = request()->file('recu_versement');
                        $file_name = str_replace(' ', '_', strtolower(time().'.'.$recu_versement->getClientOriginalName()));
                        //Vérification du format de fichier
                        $extensions = array('.png','.jpg', '.jpeg');
                        $extension = strrchr($file_name, '.');
                        //Début des vérifications de sécurité...
                        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                        {
                            return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                        }
                        $path = public_path().'/img/recu/';
                        $recu_versement->move($path,$file_name);
                        $cautionAgence->recu_versement = 'img/recu/'.$file_name;
                    }   
               
                    $cautionAgence->created_by = Auth::user()->id;
                    $cautionAgence->save();
                
                if($cautionAgence){
                    $agence = Agence::find(Auth::user()->agence_id);
                    $to_name = "Multiservices MAN";
                    $to_email = "multiservices811@yahoo.fr";
                    
                    $data = array("name"=>$to_name, "DEMANDE DE RECHARGEMENT CAUTION", "body" => "L'agence ".$agence->libelle_agence." N° de référence ".$agence->numero_identifiant_agence." demande un rechargement de caution. Veillez visiter l'ecran des cautions pour plus de détails.");
  
                    Mail::send('auth/user/mail', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)
                    ->subject('DEMANDE DE RECHARGEMENT CAUTION');
                    $message->from('tranxpert@smartyacademy.com','SMART-SFV');
                    });
                }
                $jsonData["data"] = json_decode($cautionAgence);
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
    
    public function updateDemandeCaution(Request $request){
        
    }
    
    public function confirmation($id){
        
        $jsonData = ["code" => 1, "msg" => "Confirmation effectuée avec succès."];
        
        $cautionAgence = CautionAgence::find($id);
        
        if ($cautionAgence) {

            try {
           
                    //Vérification de la disponibilité du rebi qui concerne ce type de caution
                    $total_rechargement = 0; $total_distribue = 0;
                    $demandeApprs =  DemandeApproviCanal::where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id', $cautionAgence->type_caution_id]]) 
                                                   ->select('demande_approvi_canals.*')
                                                   ->get();
                    foreach ($demandeApprs as $demandeAppr){
                        $total_rechargement = $total_rechargement + $demandeAppr->montant_depose;
                    }

                    $demandeApprAgences = CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', $cautionAgence->type_caution_id]]) 
                                            ->select('caution_agences.*')
                                            ->get();
                    foreach ($demandeApprAgences as $demandeApprAgence){
                        $total_distribue = $total_distribue + $demandeApprAgence->montant_depose;
                    }

                    $disponible = $total_rechargement-$total_distribue;

                    if($cautionAgence->montant_depose > $disponible){
                        return response()->json(["code" => 0, "msg" => "Le montant disponible pour ce type de caution est inférieur au montant de recharge demandé", "data" => NULL]);
                    }
                    
                    $cautionAgence->confirmer = TRUE;
                    $cautionAgence->save();

                    $rebi =  new Rebi;
                    $rebi->date_operation = now();
                    $rebi->concerne = "Agence";
                    $rebi->caution_agence_id = $cautionAgence->id;
                    $rebi->montant_recharge_agence = $cautionAgence->montant_depose;
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();

                    $jsonData["data"] = json_decode($cautionAgence);
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
     * @param  \App\CautionAgence  $cautionAgence
     * @return Response
     */
    public function updateCautionAgence(Request $request)
    {
       $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        $cautionAgence = CautionAgence::find($data['idCautionAgence']);
        
        if($cautionAgence){
            try {
                $old_confirm = $cautionAgence->confirmer;
                if(!isset($data['confirmer']) && empty($data['confirmer']) && $cautionAgence->confirmer==1){
                    $rebi = Rebi::where('caution_agence_id',$cautionAgence->id)->first();
                    if($rebi!=null){
                        $rebi->delete();
                    }
                }
                
                if(isset($data['confirmer']) && !empty($data['confirmer']) && $cautionAgence->confirmer==0){
                    //Vérification de la disponibilité du rebi qui concerne ce type de caution
                    $total_rechargement = 0; $total_distribue = 0;
                    $demandeApprs =  DemandeApproviCanal::where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id', $data['type_caution_id']]]) 
                                                   ->select('demande_approvi_canals.*')
                                                   ->get();
                    foreach ($demandeApprs as $demandeAppr){
                        $total_rechargement = $total_rechargement + $demandeAppr->montant_depose;
                    }

                    $demandeApprAgences = CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.type_caution_id', $data['type_caution_id']]]) 
                                            ->select('caution_agences.*')
                                            ->get();
                    foreach ($demandeApprAgences as $demandeApprAgence){
                        $total_distribue = $total_distribue + $demandeApprAgence->montant_depose;
                    }

                    $disponible = ($total_rechargement+$cautionAgence->montant_depose)-$total_distribue;

                    if($data['montant_depose'] > $disponible){
                        return response()->json(["code" => 0, "msg" => "Le montant disponible pour ce type de caution est inférieur au montant de recharge demandé", "data" => NULL]);
                    }
                }
                
                $cautionAgence->deposant = $data['deposant'];
                $cautionAgence->date_versement = Carbon::createFromFormat('d-m-Y', $data['date_versement']);
                $cautionAgence->montant_depose = $data['montant_depose'];
                $cautionAgence->type_caution_id = $data['type_caution_id'];
                $cautionAgence->moyen_reglement_id = $data['moyen_reglement_id'];
                $cautionAgence->agence_id = $data['agence_id'];
                $cautionAgence->confirmer = isset($data['confirmer']) && !empty($data['confirmer']) ? TRUE : FALSE;
                $cautionAgence->reference_versement = isset($data['reference_versement']) && !empty($data['reference_versement']) ? $data['reference_versement'] : null;
                
                //Ajout de l'image du matériel s'il y a en 
                if(isset($data['recu_versement'])){
                    $recu_versement = request()->file('recu_versement');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$recu_versement->getClientOriginalName()));
                    //Vérification du format de fichier
                    $extensions = array('.png','.jpg', '.jpeg');
                    $extension = strrchr($file_name, '.');
                    //Début des vérifications de sécurité...
                    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
                    {
                        return response()->json(["code" => 0, "msg" => "Vous devez uploader un fichier de type jpeg png, jpg", "data" => NULL]);
                    }
                    $path = public_path().'/img/recu/';
                    $recu_versement->move($path,$file_name);
                    $cautionAgence->recu_versement = 'img/recu/'.$file_name;
                }   
           
                $cautionAgence->updated_by = Auth::user()->id;
                $cautionAgence->save();
                
                if($cautionAgence && $cautionAgence->confirmer == 1 && $old_confirm==0){
                    $rebi =  new Rebi;
                    $rebi->date_operation = now();
                    $rebi->concerne = "Agence";
                    $rebi->caution_agence_id = $cautionAgence->id;
                    $rebi->montant_recharge_agence = $cautionAgence->montant_depose;
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();
                }
             
                $jsonData["data"] = json_decode($cautionAgence);
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
     * @param  \App\CautionAgence  $cautionAgence
     * @return Response
     */
    public function destroy($id)
    {
        $cautionAgence = CautionAgence::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($cautionAgence){
                try {
                    $rebi = Rebi::where('caution_agence_id',$cautionAgence->id)->first();
                    if($rebi!=null){
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->delete();
                    }
                $cautionAgence->update(['deleted_by' => Auth::user()->id]);
                $cautionAgence->delete();
                $jsonData["data"] = json_decode($cautionAgence);
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
    
    //Liste payement caution agence
    public function listeCautionAgencePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeCautionAgences());
        return $pdf->stream('liste_payement_caution_agence.pdf');
    }
    public function listeCautionAgences(){
         $month = date("m");
         $datas = CautionAgence::where('caution_agences.deleted_at', NULL)
                    ->join('type_cautions','type_cautions.id','=','caution_agences.type_caution_id') 
                    ->join('moyen_reglements','moyen_reglements.id','=','caution_agences.moyen_reglement_id') 
                    ->join('agences','agences.id','=','caution_agences.agence_id') 
                    ->select('caution_agences.*','agences.libelle_agence','agences.numero_identifiant_agence', 'type_cautions.libelle_type_caution','moyen_reglements.libelle_moyen_reglement',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                    ->whereMonth('caution_agences.date_versement','=', $month)
                    ->orderBy('caution_agences.date_versement', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution par les agences pour le mois en cours</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Agence</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Code Agence</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Caution</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Moyen de payement</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_versements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_identifiant_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_caution.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_moyen_reglement.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste payement caution agence par agence
    public function listeCautionAgenceByAgencePdf($agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeCautionAgenceByAgences($agence));
        $infor_agence= Agence::find($agence);
        return $pdf->stream('liste_payement_caution_agence_'.$infor_agence->libelle_agence.'.pdf');
    }
    public function listeCautionAgenceByAgences($agence){
        $infor_agence = Agence::find($agence);
         $datas = CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.agence_id',$agence]])
                    ->join('type_cautions','type_cautions.id','=','caution_agences.type_caution_id') 
                    ->join('moyen_reglements','moyen_reglements.id','=','caution_agences.moyen_reglement_id') 
                    ->join('agences','agences.id','=','caution_agences.agence_id') 
                    ->select('caution_agences.*','agences.libelle_agence','type_cautions.libelle_type_caution','moyen_reglements.libelle_moyen_reglement',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                    ->orderBy('caution_agences.date_versement', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements de caution agence '.$infor_agence->libelle_agence.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Caution</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_versements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_caution.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_moyen_reglement.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    //Liste payement caution agence par période
    public function listeCautionAgenceByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeCautionAgenceByPeriodes($debut,$fin));
        return $pdf->stream('liste_payement_caution_agence_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeCautionAgenceByPeriodes($debut,$fin){
         $date1 = Carbon::createFromFormat('d-m-Y', $debut);
         $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas = CautionAgence::where('caution_agences.deleted_at', NULL)
                    ->join('type_cautions','type_cautions.id','=','caution_agences.type_caution_id') 
                    ->join('moyen_reglements','moyen_reglements.id','=','caution_agences.moyen_reglement_id') 
                    ->join('agences','agences.id','=','caution_agences.agence_id') 
                    ->select('caution_agences.*','agences.libelle_agence','agences.numero_identifiant_agence', 'type_cautions.libelle_type_caution','moyen_reglements.libelle_moyen_reglement',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                    ->whereDate('caution_agences.date_versement','>=',$date1)
                    ->whereDate('caution_agences.date_versement','<=', $date2)
                    ->orderBy('caution_agences.date_versement', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution par les agences du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Agence</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Code agence</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Caution</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Moyen de payement</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_versements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_identifiant_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_caution.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_moyen_reglement.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste payement caution agence par période pour une agence
    public function listeCautionAgenceByAgencePeriodePdf($agence,$debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeCautionAgenceByAgencePeriodes($agence,$debut,$fin));
        $infor_agence= Agence::find($agence);
        return $pdf->stream('liste_payement_caution_de_'.$infor_agence->libelle_agence.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeCautionAgenceByAgencePeriodes($agence,$debut,$fin){
         $infor_agence= Agence::find($agence);
         $date1 = Carbon::createFromFormat('d-m-Y', $debut);
         $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $datas = CautionAgence::where([['caution_agences.deleted_at', NULL],['caution_agences.agence_id',$agence]])
                    ->join('type_cautions','type_cautions.id','=','caution_agences.type_caution_id') 
                    ->join('moyen_reglements','moyen_reglements.id','=','caution_agences.moyen_reglement_id') 
                    ->join('agences','agences.id','=','caution_agences.agence_id') 
                    ->select('caution_agences.*','agences.libelle_agence','type_cautions.libelle_type_caution','moyen_reglements.libelle_moyen_reglement',DB::raw('DATE_FORMAT(caution_agences.date_versement, "%d-%m-%Y") as date_versements'))
                    ->whereDate('caution_agences.date_versement','>=',$date1)
                    ->whereDate('caution_agences.date_versement','<=', $date2)
                    ->orderBy('caution_agences.date_versement', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution du '.$debut.' au '.$fin.' par '.$infor_agence->libelle_agence.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_versements.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_moyen_reglement.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
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
