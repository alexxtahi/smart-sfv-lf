<?php

namespace App\Http\Controllers\Canal;

use App\Helpers\ConfigurationHelper\Configuration;
use App\Http\Controllers\Controller;
use App\Models\Canal\Abonnement;
use App\Models\Canal\Agence;
use App\Models\Canal\CautionAgence;
use App\Models\Canal\MaterielVendue;
use App\Models\Canal\Rebi;
use App\Models\Canal\VenteMateriel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;

class VenteMaterielController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $materiels = DB::table('materiels')->select('materiels.*')->Where('deleted_at', NULL)->orderBy('libelle_materiel', 'ASC')->get();
       $clients = DB::table('clients')->select('clients.*')->Where('deleted_at', NULL)->orderBy('full_name_client', 'ASC')->get();
       $agences = DB::table('agences')->select('agences.*')->Where('deleted_at', NULL)->orderBy('libelle_agence', 'ASC')->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Vente de matériel";
       (Auth::user()->role=='Agence') ? $btnModalAjout = "TRUE" : $btnModalAjout = "FALSE";
       return view('canal.vente.index',compact('materiels','clients','agences', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeVenteMateriel()
    {
      if(Auth::user()->role=='Agence'){
            $ventes = VenteMateriel::where([['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',Auth::user()->agence_id]])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        }else{
            $ventes = VenteMateriel::with('agence') 
                                    ->where('vente_materiels.deleted_at', NULL)
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        }
        
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function listeVenteMaterielByFacture($dnumero_facture)
    {
      if(Auth::user()->role=='Agence'){
            $ventes = VenteMateriel::where([['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',Auth::user()->agence_id],['vente_materiels.numero_facture', 'like', '%' . $dnumero_facture . '%']])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        }else{
            $ventes = VenteMateriel::with('agence')
                                    ->where([['vente_materiels.deleted_at', NULL],['vente_materiels.numero_facture', 'like', '%' . $dnumero_facture . '%']])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        }
        
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function listeVenteMaterielByDate($dates)
    {
        $date = Carbon::createFromFormat('d-m-Y', $dates);
      if(Auth::user()->role=='Agence'){
            $ventes = VenteMateriel::where([['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',Auth::user()->agence_id]])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->whereDate('vente_materiels.date_vente','=',$date)
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        }else{
            $ventes = VenteMateriel::with('agence')
                                    ->where('vente_materiels.deleted_at', NULL)
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->whereDate('vente_materiels.date_vente','=',$date)
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        }
        
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function listeVenteMaterielByAgence($agence){
        $ventes = VenteMateriel::with('agence')
                                    ->where([['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',$agence]])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
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
        if ($request->isMethod('post') && $request->input('monPanier')) {

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
              
                $caution_equipement_disponible = $total_caution_equip-$total_caution_equipement_vendu;
              
                if($data['montantTTC_input'] > $caution_equipement_disponible){
                    return response()->json(["code" => 0, "msg" => "Le montant disponible pour la caution des équipements est inférieur au montant demandé", "data" => NULL]);
                }
                
                //formation numéro facture
                $maxIdVente = DB::table('vente_materiels')->max('id');
                $numero_facture = sprintf("%06d", ($maxIdVente + 1));
                    
                $vente = new VenteMateriel;
                $vente->date_vente = now();
                $vente->numero_facture = $numero_facture.date("Y");
                $vente->agence_id = Auth::user()->agence_id;
//                $vente->client_id = isset($data["client_id"]) && !empty($data["client_id"]) ? $data["client_id"] : null;
                $vente->created_by = Auth::user()->id;
                $vente->save();
                
                if($vente && !empty($data["monPanier"])){
                    
                    $panierContent = is_array($data["monPanier"]) ? $data["monPanier"] : array($data["monPanier"]);
                    $montantTTC = 0;
                    foreach($panierContent as $index => $materiel) {
                        
                        $materielVendue = new MaterielVendue;
                        $materielVendue->materiel_id = $data["monPanier"][$index]["materiels"];
                        $materielVendue->vente_materiel_id = $vente->id;
                        $materielVendue->quantite = $data["monPanier"][$index]["quantites"];
                        $materielVendue->prix = $data["monPanier"][$index]["prix"];
                        $materielVendue->save();
                        
                        $montantTTC = $montantTTC + ($data["monPanier"][$index]["prix"] * $data["monPanier"][$index]["quantites"]);
                    }
                    
                    $rebi = new Rebi;
                    $rebi->date_operation = now();
                    $rebi->vente_materiel_id = $vente->id;
                    $rebi->concerne = "Client";
                    $rebi->montant_recharge_client = $montantTTC;
                    $rebi->created_by = Auth::user()->id;
                    $rebi->save();
                }
                
            $jsonData["data"] = json_decode($vente);
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
     * @param  \App\VenteMateriel  $venteMateriel
     * @return Response
     */
    public function updateVenteMateriel(Request $request)
    {
        $vente = VenteMateriel::find($request->get('idVente'));
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        if($vente){
           
            try {
                
//                $vente->client_id = isset($data["client_id"]) && !empty($data["client_id"]) ? $data["client_id"] : null;
                $vente->updated_by = Auth::user()->id;
                $vente->save();
                
           $jsonData["data"] = json_decode($vente);
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
     * @param  \App\VenteMateriel  $venteMateriel
     * @return Response
     */
    public function destroy(VenteMateriel $venteMateriel)
    {
        
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        if($venteMateriel){
            try {
                $materielVendus = MaterielVendue::where('vente_materiel_id',$venteMateriel->id)->get();
                $rebi = Rebi::where('vente_materiel_id',$venteMateriel->id)->first();
                
                foreach ($materielVendus as $materielVendu){
                    MaterielVendue::where('id', $materielVendu->id)->delete();
                }
                
                $rebi->delete();
                
                $venteMateriel->update(['deleted_by' => Auth::user()->id]);
                $venteMateriel->delete();
                $jsonData["data"] = json_decode($venteMateriel);
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
        $get_configuration_infos = Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    
    
    //facture 
    public function factureVenteMaterielPdf($id){
         $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->ticketVente($id));
        $ticket = VenteMateriel::find($id);
        return $pdf->stream('Facture_'.$ticket->numero_facture.'.pdf');
    }
    
     public function ticketVente($id){
        $outPut = $this->ticketHeader($id);
        $outPut.= $this->ticketContent($id);
        return $outPut;
    }
    
    public function ticketHeader($id){
        
        $info_en_tete = VenteMateriel::where([['vente_materiels.id', $id],['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',Auth::user()->agence_id]])
                                    ->join('agences','agences.id','=','vente_materiels.agence_id')
                                    ->select('vente_materiels.numero_facture','agences.libelle_agence',DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->first();
          
        $header = "<html>
                        <head>
                            <meta charset='utf-8'>
                            <title></title>
                                    <style>
                                        @page { size: 30cm 15cm landscape; }
                                       .container{
                                            width: 100%;
                                            margin: 0 5px;
                                            font-size:15px;
                                        }
                                        .container-table{        
                                            width: 100%;
                                        }
                                    </style>
                        </head>
                <body style='margin-bottom:0; margin-top:0px; font-size:16px;'>
                <p style='text-align:center; font-size:16px;'>
                    <b>FACTURE N° ".$info_en_tete['numero_facture']."</b><br/>
                    <img src=".$this->infosConfig()->logo." width='180' height='160'/><br/>
                     ".$this->infosConfig()->commune_compagnie."<br/>
                    Tel : ".$this->infosConfig()->cellulaire."<br/>
                     ".$this->infosConfig()->email_compagnie."
                    <hr/>
                </p>
                <p style='line-height:1.6; font-size:16px;'>
                   Date <b>".$info_en_tete['date_ventes']."</b><br/>
                   Agence : <b>".$info_en_tete['libelle_agence']."</b><br/>
                   <hr/>
                </p>";     
        return $header;
    }
    
    public function ticketContent($id){
        $generator = new BarcodeGeneratorPNG();
        $montantTTTC_add=0;
        
        $materielVendus = MaterielVendue::where([['materiel_vendues.vente_materiel_id', $id],['materiel_vendues.retourne', 0]])
                            ->join('materiels','materiels.id','=','materiel_vendues.materiel_id')
                            ->select('materiel_vendues.*','materiels.libelle_materiel')
                            ->get();
        
        $vente = VenteMateriel::where([['vente_materiels.id', $id],['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',Auth::user()->agence_id]])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->first();

        $content = '<div class="container-table" style="font-size:16px;">
                        <table border="0" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="45%" align="left">Matériel</th>
                                <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                                <th cellspacing="0" border="2" width="25%" align="center">Prix U</th>
                                <th cellspacing="0" border="2" width="30%" align="center">Montant TTC</th>
                            </tr>';
        $articlesTotal = 0; $montantApayer = 0;
        foreach($materielVendus as $element){
            $articlesTotal = $articlesTotal +1;
          
            $montantApayer = $montantApayer + ($element->prix*$element->quantite);
            $content.='<tr>
                            <td style="font-size:15px;"  cellspacing="0" border="2" align="left" width="45%">'.$element->libelle_materiel.'</td>
                            <td style="font-size:15px;"  cellspacing="0" border="2" align="center" width="10%">'.$element->quantite.'</td>
                            <td style="font-size:15px;"  cellspacing="0" border="2" align="right" width="25%">'.number_format($element->prix, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:15px;"  cellspacing="0" border="2" align="right" width="30%">'.number_format($element->prix*$element->quantite, 0, ',', ' ').'&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }
        $content.='</table><hr/>';
        $content.='<p>Total articles : '.$articlesTotal.'</p>';
        $content.='<p align="right" style="font-size:16px;"><b>NET A PAYER &nbsp;&nbsp;'.number_format($montantApayer, 0, ',', ' ').'</b></p>
                   <p align="center" style="font-size:16px;"><b>MERCI POUR VOTRE VISITE</b></p>
                   <p align="center"><img src="data:image/png;base64,'.base64_encode($generator->getBarcode(123456789, $generator::TYPE_CODE_128)).'"></p>
                   <p align="center" style="font-size:16px;"><b>'.$vente["numero_facture"].'</b><i>&nbsp;&nbsp;Editer le '.date('d-m-Y').' à '.date("H:i:s").'</i></p>
                </div>';
       return $content;
    }



    //Etat
    public function allVenteMaterielPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMateriel());
        return $pdf->stream('liste_vente_materiel.pdf');
    }
    public function allVenteMateriel(){
        $datas = VenteMateriel::where('vente_materiels.deleted_at', NULL) 
                                    ->join('agences','agences.id','=','vente_materiels.agence_id')
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*','agences.libelle_agence',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des ventes de matériel pour toutes les agences</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Agence</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + $data->sommeTotale;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Montant total :<b> '.number_format($total, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function allVenteMaterielByAgencePdf($agence){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMaterielByAgence($agence));
        $infos_agence = Agence::find($agence);
        return $pdf->stream('liste_vente_materiel_par_'.$infos_agence->libelle_agence.'.pdf');
    }
    public function allVenteMaterielByAgence($agence){
        $infos_agence = Agence::find($agence);
        $datas = VenteMateriel::where([['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',$agence]]) 
                                    ->join('agences','agences.id','=','vente_materiels.agence_id')
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*','agences.libelle_agence',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des ventes de matériel par '.$infos_agence->libelle_agence.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="30%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Montant TTC</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + $data->sommeTotale;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Montant total :<b> '.number_format($total, 0, ',', ' ').'F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function allVenteMaterielByAgenceDatePdf($agence,$date){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMaterielByAgenceDate($agence,$date));
        $infos_agence = Agence::find($agence);
        return $pdf->stream('liste_vente_materiel_par_'.$infos_agence->libelle_agence.'_au_'.$date.'.pdf');
    }
    public function allVenteMaterielByAgenceDate($agence,$dates){
        $infos_agence = Agence::find($agence);
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        
        $datas = VenteMateriel::where([['vente_materiels.deleted_at', NULL],['vente_materiels.agence_id',$agence]])
                                    ->join('materiel_vendues','materiel_vendues.vente_materiel_id','=','vente_materiels.id')->Where('materiel_vendues.retourne',0)
                                    ->select('vente_materiels.*',DB::raw('sum(materiel_vendues.quantite*materiel_vendues.prix) as sommeTotale'),DB::raw('DATE_FORMAT(vente_materiels.date_vente, "%d-%m-%Y") as date_ventes'))
                                    ->whereDate('vente_materiels.date_vente','=',$date)
                                    ->groupBy('materiel_vendues.vente_materiel_id')
                                    ->orderBy('vente_materiels.id', 'DESC')
                                    ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des ventes de matériel par '.$infos_agence->libelle_agence.' au '.$dates.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="30%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="40%" align="center">Montant TTC</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + $data->sommeTotale;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Montant total :<b> '.number_format($total, 0, ',', ' ').'F CFA</b>';
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
