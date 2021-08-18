<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\DetailInventaire;
use App\Models\Boutique\Inventaire;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
        $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
        $menuPrincipal = "Boutique";
        $titleControlleur = "Inventaire";
        $btnModalAjout = "TRUE";
        return view('boutique.inventaire.index',compact('depots','categories', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function listeInventaire()
    {
        $inventaires = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where('inventaires.deleted_at', NULL)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
       $jsonData["rows"] = $inventaires->toArray();
       $jsonData["total"] = $inventaires->count();
       return response()->json($jsonData);
    }
    
    public function listeInventaireByDate($dates){
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $inventaires = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where('inventaires.deleted_at', NULL)
                ->whereDate('inventaires.date_inventaire','=', $date)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
       $jsonData["rows"] = $inventaires->toArray();
       $jsonData["total"] = $inventaires->count();
       return response()->json($jsonData);
    }
    
    public function listeInventaireByDepot($depot){
        $inventaires = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where([['inventaires.deleted_at', NULL],['inventaires.depot_id',$depot]])
                ->orderBy('inventaires.id', 'DESC')
                ->get();
       $jsonData["rows"] = $inventaires->toArray();
       $jsonData["total"] = $inventaires->count();
       return response()->json($jsonData);
    }
    
    public function listeInventaireByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $inventaires = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where('inventaires.deleted_at', NULL)
                ->whereDate('inventaires.date_inventaire','>=',$date1)
                ->whereDate('inventaires.date_inventaire','<=', $date2)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
       $jsonData["rows"] = $inventaires->toArray();
       $jsonData["total"] = $inventaires->count();
       return response()->json($jsonData);
    }
    
    public function listeInventaireByDepotPeriode($depot,$debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
         $inventaires = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where([['inventaires.deleted_at', NULL],['inventaires.depot_id',$depot]])
                ->whereDate('inventaires.date_inventaire','>=',$date1)
                ->whereDate('inventaires.date_inventaire','<=', $date2)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
       $jsonData["rows"] = $inventaires->toArray();
       $jsonData["total"] = $inventaires->count();
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
        if ($request->isMethod('post') && $request->input('date_inventaire') && !empty($request->input('lotArticle'))) {

                $data = $request->all(); 

            try {
                if(empty($data['lotArticle'])){
                    return response()->json(["code" => 0, "msg" => "Vous n'avez pas ajouté d'articles à cet inventaire", "data" => NULL]);
                }
               
                $inventaire = new Inventaire;
                $inventaire->libelle_inventaire = $data['libelle_inventaire'];
                $inventaire->date_inventaire = Carbon::createFromFormat('d-m-Y', $data['date_inventaire']);
                $inventaire->depot_id = $data['depot_id'];
                $inventaire->created_by = Auth::user()->id;
                $inventaire->save();
                
                if($inventaire){
                    $lotArticle = is_array($data["lotArticle"]) ? $data["lotArticle"] : array($data["lotArticle"]);
                    
                    foreach ($lotArticle as $index => $article) {
                 
                        //Enregistrement de detail-inventaire
                            $detailInventaire = new DetailInventaire;
                            $detailInventaire->article_id = $data["lotArticle"][$index]["articles"];
                            $detailInventaire->inventaire_id = $inventaire->id;
                            $detailInventaire->unite_id =  $data["lotArticle"][$index]["unites"];
                            $detailInventaire->quantite_en_stocke = $data["lotArticle"][$index]["quantite_en_stocks"];
                            $detailInventaire->quantite_denombree = $data["lotArticle"][$index]["quantite_denombrees"];
                            $detailInventaire->date_peremption = Carbon::createFromFormat('d-m-Y', $data["lotArticle"][$index]["date_peremptions"]);
                            $detailInventaire->created_by = Auth::user()->id;
                            $detailInventaire->save();
                        
                        //Ajustement du stock
                            $DepotArticle = DepotArticle::where([['depot_id', $data['depot_id']],['article_id', $data["lotArticle"][$index]["articles"]],['unite_id', $data["lotArticle"][$index]["unites"]]])->first();
                            $DepotArticle->quantite_disponible = $data["lotArticle"][$index]["quantite_denombrees"];
                            $DepotArticle->date_peremption = Carbon::createFromFormat('d-m-Y', $data["lotArticle"][$index]["date_peremptions"]);
                            $DepotArticle->save();
                    }
                }
                
                $jsonData["data"] = json_decode($inventaire);
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
     * @param  \App\Inventaire  $inventaire
     * @return Response
     */
    public function updateInventaire(Request $request)
    {
        $inventaire = Inventaire::find($request->input('idInventaireModifier'));
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($inventaire){
            $data = $request->all(); 
            try {

                $inventaire->libelle_inventaire = $data['libelle_inventaire'];
                $inventaire->date_inventaire = Carbon::createFromFormat('d-m-Y', $data['date_inventaire']);
                $inventaire->updated_by = Auth::user()->id;
                $inventaire->save();
               
                $jsonData["data"] = json_decode($inventaire);
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
     * @param  \App\Inventaire  $inventaire
     * @return Response
     */
    public function destroy($id)
    {
        $inventaire = Inventaire::find($id);
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($inventaire){
                try {
                    
                //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                $detailInventaires = DetailInventaire::where('inventaire_id',$inventaire->id)->get();
               
                foreach($detailInventaires as $detailInventaire) {
                    $depot = DepotArticle::where([['article_id', $detailInventaire->article_id], ['depot_id', $inventaire->depot_id], ['unite_id', $detailInventaire->unite_id]])->first();
                    $depot->quantite_disponible = $detailInventaire->quantite_en_stocke;
                    $depot->save();
                }

                $inventaire->update(['deleted_by' => Auth::user()->id]);
                $inventaire->delete();
                $jsonData["data"] = json_decode($inventaire);
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
    //Etat
    public function ficheInventairePdf($inventaire){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->ficheInventaire($inventaire));
        $infos_inventaire = Inventaire::find($inventaire);
        return $pdf->stream($infos_inventaire->libelle_inventaire.'.pdf');
    }
    public function ficheInventaire($inventaire){
       $infos_inventaire = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where([['inventaires.deleted_at', NULL],['inventaires.id',$inventaire]])
                ->orderBy('inventaires.id', 'DESC')
                ->first();
        $datas = DetailInventaire::with('article','unite')
                ->select('detail_inventaires.*')
                ->Where([['detail_inventaires.deleted_at', NULL],['detail_inventaires.inventaire_id',$inventaire]])
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste inventaire du dépôt '.$infos_inventaire->depot->libelle_depot.' le '.$infos_inventaire->date_inventaires.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="40%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date péremption</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté en stock</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Qté dénombrée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Ecart</th>
                        </tr>
                    </div>';
       foreach ($datas as $data){
           
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->article->description_article.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2">&nbsp;'. date_format($data->date_peremption,'d-m-Y').'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->quantite_en_stocke, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->quantite_denombree, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->quantite_en_stocke-$data->quantite_denombree, 0, ',', ' ').'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
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
