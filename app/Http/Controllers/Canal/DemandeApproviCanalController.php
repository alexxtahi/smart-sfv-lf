<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\DemandeApproviCanal;
use App\Models\Canal\Rebi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandeApproviCanalController extends Controller
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
       $titleControlleur = "Demande d'approvisionnement chez canal pour le mois en cours";
       $btnModalAjout = "TRUE";
       return view('canal.approvsionnement.index',compact('type_cautions', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
 
    }

    public function listeDemandeApprov()
    {   $month = date("m");
        $demandes = DemandeApproviCanal::with('type_caution')
                    ->Where('demande_approvi_canals.deleted_at', NULL) 
                    ->select('demande_approvi_canals.*',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->whereMonth('demande_approvi_canals.date_demande','=', $month)
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
        $jsonData["rows"] = $demandes->toArray();
        $jsonData["total"] = $demandes->count();
        return response()->json($jsonData);
    }
    
    public function listeDemandeApprovByTypeCaution($type_caution){
        $demandes = DemandeApproviCanal::with('type_caution')
                    ->Where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id',$type_caution]]) 
                    ->select('demande_approvi_canals.*',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
        $jsonData["rows"] = $demandes->toArray();
        $jsonData["total"] = $demandes->count();
        return response()->json($jsonData);
    }
    
    public function listeDemandeApprovByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $demandes = DemandeApproviCanal::with('type_caution')
                    ->Where('demande_approvi_canals.deleted_at', NULL) 
                    ->whereDate('demande_approvi_canals.date_demande','>=',$date1)
                    ->whereDate('demande_approvi_canals.date_demande','<=', $date2)
                    ->select('demande_approvi_canals.*',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
        $jsonData["rows"] = $demandes->toArray();
        $jsonData["total"] = $demandes->count();
        return response()->json($jsonData);
    }
    public function listeDemandeApprovByPeriodeTypeCaution($debut,$fin,$type_caution){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $demandes = DemandeApproviCanal::with('type_caution')
                    ->Where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id',$type_caution]]) 
                    ->whereDate('demande_approvi_canals.date_demande','>=',$date1)
                    ->whereDate('demande_approvi_canals.date_demande','<=', $date2)
                    ->select('demande_approvi_canals.*',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
        $jsonData["rows"] = $demandes->toArray();
        $jsonData["total"] = $demandes->count();
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
        if ($request->isMethod('post') && $request->input('montant_depose') && $request->input('reference_versement')) {

                $data = $request->all(); 

            try {
               
                $maxIdDemande = DB::table('demande_approvi_canals')->max('id');
                $annee = date("Y");
                $numero_id = sprintf("%06d", ($maxIdDemande + 1));
                
                $demandeApproviCanal = new DemandeApproviCanal;
                $demandeApproviCanal->numero_demande = $numero_id.$annee;
                $demandeApproviCanal->deposant = $data['deposant'];
                $demandeApproviCanal->date_demande = Carbon::createFromFormat('d-m-Y', $data['date_demande']);
                $demandeApproviCanal->montant_depose = $data['montant_depose'];
                $demandeApproviCanal->type_caution_id = $data['type_caution_id'];
                $demandeApproviCanal->approvisionne = isset($data['approvisionne']) && !empty($data['approvisionne']) ? TRUE : FALSE;
                $demandeApproviCanal->reference_versement = isset($data['reference_versement']) && !empty($data['reference_versement']) ? $data['reference_versement'] : null;
                
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
                    $demandeApproviCanal->recu_versement = 'img/recu/'.$file_name;
                }   
                $demandeApproviCanal->created_by = Auth::user()->id;
                $demandeApproviCanal->save();
                if($demandeApproviCanal->approvisionne==1){
                    $rebi =  new Rebi;
                    $rebi->date_operation = now();
                    $rebi->concerne = "Canal";
                    $rebi->demande_approvi_canal_id = $demandeApproviCanal->id;
                    $rebi->montant_recharge = $demandeApproviCanal->montant_depose;
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();
                }
                $jsonData["data"] = json_decode($demandeApproviCanal);
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
     * @param  \App\DemandeApproviCanal  $demandeApproviCanal
     * @return Response
     */
    public function updateDemandeApprov(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        $demandeApproviCanal = DemandeApproviCanal::find($data['idDemandeApproviCanal']);
        
        if($demandeApproviCanal){
            try {
                $oldApprov = $demandeApproviCanal->approvisionne;
                $demandeApproviCanal->deposant = $data['deposant'];
                $demandeApproviCanal->date_demande = Carbon::createFromFormat('d-m-Y', $data['date_demande']);
                $demandeApproviCanal->montant_depose = $data['montant_depose'];
                $demandeApproviCanal->type_caution_id = $data['type_caution_id'];
                $demandeApproviCanal->approvisionne = isset($data['approvisionne']) && !empty($data['approvisionne']) ? TRUE : FALSE;
                $demandeApproviCanal->reference_versement = isset($data['reference_versement']) && !empty($data['reference_versement']) ? $data['reference_versement'] : null;

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
                    $demandeApproviCanal->recu_versement = 'img/recu/'.$file_name;
                }   
           
                $demandeApproviCanal->updated_by = Auth::user()->id;
                $demandeApproviCanal->save();
                
                if($demandeApproviCanal->approvisionne==1 && $oldApprov==0){
                    $rebi = Rebi::where('demande_approvi_canal_id',$demandeApproviCanal->id)->first();
                    if($rebi!=null){
                        $rebi->date_operation = now();
                        $rebi->concerne = "Canal";
                        $rebi->demande_approvi_canal_id = $demandeApproviCanal->id;
                        $rebi->montant_recharge = $demandeApproviCanal->montant_depose;
                        $rebi->updated_by = Auth::user()->id;
                        $rebi->save();
                    }else{
                        $rebi =  new Rebi;
                        $rebi->date_operation = now();
                        $rebi->concerne = "Canal";
                        $rebi->demande_approvi_canal_id = $demandeApproviCanal->id;
                        $rebi->montant_recharge = $demandeApproviCanal->montant_depose;
                        $rebi->created_by = Auth::user()->id;
                        $rebi->save();
                    }
                }
                if($demandeApproviCanal->approvisionne==1 && $oldApprov==1){
                    $rebi = Rebi::where('demande_approvi_canal_id',$demandeApproviCanal->id)->first();
                    $rebi->montant_recharge = $demandeApproviCanal->montant_depose;
                    $rebi->updated_by = Auth::user()->id;
                    $rebi->save();
                }
                if($demandeApproviCanal->approvisionne==0 && $oldApprov==1){
                    $rebi = Rebi::where('demande_approvi_canal_id',$demandeApproviCanal->id)->first();
                    $rebi->montant_recharge = 0;
                    $rebi->updated_by = Auth::user()->id;
                    $rebi->save();
                }
                $jsonData["data"] = json_decode($demandeApproviCanal);
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
     * @param  \App\DemandeApproviCanal  $demandeApproviCanal
     * @return Response
     */
    public function destroy($id)
    {
        $demandeApproviCanal = DemandeApproviCanal::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($demandeApproviCanal){
                try {
               
                $demandeApproviCanal->update(['deleted_by' => Auth::user()->id]);
                $demandeApproviCanal->delete();
                $jsonData["data"] = json_decode($demandeApproviCanal);
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
    
    //Liste payement caution
    public function listeCautionCanalPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeCautionCanal());
        return $pdf->stream('liste_payement_caution.pdf');
    }
    public function listeCautionCanal(){
        $month = date("m");
        $datas = DemandeApproviCanal::where('demande_approvi_canals.deleted_at', NULL)
                    ->join('type_cautions','type_cautions.id','=','demande_approvi_canals.type_caution_id')
                    ->select('demande_approvi_canals.*','type_cautions.libelle_type_caution',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->whereMonth('demande_approvi_canals.date_demande','=', $month)
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution Canal pour le mois en cours</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° demande</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Caution</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Approuvé</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           $data->approvisionne == 1 ? $approvisionne = "OUI" : $approvisionne = "NON";
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_demande.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_demandes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_caution.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$approvisionne.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste payement caution par type
    public function listeCautionCanalByTypePdf($type_caution){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeCautionCanalByType($type_caution));
        $infor_type_caution= \App\Models\Canal\TypeCaution::find($type_caution);
        return $pdf->stream('liste_payement_caution_du_type_'.$infor_type_caution->libelle_type_caution.'.pdf');
    }
    public function listeCautionCanalByType($type_caution){
        $infor_type_caution= \App\Models\Canal\TypeCaution::find($type_caution);
        $datas = DemandeApproviCanal::where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id',$type_caution]]) 
                    ->join('type_cautions','type_cautions.id','=','demande_approvi_canals.type_caution_id')
                    ->select('demande_approvi_canals.*','type_cautions.libelle_type_caution',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution Canal du type '.$infor_type_caution->libelle_type_caution.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                           <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° demande</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Approuvé</th>
                        </tr>
                    </div>';
         $total = 0; $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           $data->approvisionne == 1 ? $approvisionne = "OUI" : $approvisionne = "NON";
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_demande.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_demandes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$approvisionne.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste payement caution par période
    public function listeCautionCanalByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeCautionCanalByPeriode($debut,$fin));
        return $pdf->stream('liste_payement_caution_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeCautionCanalByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas =DemandeApproviCanal::where('demande_approvi_canals.deleted_at', NULL) 
                    ->join('type_cautions','type_cautions.id','=','demande_approvi_canals.type_caution_id')
                    ->whereDate('demande_approvi_canals.date_demande','>=',$date1)
                    ->whereDate('demande_approvi_canals.date_demande','<=', $date2)
                    ->select('demande_approvi_canals.*','type_cautions.libelle_type_caution',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
                   
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution Canal du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° demande</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Caution</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Approuvé</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           $data->approvisionne == 1 ? $approvisionne = "OUI" : $approvisionne = "NON";
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_demande.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_demandes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_type_caution.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$approvisionne.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').'</b> versement(s) pour un montant total de <b>'.number_format($montantTotal, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Liste payement caution par type sur une période
    public function listeCautionCanalByTypePeriodePdf($type_caution,$debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeCautionCanalByTypePeriode($type_caution,$debut,$fin));
        $infor_type_caution= \App\Models\Canal\TypeCaution::find($type_caution);
        return $pdf->stream('liste_payement_caution_du_type_'.$infor_type_caution->libelle_type_caution.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function listeCautionCanalByTypePeriode($type_caution,$debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $infor_type_caution= \App\Models\Canal\TypeCaution::find($type_caution);
        $datas =DemandeApproviCanal::where([['demande_approvi_canals.deleted_at', NULL],['demande_approvi_canals.type_caution_id',$type_caution]]) 
                    ->join('type_cautions','type_cautions.id','=','demande_approvi_canals.type_caution_id')
                    ->whereDate('demande_approvi_canals.date_demande','>=',$date1)
                    ->whereDate('demande_approvi_canals.date_demande','<=', $date2)
                    ->select('demande_approvi_canals.*','type_cautions.libelle_type_caution',DB::raw('DATE_FORMAT(demande_approvi_canals.date_demande, "%d-%m-%Y") as date_demandes'))
                    ->orderBy('demande_approvi_canals.approvisionne', 'ASC','demande_approvi_canals.date_demande', 'DESC')
                    ->get();
                   
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des versements caution du type '.$infor_type_caution->libelle_type_caution.' sur la période du '.$debut.' au '.$fin.' chez Canal</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° demande</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Déposant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Ref. versement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Approuvé</th>
                        </tr>
                    </div>';
         $total = 0;  $montantTotal = 0;
       foreach ($datas as $data){
           $total = $total + 1; $montantTotal = $montantTotal + $data->montant_depose; 
           $data->approvisionne == 1 ? $approvisionne = "OUI" : $approvisionne = "NON";
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_demande.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_demandes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_depose, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->deposant.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->reference_versement.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$approvisionne.'</td>
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
