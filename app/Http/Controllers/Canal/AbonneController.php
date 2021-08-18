<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Abonne;
use App\Models\Canal\Localite;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function view;

class AbonneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $localites = DB::table('localites')->select('localites.*')->Where('deleted_at', NULL)->orderBy('libelle_localite', 'ASC')->get();
       $nations = DB::table('nations')->select('nations.*')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'ASC')->get();
       $type_pieces = DB::table('type_pieces')->select('type_pieces.*')->Where('deleted_at', NULL)->orderBy('libelle_type_piece', 'ASC')->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Abonnés";
       (Auth::user()->role != 'Agence') ? $btnModalAjout = "FALSE" : $btnModalAjout = "TRUE";
       return view('canal.abonne.index',compact('localites','nations','type_pieces','btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeAbonne()
    {
//        if(Auth::user()->role=='Agence'){
////            $abonnes = Abonne::with('localite','nation','type_piece')
////                    ->Where([['abonnes.deleted_at', NULL],['abonnes.localite_id',Auth::user()->localite_id]]) 
////                    ->select('abonnes.*',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
////                    ->orderBy('abonnes.full_name_abonne', 'ASC')
////                    ->get();
//        }else{
            $abonnes = Abonne::with('localite','nation','type_piece')
                    ->Where('abonnes.deleted_at', NULL) 
                    ->select('abonnes.*',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
                    ->orderBy('abonnes.full_name_abonne', 'ASC')
                    ->get();
//        }
       $jsonData["rows"] = $abonnes->toArray();
       $jsonData["total"] = $abonnes->count();
       return response()->json($jsonData);
    }
    public function listeAbonneByName($name)
    {
//        if(Auth::user()->role=='Agence'){
//            $abonnes = Abonne::with('localite','nation','type_piece')
//                    ->Where([['abonnes.deleted_at', NULL],['abonnes.localite_id',Auth::user()->localite_id],['abonnes.full_name_abonne','like','%'.$name.'%']]) 
//                    ->select('abonnes.*',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
//                    ->orderBy('abonnes.full_name_abonne', 'ASC')
//                    ->get();   
//        }else{
            $abonnes = Abonne::with('localite','nation','type_piece')
                    ->Where([['abonnes.deleted_at', NULL],['abonnes.full_name_abonne','like','%'.$name.'%']]) 
                    ->select('abonnes.*',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
                    ->orderBy('abonnes.full_name_abonne', 'ASC')
                    ->get();
//        }
       $jsonData["rows"] = $abonnes->toArray();
       $jsonData["total"] = $abonnes->count();
       return response()->json($jsonData);
    }
    
    public function listeAbonneByLocalite($localite)
    {
        $abonnes = Abonne::with('localite','nation','type_piece')
                    ->Where([['abonnes.deleted_at', NULL],['abonnes.localite_id',$localite]]) 
                    ->select('abonnes.*',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
                    ->orderBy('abonnes.full_name_abonne', 'ASC')
                    ->get();
       $jsonData["rows"] = $abonnes->toArray();
       $jsonData["total"] = $abonnes->count();
       return response()->json($jsonData);
    }
    
    public function getInfosAbonne($id){
        $abonnes = Abonne::where([['abonnes.deleted_at', NULL],['abonnes.id',$id]]) 
                    ->select('abonnes.contact1',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
                    ->get();
       $jsonData["rows"] = $abonnes->toArray();
       $jsonData["total"] = $abonnes->count();
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
        if ($request->isMethod('post') && $request->input('full_name_abonne')) {
            $data = $request->all(); 
            try {
               
                $abonne = new Abonne;
                $abonne->civilite = $data['civilite'];
                $abonne->full_name_abonne = $data['full_name_abonne'];
                $abonne->date_naissance_abonne = Carbon::createFromFormat('d-m-Y', $data['date_naissance_abonne']);
                $abonne->adresse_abonne = $data['adresse_abonne'];
                $abonne->contact1 = $data['contact1'];
                $abonne->localite_id = $data['localite_id'];
                $abonne->nation_id = isset($data['nation_id']) && !empty($data['nation_id']) ? $data['nation_id'] : null;
                $abonne->type_piece_id = isset($data['type_piece_id']) && !empty($data['type_piece_id']) ? $data['type_piece_id'] : null;
                $abonne->numero_piece = isset($data['numero_piece']) && !empty($data['numero_piece']) ? $data['numero_piece'] : null;
                $abonne->code_postal = isset($data['code_postal']) && !empty($data['code_postal']) ? $data['code_postal'] : null;
                $abonne->email_abonne = isset($data['email_abonne']) && !empty($data['email_abonne']) ? $data['email_abonne'] : null;
                $abonne->contact2 = isset($data['contact2']) && !empty($data['contact2']) ? $data['contact2'] : null;
                $abonne->contact_conjoint = isset($data['contact_conjoint']) && !empty($data['contact_conjoint']) ? $data['contact_conjoint'] : null;
                $abonne->created_by = Auth::user()->id;
                $abonne->save();
                $jsonData["data"] = json_decode($abonne);
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
     * @param  \App\Abonne  $abonne
     * @return Response
     */
    public function update(Request $request, Abonne $abonne)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $data = $request->all(); 
        
        if($abonne){
            try {
                
                $abonne->civilite = $data['civilite'];
                $abonne->full_name_abonne = $data['full_name_abonne'];
                $abonne->date_naissance_abonne = Carbon::createFromFormat('d-m-Y', $data['date_naissance_abonne']);
                $abonne->adresse_abonne = $data['adresse_abonne'];
                $abonne->contact1 = $data['contact1'];
                $abonne->localite_id = $data['localite_id'];
                $abonne->nation_id = isset($data['nation_id']) && !empty($data['nation_id']) ? $data['nation_id'] : null;
                $abonne->type_piece_id = isset($data['type_piece_id']) && !empty($data['type_piece_id']) ? $data['type_piece_id'] : null;
                $abonne->numero_piece = isset($data['numero_piece']) && !empty($data['numero_piece']) ? $data['numero_piece'] : null;
                $abonne->code_postal = isset($data['code_postal']) && !empty($data['code_postal']) ? $data['code_postal'] : null;
                $abonne->email_abonne = isset($data['email_abonne']) && !empty($data['email_abonne']) ? $data['email_abonne'] : null;
                $abonne->contact2 = isset($data['contact2']) && !empty($data['contact2']) ? $data['contact2'] : null;
                $abonne->contact_conjoint = isset($data['contact_conjoint']) && !empty($data['contact_conjoint']) ? $data['contact_conjoint'] : null;
                $abonne->created_by = Auth::user()->id;
                $abonne->updated_by = Auth::user()->id;
                $abonne->save();
           
                $jsonData["data"] = json_decode($abonne);
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
     * @param  \App\Abonne  $abonne
     * @return Response
     */
    public function destroy(Abonne $abonne)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($abonne){
                try {
               
                $abonne->update(['deleted_by' => Auth::user()->id]);
                $abonne->delete();
                $jsonData["data"] = json_decode($abonne);
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
    
    //Liste des abonnés
    public function listeAbonnePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeAbonnes());
        return $pdf->stream('liste_abonnes.pdf');
    }
    public function listeAbonnes(){
         $datas = Abonne::where('abonnes.deleted_at', NULL)
                    ->join('localites','localites.id','=','abonnes.localite_id') 
                    ->select('abonnes.*','localites.libelle_localite',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
                    ->orderBy('abonnes.full_name_abonne', 'ASC')
                    ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des abonnés</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="35%" align="center">Nom complet</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date naissance</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>';
                            if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                         $outPut .='<th cellspacing="0" border="2" width="25%" align="center">Localité</th>';
                            }
                         $outPut .='<th cellspacing="0" border="2" width="30%" align="center">Adresse</th>
                            <th cellspacing="0" border="2" width="25%" align="center">N° pièce</th>
                        </tr>
                    </div>';
        $total = 0;  
       foreach ($datas as $data){
           $total = $total + 1; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->full_name_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_naissance_abonnes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact1.'</td>';
                        if(Auth::user()->role == "Concepteur" or Auth::user()->role == "Administrateur" or Auth::user()->role == "Gerant"){
                            $outPut .='<td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_localite.'</td>';
                        }    
                         $outPut .='<td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->adresse_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_piece.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' Abonné(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
     }
     
    //Liste des abonnés par localité
    public function listeAbonneByLocalitePdf($localite){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeAbonneByLocalites($localite));
        $inofo_localite= Localite::find($localite);
        return $pdf->stream('liste_abonnes_de_localite_'.$inofo_localite->libelle_localite.'.pdf');
    }
    public function listeAbonneByLocalites($localite){
        $inofo_localite= Localite::find($localite);
         $datas = Abonne::where([['abonnes.deleted_at', NULL],['abonnes.localite_id',$localite]])
                    ->select('abonnes.*',DB::raw('DATE_FORMAT(abonnes.date_naissance_abonne, "%d-%m-%Y") as date_naissance_abonnes'))
                    ->orderBy('abonnes.full_name_abonne', 'ASC')
                    ->get();
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des abonnés de la localité '.$inofo_localite->libelle_localite.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="35%" align="center">Nom complet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Date naissance</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Adresse</th>
                            <th cellspacing="0" border="2" width="30%" align="center">N° pièce</th>
                        </tr>
                    </div>';
        $total = 0;  
       foreach ($datas as $data){
           $total = $total + 1; 
           
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->civilite.'. '.$data->full_name_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_naissance_abonnes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact1.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->adresse_abonne.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_piece.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Au total :<b> '.number_format($total, 0, ',', ' ').' Abonné(s)</b>';
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
