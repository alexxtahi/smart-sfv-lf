<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\Billetage;
use App\Models\Boutique\CaisseOuverte;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Caisse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\json_decode;
use function now;
use function response;

class CaisseOuverteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }
    
    public function getCaisseOuverteById($id){
        $caisse_ouverte = DB::table('caisse_ouvertes')
                ->select('caisse_ouvertes.*')
                ->Where([['deleted_at', NULL],['caisse_ouvertes.id',$id]])
                ->get();
       $jsonData["rows"] = $caisse_ouverte->toArray();
       $jsonData["total"] = $caisse_ouverte->count();
       return response()->json($jsonData);
    }
    public function getOneCaisseOuverteByCaisse($caisse){
        $caisse_ouverte =  Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisse_ouvertes.caisse_id',$caisse]])
                                        ->groupBy('caisse_ouvertes.id')
                                        ->orderBy('ventes.id','DESC')
                                        ->get();
       $jsonData["rows"] = $caisse_ouverte->toArray();
       $jsonData["total"] = $caisse_ouverte->count();
       return response()->json($jsonData);
    }
    
    public function listeRaportCaisseByDepot($depot){
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function listeRaportCaisseByDepotPeriode($debut,$fin,$depot){
         $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.depot_id',$depot]])
                    ->whereDate('ventes.date_vente','>=',$date1)
                   ->whereDate('ventes.date_vente','<=', $date2)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeRaportCaisseByPeriode($debut,$fin){
         $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente','>=',$date1)
                   ->whereDate('ventes.date_vente','<=', $date2)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
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
        if ($request->isMethod('post') && $request->input('caisse_id')) {
                $data = $request->all(); 
            try {
                //Si la caisse est déjà ouverte ou n'existe pas
                $Caisse = Caisse::find($data['caisse_id']);
                if($Caisse->ouvert==1 or !$Caisse){
                    return response()->json(["code" => 0, "msg" => "Cette caisse est déjà ouverte ou n'existe pas", "data" => NULL]);
                }
                //Si la personne à déjà fait une ouverture de caisse sans la fermer
                $caisse_ouverte_sans_fermee = CaisseOuverte::where([['caisse_id',$data['caisse_id']],['user_id',Auth::user()->id],['date_fermeture',null]])->first();
                if($caisse_ouverte_sans_fermee!=null){
                    return response()->json(["code" => 0, "msg" => "Vous avez une session ouverte sur cette caisse.", "data" => NULL]);
                }
                
                //Mise à jour
                $Caisse->ouvert = TRUE;
                $Caisse->updated_by = Auth::user()->id;
                $Caisse->save();
                
                $caisseOuverte = new CaisseOuverte;
                $caisseOuverte->montant_ouverture = $data['montant_ouverture'];
                $caisseOuverte->date_ouverture = now();
                $caisseOuverte->caisse_id = $data['caisse_id'];
                $caisseOuverte->user_id = Auth::user()->id;
                $caisseOuverte->created_by = Auth::user()->id;
                $caisseOuverte->save();
                
                //Stockage en session
                $request->session()->put('session_caisse_ouverte',$caisseOuverte->id);
                $jsonData["data"] = json_decode($caisseOuverte);
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
     * @param  \App\CaisseOuverte  $caisseOuverte
     * @return Response
     */
    public function update(Request $request, CaisseOuverte $caisseOuverte)
    {
        //
    }

    /**
     * Cette méthode va servir à fermer la caisse ouverte en session
     *
     * @param  \App\CaisseOuverte  $caisseOuverte
     * @return Response
     */
    public function fermetureCaisse(Request $request)
    {
       
       $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
        $caisseOuverte = CaisseOuverte::where([['caisse_id',$request->caisses_fermeture],['date_fermeture',null]])->first();
        if($caisseOuverte){
            try {
                //On récupere la caisse pour fermer
                $caisse = Caisse::find($request->caisses_fermeture);

                if($caisse->ouvert==0 or !$caisse){
                    return response()->json(["code" => 0, "msg" => "Cette caisse est déjà fermée ou n'existe pas", "data" => NULL]);
                }
                $data = $request->all();
                //Controle du billetage
                if(empty($data["panierBillet"])){
                    return response()->json(["code" => 0, "msg" => "Veillez remplir le billetage svp!", "data" => NULL]);
                }
                //Recuperation du montant total du billetage
                $billetageContent = is_array($data["panierBillet"]) ? $data["panierBillet"] : array($data["panierBillet"]);
                $montantBilletage = 0;
                foreach($billetageContent as $index => $billetage) {
                    $montantBilletage = $montantBilletage + $data["panierBillet"][$index]["billets"]*$data["panierBillet"][$index]["quantite_billets"];
                }
                if($montantBilletage!=$request->get('solde_fermeture') && empty($data["motif_non_conformite"])){
                    return response()->json(["code" => 0, "msg" => "Le montant du billetage ne correspond pas au solde de la caisse!", "data" => NULL]);
                }
                
                //Si tout se passe bien
                foreach($billetageContent as $index => $billetage) {
                    $Billetage = new Billetage;
                    $Billetage->billet = $data["panierBillet"][$index]["billets"];
                    $Billetage->quantite = $data["panierBillet"][$index]["quantite_billets"];
                    $Billetage->caisse_ouverte_id = $caisseOuverte->id;
                    $Billetage->created_by = Auth::user()->id;
                    $Billetage->save();
                }
                //Mise à jour
                $caisse->ouvert = FALSE;
                $caisse->updated_by = Auth::user()->id;
                $caisse->save();
                
                //Mise à jour caisse ouverte 
                $caisseOuverte->solde_fermeture = $request->get('solde_fermeture');
                $caisseOuverte->date_fermeture = now();
                $caisseOuverte->updated_by = Auth::user()->id;
                $caisseOuverte->save();
                
                //Destruction de la session de caisse ouverte
                if($request->session()->has('session_caisse_ouverte')){
                    $request->session()->forget('session_caisse_ouverte');
                }
                $jsonData["data"] = json_decode($caisseOuverte);
                return response()->json($jsonData);
            } catch (Exception $exc) {
               $jsonData["code"] = -1;
               $jsonData["data"] = NULL;
               $jsonData["msg"] = $exc->getMessage();
               return response()->json($jsonData); 
            } 
        }
        return response()->json(["code" => 0, "msg" => "Echec de fermeture", "data" => NULL]);
    }
    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    
    //Etat
    
    public function raportCaissesPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->raportCaisses());
//        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_ventes_caisses.pdf');
    }
    public function raportCaisses(){
        $date_jour = date("Y-m-d");
        $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->Where('caisse_ouvertes.deleted_at', NULL)
                    ->whereDate('caisse_ouvertes.date_ouverture', $date_jour)
                    ->get();
        $outPut = $this->header();
        $outPut .= "<div class='container-table' font-size:12px;><h3 align='center'><u>Journal de mouvement des caisses d'aoujourd'hui</h3>
                    <table border='2' cellspacing='0' width='100%'>";
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="2" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">TICKET</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Rémise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Net à payer</th>
                        </tr>';
                $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.caisse_ouverte_id',$data->id]])
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
                $totalCiasse = 0;
            foreach($ventes as $vente){
                $totalCiasse = $totalCiasse + $vente->sommeTotale-$vente->sommeRemise;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$vente->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$vente->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($vente->sommeTotale, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($vente->sommeRemise, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($vente->sommeTotale-$vente->sommeRemise, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
         $outPut.= '<tr><td colspan="5" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
        $outPut .='</table> </div>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function raportCaisseByPeriodePdf($debut,$fin){
         $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->raportCaisseByPeriode($debut,$fin));
        return $pdf->stream('liste_ventes_caisses_periode.pdf');
    }
    public function raportCaisseByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = DB::table('caisse_ouvertes')
                    ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                    ->join('users','users.id','=','caisse_ouvertes.user_id')
                    ->select('caisse_ouvertes.*','caisses.libelle_caisse','users.full_name')
                    ->Where('caisse_ouvertes.deleted_at', NULL)
                    ->whereDate('caisse_ouvertes.date_ouverture','>=',$dateDebut)
                    ->whereDate('caisse_ouvertes.date_ouverture','<=', $dateFin)
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Journal du mouvement de caisse du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        
                    </div>';
        $grandTotal=0;
        foreach($datas as $data){
           $outPut .= '<tr>
                            <td  colspan="2" cellspacing="0" border="2" align="left">&nbsp; Caisse : <b>'.$data->libelle_caisse.'</b></td>
                            <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp; Caissier(e) : <b>'.$data->full_name.'</b></td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">TICKET</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Rémise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Net à payer</th>
                        </tr>';
                $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                    ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['ventes.caisse_ouverte_id',$data->id]])
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
                $totalCiasse = 0;
            foreach($ventes as $vente){
                $totalCiasse = $totalCiasse + $vente->sommeTotale-$vente->sommeRemise;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$vente->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$vente->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($vente->sommeTotale, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($vente->sommeRemise, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($vente->sommeTotale-$vente->sommeRemise, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
                        
            }
         $outPut.= '<tr><td colspan="5" cellspacing="0" border="2" align="left">&nbsp; Total <b>'.number_format($totalCiasse, 0, ',', ' ').'</b></td></tr>';
          $grandTotal = $grandTotal + $totalCiasse;
        }
        $outPut .='</table>';
        $outPut.='<br/> Somme totale : <b> '.number_format($grandTotal, 0, ',', ' ').' F CFA</b>';
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
