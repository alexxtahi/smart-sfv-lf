<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleTransfert;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\MouvementStock;
use App\Models\Boutique\TransfertStock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\json_decode;

class TransfertStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $unites = DB::table('unites')->Where('deleted_at', NULL)->orderBy('libelle_unite', 'asc')->get();
       $menuPrincipal = "Boutique";
       $titleControlleur = "Transfert de stock";
       $btnModalAjout = "TRUE";
       return view('boutique.transfert-stock.index',compact('depots','unites', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeTransfertStock(){
        $transfert_stocks = TransfertStock::with('depot_depart','depot_arrivee')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where('transfert_stocks.deleted_at', NULL)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
        return response()->json($jsonData);
    }
    
    public function listeTransfertStockByDate($dates){
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $transfert_stocks = TransfertStock::with('depot_depart','depot_arrivee')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where('transfert_stocks.deleted_at', NULL)
                ->whereDate('transfert_stocks.date_transfert','=', $date)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
        return response()->json($jsonData);
    }
    
    public function listeTransfertStockByArticle($article){
        $transfert_stocks = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where([['transfert_stocks.deleted_at', NULL],['transfert_stocks.article_id',$article]])
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
        return response()->json($jsonData);
    }
    
    public function listeTransfertStockByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $transfert_stocks = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where('transfert_stocks.deleted_at', NULL)
                ->whereDate('transfert_stocks.date_transfert','>=', $dateDebut)
                ->whereDate('transfert_stocks.date_transfert','<=', $dateFin)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
        return response()->json($jsonData);
        
    }
    public function listeTransfertStockByPeriodeArticle($debut,$fin,$article){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $transfert_stocks = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where([['transfert_stocks.deleted_at', NULL],['transfert_stocks.article_id',$article]])
                ->whereDate('transfert_stocks.date_transfert','>=', $dateDebut)
                ->whereDate('transfert_stocks.date_transfert','<=', $dateFin)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $jsonData["rows"] = $transfert_stocks->toArray();
        $jsonData["total"] = $transfert_stocks->count();
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
        if ($request->isMethod('post') && $request->input('lotTransfert')) {
            $data = $request->all();
                if($data['depot_depart_id']==$data['depot_arrivee_id']){
                    return response()->json(["code" => 0, "msg" => "Impossible de faire du transfèrt dans le même dépôt", "data" => NULL]);
                }
            try {
            
                $transfertStock = new TransfertStock;
                $transfertStock->depot_depart_id = $data['depot_depart_id'];
                $transfertStock->depot_arrivee_id = $data['depot_arrivee_id'];
                $transfertStock->date_transfert = Carbon::createFromFormat('d-m-Y', $data['date_transfert']);
                $transfertStock->created_by = Auth::user()->id;
                $transfertStock->save();
                
                if($transfertStock!=null){
                    $lotTransfert = is_array($data["lotTransfert"]) ? $data["lotTransfert"] : array($data["lotTransfert"]);
                    foreach($lotTransfert as $index => $article) {
                         
                            //Enregistrement du transfert
                                $articleTransfert = new ArticleTransfert();
                                $articleTransfert->article_id = $data["lotTransfert"][$index]["articles"];
                                $articleTransfert->unite_depart = $data["lotTransfert"][$index]["unites"];
                                $articleTransfert->unite_reception = $data["lotTransfert"][$index]["unite_receptions"];
                                $articleTransfert->quantite_depart = $data["lotTransfert"][$index]["quantites"];
                                $articleTransfert->quantite_reception = $data["lotTransfert"][$index]["quantite_receptions"];
                                $articleTransfert->transfert_stock_id = $transfertStock->id;
                                $articleTransfert->created_by = Auth::user()->id;
                                $articleTransfert->save();

                            //Traitement sur le stock dans depot-article 
                            if($articleTransfert!=null){
                                $depotDpart = DepotArticle::where([['depot_id', $data['depot_depart_id']],['article_id', $data["lotTransfert"][$index]["articles"]], ['unite_id', $data["lotTransfert"][$index]["unites"]]])->first();
                                $depotArrive = DepotArticle::where([['depot_id', $data["depot_arrivee_id"]],['article_id', $data["lotTransfert"][$index]["articles"]], ['unite_id', $data["lotTransfert"][$index]["unite_receptions"]]])->first();
                                $mouvementStock = MouvementStock::where([['depot_id', $data['depot_depart_id']],['article_id', $data["lotTransfert"][$index]["articles"]],['unite_id', $data["lotTransfert"][$index]["unites"]]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_transfert']))->first();
                               
                                if(!$mouvementStock){
                                    $mouvementStock = new MouvementStock;
                                    $mouvementStock->date_mouvement = Carbon::createFromFormat('d-m-Y', $data['date_transfert']);
                                    $mouvementStock->depot_id = $data['depot_depart_id'];
                                    $mouvementStock->article_id = $data["lotTransfert"][$index]["articles"];
                                    $mouvementStock->unite_id = $data["lotTransfert"][$index]["unites"];
                                    $mouvementStock->quantite_initiale = $depotDpart != null ? $depotDpart->quantite_disponible : 0 ;
                                    $mouvementStock->created_by = Auth::user()->id;
                                }
                                
                                if(!$depotArrive){
                                    $depotArrive = new DepotArticle;
                                    $depotArrive->article_id = $data["lotTransfert"][$index]["articles"];
                                    $depotArrive->depot_id = $data["depot_arrivee_id"];
                                    $depotArrive->unite_id = $data["lotTransfert"][$index]["unite_receptions"];
                                }
                                
                                $depotDpart->quantite_disponible = $depotDpart->quantite_disponible - $data["lotTransfert"][$index]["quantites"];
                                $depotDpart->save();
                                $depotArrive->quantite_disponible = $depotArrive->quantite_disponible + $data["lotTransfert"][$index]["quantite_receptions"];
                                $depotArrive->save();
                                $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $data["lotTransfert"][$index]["quantites"];
                                $mouvementStock->save();
                            }
                                    
                    }
                }
                $jsonData["data"] = json_decode($transfertStock);
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
     * @param  \App\TransfertStock  $transfertStock
     * @return Response
     */
    public function updateTransfertStocks(Request $request)
    {   
        $transfertStock = TransfertStock::find($request->get('idTransfertStockModifier'));
         $jsonData = ["code" => 1, "msg" => "Enregistrement effectué avec succès."];
        if ($transfertStock) {
            $data = $request->all();
                if($data['depot_depart_id']==$data['depot_arrivee_id']){
                    return response()->json(["code" => 0, "msg" => "Impossible de faire du transfèrt dans le même dépôt", "data" => NULL]);
                }
            try {
                //S'il y a changement de depot lors de la modification
                $old_depot_depart_id = $transfertStock->depot_depart_id;
                $old_depot_arrivee_id = $transfertStock->depot_arrivee_id;
               
                if($transfertStock->depot_depart_id!=$data['depot_depart_id']){
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id',$transfertStock->id)->get();
                   
                    foreach($articleTransferts as $articleTransfert) {
                        $depotDepart = DepotArticle::where([['article_id',$articleTransfert->article_id],['depot_id',$old_depot_depart_id],['unite_id',$articleTransfert->unite_depart]])->first();
                        $depotDepart->quantite_disponible = $depotDepart->quantite_disponible + $articleTransfert->quantite_depart;
                        $depotDepart->save();   
                        $mouvementStock = MouvementStock::where([['depot_id', $old_depot_depart_id],['article_id', $articleTransfert->article_id],['unite_id', $articleTransfert->unite_depart]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();
                        $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee - $articleTransfert->quantite_depart;
                        $mouvementStock->save();
                    }
                }
              
                if($transfertStock->depot_arrivee_id!=$data['depot_arrivee_id']){
                     //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id',$transfertStock->id)->get();
                    foreach($articleTransferts as $articleTransfert) {
                        $depotArrive = DepotArticle::where([['article_id',$articleTransfert->article_id],['depot_id',$transfertStock->depot_arrivee_id],['unite_id',$articleTransfert->unite_reception]])->first();
                        $depotArrive->quantite_disponible = $depotArrive->quantite_disponible - $articleTransfert->quantite_reception;
                        $depotArrive->save();
                    }
                }
                
                $transfertStock->depot_depart_id = $data['depot_depart_id'];
                $transfertStock->depot_arrivee_id = $data['depot_arrivee_id'];
                $transfertStock->date_transfert = Carbon::createFromFormat('d-m-Y', $data['date_transfert']);
                $transfertStock->updated_by = Auth::user()->id;
                $transfertStock->save();
               
                //S'il y a changement de depot lors de la modification
                if($old_depot_depart_id!=$data['depot_depart_id']){
                    //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id',$transfertStock->id)->get();
                    foreach($articleTransferts as $articleTransfert) {
                        $depotDepart = DepotArticle::where([['article_id',$articleTransfert->article_id],['depot_id',$data['depot_depart_id']],['unite_id',$articleTransfert->unite_depart]])->first();
                        $depotDepart->quantite_disponible = $depotDepart->quantite_disponible - $articleTransfert->quantite_depart;
                        $depotDepart->save();
                        $mouvementStock = MouvementStock::where([['depot_id', $data['depot_depart_id']],['article_id', $articleTransfert->article_id],['unite_id', $articleTransfert->unite_depart]])->whereDate('date_mouvement', Carbon::createFromFormat('d-m-Y', $data['date_transfert']))->first();
                        $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee + $articleTransfert->quantite_depart;
                        $mouvementStock->save();
                    }
                }
              
                if($old_depot_arrivee_id!=$data['depot_arrivee_id']){
                     //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                    $articleTransferts = ArticleTransfert::where('transfert_stock_id',$transfertStock->id)->get();
                    foreach($articleTransferts as $articleTransfert) {
                        $depotArrive = DepotArticle::where([['article_id',$articleTransfert->article_id],['depot_id',$data['depot_arrivee_id']],['unite_id',$articleTransfert->unite_reception]])->first();
                        $depotArrive->quantite_disponible = $depotArrive->quantite_disponible + $articleTransfert->quantite_reception;
                        $depotArrive->save();
                    }
                }
                $jsonData["data"] = json_decode($transfertStock);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\TransfertStock  $transfertStock
     * @return Response
     */
    public function destroy(TransfertStock $transfertStock)
    {
       $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($transfertStock){
                try {
                //Récuperation des anciens articles pour les mettre a leur place dans Depot-Article
                $articleTransferts = ArticleTransfert::where('transfert_stock_id',$transfertStock->id)->get();
                foreach($articleTransferts as $articleTransfert) {
                    $depotDepart = DepotArticle::where([['article_id',$articleTransfert->article_id],['depot_id',$transfertStock->depot_depart_id],['unite_id',$articleTransfert->unite_depart]])->first();
                    $depotDepart->quantite_disponible = $depotDepart->quantite_disponible + $articleTransfert->quantite_depart;
                    $depotDepart->save();
                    
                    $depotArrive = DepotArticle::where([['article_id',$articleTransfert->article_id],['depot_id',$transfertStock->depot_arrivee_id],['unite_id',$articleTransfert->unite_reception]])->first();
                    $depotArrive->quantite_disponible = $depotArrive->quantite_disponible - $articleTransfert->quantite_reception;
                    $depotArrive->save();
                    
                    $mouvementStock = MouvementStock::where([['depot_id', $transfertStock->depot_depart_id],['article_id', $articleTransfert->article_id],['unite_id', $articleTransfert->unite_depart]])->whereDate('date_mouvement', $transfertStock->date_transfert)->first();
                    $mouvementStock->quantite_transferee = $mouvementStock->quantite_transferee - $articleTransfert->quantite_depart;
                    $mouvementStock->save();
                }
                
                $transfertStock->update(['deleted_by' => Auth::user()->id]);
                $transfertStock->delete();
                $jsonData["data"] = json_decode($transfertStock);
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
    public function transfertStockPdf($transfertStock){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->transfertStock($transfertStock));
        return $pdf->stream('Fiche_transfert_stock.pdf');
    }

    public function transfertStock($transfertStock){
        $outPut = $this->header($transfertStock);
        $outPut.= $this->content($transfertStock);
        $outPut.= $this->footer();
        return $outPut;
    }

    public function content($transfertStock){
         $elements = ArticleTransfert::where('article_transferts.deleted_at', NULL)
                            ->join('articles','articles.id','=','article_transferts.article_id')
                            ->join('unites as uniteDepart','uniteDepart.id','=','article_transferts.unite_depart')
                            ->join('unites as uniteArrive','uniteArrive.id','=','article_transferts.unite_reception')
                            ->select('article_transferts.*','articles.description_article','articles.code_barre','uniteDepart.libelle_unite as unite_depart','uniteArrive.libelle_unite as unite_arrivee')
                            ->Where([['article_transferts.transfert_stock_id',$transfertStock]])
                            ->get();

        $content = '<div class="container-table">
                        <table border="1" cellspacing="-1" width="100%">
                            <tr>
                                <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                                <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Colis</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Qté transférée</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Colis reception</th>
                                <th cellspacing="0" border="2" width="15%" align="center">Qté reception</th>
                            </tr>';
      
        foreach($elements as $element){
            $content.='<tr>
                            <td style="font-size:13px;"  cellspacing="0" border="2">&nbsp;&nbsp;'.$element->code_barre.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2">&nbsp;&nbsp;'.$element->description_article.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">'.$element->unite_depart.'</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">'.$element->quantite_depart.'&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">'.$element->unite_arrivee.'&nbsp;&nbsp;&nbsp;</td>
                            <td style="font-size:13px;"  cellspacing="0" border="2" align="center">'.$element->quantite_reception.'&nbsp;&nbsp;&nbsp;</td>
                       </tr>';
        }
        
       return $content;
    }
    
    public function header($transfertStock){
        $infosTransfertStocks = TransfertStock::with('depot_depart','depot_arrivee')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where([['transfert_stocks.deleted_at', NULL],['transfert_stocks.id',$transfertStock]])
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->first();

        $header = '<html>
                         <head>
                            <meta charset="utf-8">
                            <title></title>
                                    <style>
                                        .container-table{        
                                            margin:130px 0;
                                            width: 100%;
                                        }
                                        .container{
                                            width: 100%;
                                            margin: 2px 5px;
                                            font-size:15px;
                                        }
                                        .fixed-header-left{
                                            width: 34%;
                                            height:4%;
                                            position: absolute; 
                                            line-height:1;
                                            font-size:13px;
                                            top: 0;
                                        }
                                        .fixed-header-right{
                                            width: 40%;
                                            height:6%;
                                            float: right;
                                            position: absolute;
                                            top: 0;
                                            background: #fff;
                                            padding: 10px 0;
                                            color: #333;
                                            border: 1px #333 solid;
                                            border-radius: 3px;
                                        }
                                        .fixed-header-center{
                                            width:35%;
                                            height:7%;
                                            margin: 0 150px;
                                            top: 0;
                                            text-align:center;
                                            position: absolute; 
                                        }
                                        .fixed-footer{
                                            position: fixed; 
                                            bottom: -28; 
                                            left: 0px; 
                                            right: 0px;
                                            height: 80px; 
                                            text-align:center;
                                        }     
                                        .titre-style{
                                         text-align:center;
                                         text-decoration: underline;
                                        }
                                    footer{
                                    font-size:13px;
                                    position: absolute; 
                                    bottom: -35px; 
                                    left: 0px; 
                                    right: 0px;
                                    height: 80px; 
                                    text-align:center;
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
                <body style="margin-bottom:0; margin-top:0px;">
                <div class="fixed-header-left">
                    <div class="container">
                         <img src='.$this->infosConfig()->logo.' width="200" height="160"/> 
                    </div>
                </div>
                <div class="fixed-header-center">
                    <div class="container">
                       Fiche de transfert de stock
                    </div>
                </div>
                <div class="fixed-header-right">
                    <div class="container">
                       Du dépôt : <b>'.$infosTransfertStocks->depot_depart->libelle_depot.'</b><br/>
                       Au dépôt : <b>'.$infosTransfertStocks->depot_arrivee->libelle_depot.'</b><br/>
                       Date : <b>'.$infosTransfertStocks->date_transferts.'</b>
                    </div>
                </div>';     
        return $header;
    }
    //Footer fiche
    public function footer(){
        $footer ="<div class='fixed-footer'>
                        <div class='page-number'></div>
                    </div>
            </body>
        </html>";
        return $footer;
    }
}
