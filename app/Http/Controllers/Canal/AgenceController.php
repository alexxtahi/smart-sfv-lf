<?php

namespace App\Http\Controllers\Canal;

use App\Http\Controllers\Controller;
use App\Models\Canal\Agence;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $localites = DB::table('localites')->select('localites.*')->Where('deleted_at', NULL)->orderBy('libelle_localite', 'ASC')->get();
       $menuPrincipal = "Canal";
       $titleControlleur = "Agence";
       $btnModalAjout = "TRUE";
       return view('canal.agence.index',compact('localites', 'btnModalAjout', 'menuPrincipal', 'titleControlleur')); 
    }

    public function listeAgence()
    {
        $agences = Agence::with('localite')
                    ->Where('agences.deleted_at', NULL) 
                    ->select('agences.*')
                    ->orderBy('agences.libelle_agence', 'ASC')
                    ->get();
       $jsonData["rows"] = $agences->toArray();
       $jsonData["total"] = $agences->count();
       return response()->json($jsonData);
    }
    
    public function listeAgenceByLocalite($localite){
        $agences = Agence::with('localite')
                    ->Where([['agences.deleted_at', NULL],['agences.localite_id',$localite]]) 
                    ->select('agences.*')
                    ->orderBy('agences.libelle_agence', 'ASC')
                    ->get();
       $jsonData["rows"] = $agences->toArray();
       $jsonData["total"] = $agences->count();
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
        if ($request->isMethod('post') && $request->input('libelle_agence') && $request->input('numero_identifiant_agence')) {

                $data = $request->all(); 

            try {
                $Agence = Agence::where('numero_identifiant_agence', $data['numero_identifiant_agence'])->first();
                if($Agence!=null){
                    return response()->json(["code" => 0, "msg" => "Cet enregistrement existe déjà dans la base, vérifier le numéro identifiant", "data" => NULL]);
                }

                $agence = new Agence;
                $agence->libelle_agence = $data['libelle_agence'];
                $agence->numero_identifiant_agence = $data['numero_identifiant_agence'];
                $agence->contact_agence = $data['contact_agence'];
                $agence->localite_id = $data['localite_id'];
                $agence->adresse_agence = $data['adresse_agence'];
                $agence->nom_responsable = $data['nom_responsable'];
                $agence->contact_responsable = $data['contact_responsable'];
                $agence->email_agence = isset($data['email_agence']) && !empty($data['email_agence']) ? $data['email_agence'] : null;
                $agence->numero_cc = isset($data['numero_cc']) && !empty($data['numero_cc']) ? $data['numero_cc'] : null;
                $agence->created_by = Auth::user()->id;
                $agence->save();
                $jsonData["data"] = json_decode($agence);
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
     * @param  \App\Agence  $agence
     * @return Response
     */
    public function update(Request $request, Agence $agence)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($agence){
                    $data = $request->all(); 
            try {
                
                $agence->libelle_agence = $data['libelle_agence'];
                $agence->numero_identifiant_agence = $data['numero_identifiant_agence'];
                $agence->contact_agence = $data['contact_agence'];
                $agence->localite_id = $data['localite_id'];
                $agence->adresse_agence = $data['adresse_agence'];
                $agence->nom_responsable = $data['nom_responsable'];
                $agence->contact_responsable = $data['contact_responsable'];
                $agence->email_agence = isset($data['email_agence']) && !empty($data['email_agence']) ? $data['email_agence'] : null;
                $agence->numero_cc = isset($data['numero_cc']) && !empty($data['numero_cc']) ? $data['numero_cc'] : null;
                $agence->updated_by = Auth::user()->id;
                $agence->save();
                $jsonData["data"] = json_decode($agence);
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
     * @param  \App\Agence  $agence
     * @return Response
     */
    public function destroy(Agence $agence)
    {
       $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($agence){
                try {
               
                    $agence->update(['deleted_by' => Auth::user()->id]);
                    $agence->delete();
                    $jsonData["data"] = json_decode($agence);
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
    
    //Liste des agences
    public function listeAgencePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadHTML($this->listeAgences());
        return $pdf->stream('liste_agence.pdf');
    }
    public function listeAgences(){
        $datas = Agence::where('agences.deleted_at', NULL)
                    ->join('localites','localites.id','=','agences.localite_id')
                    ->select('agences.*','localites.libelle_localite')
                    ->orderBy('agences.libelle_agence', 'ASC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des agences</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° identifiant</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Agence</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Localité</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Adresse géo.</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Responsable</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Contact respon.</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_identifiant_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_localite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->adresse_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->nom_responsable.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact_responsable.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> Agence(s)';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Liste des agences par localité
     public function listeAgenceByLocalitePdf($localite){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeAgenceByLocalites($localite));
        $pdf->setPaper('A4', 'landscape');
        $infor_localite = \App\Models\Canal\Localite::find($localite);
        return $pdf->stream('liste_agence_de_localite_'.$infor_localite->libelle_localite.'.pdf');
    }
    public function listeAgenceByLocalites($localite){
        $infor_localite = \App\Models\Canal\Localite::find($localite);
        $datas = Agence::where([['agences.deleted_at', NULL],['agences.localite_id',$localite]]) 
                    ->join('localites','localites.id','=','agences.localite_id')
                    ->select('agences.*','localites.libelle_localite')
                    ->orderBy('agences.libelle_agence', 'ASC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des agences de la localité '.$infor_localite->libelle_localite.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                         <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">N° identifiant</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Agence</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Adresse géo.</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Responsable</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Contact respon.</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->numero_identifiant_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->adresse_agence.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->nom_responsable.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact_responsable.'</td>
                        </tr>';
       }
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> Agence(s)';
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
