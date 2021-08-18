<?php

namespace App\Http\Controllers\Parametre;

use App\Http\Controllers\Controller;
use App\Models\Boutique\ArticleVente;
use App\Models\Boutique\Reglement;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $regimes = DB::table('regimes')->Where('deleted_at', NULL)->orderBy('libelle_regime', 'asc')->get();
       $menuPrincipal = "Paramètre";
       $titleControlleur = "Client";
       $btnModalAjout = "TRUE";
       return view('parametre.client.index',compact('nations','regimes','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function ficheClient($client){
        $infoClient = Client::find($client);
        $menuPrincipal = "Fiche client";
        $titleControlleur = "";
        $btnModalAjout = "FALSE";
        return view('parametre.client.fiche',compact('infoClient','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function listeClient()
    {
        $clients = Client::with('nation','regime')
                ->select('clients.*')
                ->Where('deleted_at', NULL)
                ->orderBy('clients.id', 'ASC')
                ->get();
       $jsonData["rows"] = $clients->toArray();
       $jsonData["total"] = $clients->count();
       return response()->json($jsonData);
    }
    
    public function listeClientByNation($nation){
        $clients = Client::with('nation','regime')
                ->select('clients.*')
                ->Where([['deleted_at', NULL],['clients.nation_id',$nation]])
                ->orderBy('full_name_client', 'ASC')
                ->get();
       $jsonData["rows"] = $clients->toArray();
       $jsonData["total"] = $clients->count();
       return response()->json($jsonData);
    }
    
    public function findClientById($id){
        $client = Client::with('nation','regime')
                ->select('clients.contact_client','clients.plafond_client')
                ->Where([['deleted_at', NULL],['clients.id',$id]])
                ->get();
       $jsonData["rows"] = $client->toArray();
       $jsonData["total"] = $client->count();
       return response()->json($jsonData);
    }
    
    public function getAllDoitClient($client){
        
        $ventes = Vente::where([['ventes.deleted_at', NULL],['ventes.proformat',0]])
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.acompte_facture',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                ->Where('ventes.client_id',$client)
                ->groupBy('article_ventes.vente_id')
                ->get();
       
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function findClientByVente($vente){
        $client = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                ->select('ventes.*','ventes.id as id_vente',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.id',$vente]])
                ->groupBy('ventes.id')
                ->get();
        $jsonData["rows"] = $client->toArray();
        $jsonData["total"] = $client->count();
        return response()->json($jsonData);
    }
    public function lastClient(){
        $clients = Client::with('nation','regime')
                ->select('clients.*')
                ->Where('deleted_at', NULL)
                ->orderBy('clients.id', 'DESC')
                ->take(1)->get();
       $jsonData["rows"] = $clients->toArray();
       $jsonData["total"] = $clients->count();
       return response()->json($jsonData);
    }
    
    public function listeAchatsClient($client){
        $totalAchat=0; $totalAcompte=0; $totalRemise=0;
        $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        foreach ($ventes as $vente){
            if($vente->proformat==0){
                $totalAchat = $totalAchat + $vente->sommeTotale;
                $totalAcompte = $totalAcompte + $vente->acompte_facture;
                $totalRemise = $totalRemise + $vente->sommeRemise;
            }
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalAcompte"] = $totalAcompte;
        $jsonData["totalRemise"] = $totalRemise;
        return response()->json($jsonData);
    }
    public function listeAchatsClientByPeriode($debut,$fin,$client){
        $totalAchat=0; $totalAcompte=0; $totalRemise=0;
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->whereDate('ventes.date_vente','>=',$date1)
                ->whereDate('ventes.date_vente','<=', $date2)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        foreach ($ventes as $vente){
            if($vente->proformat==0){
                $totalAchat = $totalAchat + $vente->sommeTotale;
                $totalAcompte = $totalAcompte + $vente->acompte_facture;
                $totalRemise = $totalRemise + $vente->sommeRemise;
            }
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalAcompte"] = $totalAcompte;
        $jsonData["totalRemise"] = $totalRemise;
        return response()->json($jsonData);
    }
    public function listeAchatsClientByFacture($facture,$client){
        $totalAchat=0; $totalAcompte=0; $totalRemise=0;
        $ventes = Vente::with('client','depot')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.numero_facture', 'like', '%' . $facture . '%']])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        foreach ($ventes as $vente){
            if($vente->proformat==0){
                $totalAchat = $totalAchat + $vente->sommeTotale;
                $totalAcompte = $totalAcompte + $vente->acompte_facture;
                $totalRemise = $totalRemise + $vente->sommeRemise;
            }
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalAcompte"] = $totalAcompte;
        $jsonData["totalRemise"] = $totalRemise;
        return response()->json($jsonData);
    }
    public function listeReglementsClient($client){
        $reglements = Reglement::with('moyen_reglement')
                ->join('ventes','ventes.id','=','reglements.vente_id')
                ->join('clients','clients.id','=','ventes.client_id')
                ->select('reglements.*','ventes.client_id as id_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id',$client]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeReglementsClientByPeriode($debut,$fin,$client){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $reglements = Reglement::with('moyen_reglement')
                ->join('ventes','ventes.id','=','reglements.vente_id')
                ->join('clients','clients.id','=','ventes.client_id')
                ->select('reglements.*','ventes.client_id as id_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id',$client]])
                ->whereDate('reglements.date_reglement','>=',$date1)
                ->whereDate('reglements.date_reglement','<=', $date2)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeArticlePlusAchatsClient($client){
        $ventes = Vente::where([['ventes.deleted_at',null],['ventes.client_id',$client],['ventes.proformat',0]])
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->join('articles','articles.id','=','article_ventes.article_id')
                ->select('articles.description_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.quantite) as qteTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as remiseTotale'))
                ->groupBy('article_ventes.article_id')
                ->orderBy('qteTotale','DESC')
                ->take(10)->get();
  
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
        if ($request->isMethod('post') && $request->input('full_name_client')) {

                $data = $request->all(); 

            try {
               
                $client = new Client; 
                
                //Création code du client
                $maxIdTable = DB::table('clients')->max('id');
                $idClient = $maxIdTable + 1;
                $caractere_speciaux = array("'","-"," ");
                $code_client = '411'.substr(strtoupper(str_replace($caractere_speciaux,'', $data['full_name_client'])), 0, 3).$idClient;
                
                $client->code_client = $code_client;
                $client->full_name_client = $data['full_name_client'];
                $client->contact_client = $data['contact_client'];
                $client->nation_id = $data['nation_id'];
                $client->regime_id = $data['regime_id'];
                $client->email_client = isset($data['email_client']) && !empty($data['email_client']) ? $data['email_client'] : null;
                $client->plafond_client = isset($data['plafond_client']) && !empty($data['plafond_client']) ? $data['plafond_client'] : 0;
                $client->compte_contribuable_client = isset($data['compte_contribuable_client']) && !empty($data['compte_contribuable_client']) ? $data['compte_contribuable_client'] : null;
                $client->boite_postale_client = isset($data['boite_postale_client']) && !empty($data['boite_postale_client']) ? $data['boite_postale_client'] : null;
                $client->adresse_client = isset($data['adresse_client']) && !empty($data['adresse_client']) ? $data['adresse_client'] : null;
                $client->fax_client = isset($data['fax_client']) && !empty($data['fax_client']) ? $data['fax_client'] : null;
                $client->created_by = Auth::user()->id;
                $client->save();
                $jsonData["data"] = json_decode($client);
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
     * @param  \App\Client  $client
     * @return Response
     */
    public function update(Request $request, Client $client)
    {
         $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        
        if($client){
            $data = $request->all(); 
            try {
               
                $client->full_name_client = $data['full_name_client'];
                $client->contact_client = $data['contact_client'];
                $client->nation_id = $data['nation_id'];
                $client->regime_id = $data['regime_id'];
                $client->plafond_client = isset($data['plafond_client']) && !empty($data['plafond_client']) ? $data['plafond_client'] : 0;
                $client->compte_contribuable_client = isset($data['compte_contribuable_client']) && !empty($data['compte_contribuable_client']) ? $data['compte_contribuable_client'] : null;
                $client->email_client = isset($data['email_client']) && !empty($data['email_client']) ? $data['email_client'] : null;
                $client->boite_postale_client = isset($data['boite_postale_client']) && !empty($data['boite_postale_client']) ? $data['boite_postale_client'] : null;
                $client->adresse_client = isset($data['adresse_client']) && !empty($data['adresse_client']) ? $data['adresse_client'] : null;
                $client->fax_client = isset($data['fax_client']) && !empty($data['fax_client']) ? $data['fax_client'] : null;
                $client->updated_by = Auth::user()->id;
                $client->save();
                
                $jsonData["data"] = json_decode($client);
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
     * @param  \App\Client  $client
     * @return Response
     */
    public function destroy(Client $client)
    {
         $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($client){
                try {
               
                $client->update(['deleted_by' => Auth::user()->id]);
                $client->delete();
                $jsonData["data"] = json_decode($client);
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
    
    //Etat liste des achats du client
    public function listeAchatsClientPdf($client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeAchatsClients($client));
        return $pdf->stream('liste_achats_client.pdf');
    }
    public function listeAchatsClients($client){
        $infoClient = Client::find($client);
        $datas = Vente::where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->leftjoin('depots','depots.id','=','ventes.depot_id')
                ->select('ventes.*','ventes.id as idVente','depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste de tous les achats du client '.$infoClient->full_name_client.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th colspan="1" cellspacing="0" border="2" width="17%" align="center">Montant TTC</th>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Remise</th>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Acompte</th>
                            <th colspan="2" cellspacing="0" border="2" width="15%" align="center">Reste</th>
                        </tr>';
        $totalAchat = 0;$totalAcompte=0;$totalRemise=0;
        foreach ($datas as $data){
            if($data->proformat==0){
                $totalAchat = $totalAchat + $data->sommeTotale;
                $totalAcompte = $totalAcompte + $data->acompte_facture;
                $totalRemise = $totalRemise + $data->sommeRemise;
            }
            $data->proformat==0 ? $numero_facture = $data->numero_facture : $numero_facture = "Proforma";
         
           $outPut .= '<tr>
                            <td  colspan="1" cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_ventes.'</td>
                            <td  colspan="1" cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$numero_facture.'</td>
                            <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($data->sommeRemise, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($data->acompte_facture, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  colspan="2" cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale-$data->acompte_facture, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
           if($data->libelle_depot!=null){
               $articlesDiversVentes = ArticleVente::with('article','unite')
                                                    ->join('articles','articles.id','=','article_ventes.article_id')
                                                    ->join('unites','unites.id','=','article_ventes.unite_id')
                                                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                                    ->select('article_ventes.*','unites.libelle_unite','articles.description_article', 'param_tvas.montant_tva')
                                                    ->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne', 0],['article_ventes.vente_id',$data->idVente]])
                                                    ->get();
                $outPut .='<tr><td colspan="7" align="center" cellspacing="0" border="2" align="left"><b>Détails</b></td></tr>
                            <tr>
                                <th cellspacing="0" border="2" align="left">Article</th>
                                <th cellspacing="0" border="2" align="left">Colis </th>
                                <th cellspacing="0" border="2" align="left">Prix HT</th>
                                <th cellspacing="0" border="2" align="left">Prix TTC</th>
                                <th cellspacing="0" border="2" align="left">Quantité</th>
                                <th cellspacing="0" border="2" align="left">Montant TTC</th>
                                <th cellspacing="0" border="2" align="left">Remise</th>
                            </tr>';
                foreach ($articlesDiversVentes as $article){
                    $article->montant_tva!=null ? $montantHT = round($article->prix/($article->montant_tva + 1), 0) : $montantHT = $article->prix;
                            
                    $outPut .= '<tr>
                                    <td cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$article->description_article.'</td>
                                    <td cellspacing="0" border="2" align="right">'.$article->libelle_unite.'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($montantHT, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($article->prix, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.$article->quantite.'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($article->prix*$article->quantite, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                </tr>';
                }
                $outPut .='<tr>
                            <td colspan="4" cellspacing="0" border="2" align="left">&nbsp;&nbsp;<b>Total TTC</b></td>
                            <td colspan="3" cellspacing="0" border="2" align="right"><b>'.$data->sommeTotale.'&nbsp;&nbsp;</b></td>
                          </tr>';
           }else{
                $articlesDiversVentes =  ArticleVente::with('divers')
                                                    ->join('divers','divers.id','=','article_ventes.divers_id')
                                                    ->Where([['article_ventes.deleted_at', NULL],['article_ventes.vente_id',$data->idVente]])
                                                    ->select('article_ventes.*','divers.libelle_divers')
                                                    ->get();
                $outPut .='<tr><td colspan="7" align="center" cellspacing="0" border="2" align="left"><b>Détails</b></td></tr>
                            <tr>
                                <th colspan="3" cellspacing="0" border="2" align="left">Libellé</th>
                                <th colspan="1" cellspacing="0" border="2" align="left">Quantité </th>
                                <th colspan="1" cellspacing="0" border="2" align="left">Prix unitaire</th>
                                <th colspan="2" cellspacing="0" border="2" align="left">Montant</th>
                            </tr>';
                foreach ($articlesDiversVentes as $article){
                    $outPut .= '<tr>
                                    <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$article->libelle_divers.'</td>
                                    <td  colspan="1" cellspacing="0" border="2" align="right">'.$article->quantite.'&nbsp;&nbsp;</td>
                                    <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($article->prix, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td  colspan="2" cellspacing="0" border="2" align="right">'.number_format($article->prix*$article->quantite, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                </tr>';
                }
                $outPut .='<tr>
                            <td colspan="4" cellspacing="0" border="2" align="left">&nbsp;&nbsp;<b>Total TTC</b></td>
                            <td colspan="3" cellspacing="0" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</b></td>
                          </tr>';
           }
            
        }
        $outPut .='</table></div>';
        $outPut.='TOTAL ACHAT :<b> '.number_format($totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='TOTAL REMISE :<b> '.number_format($totalRemise, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='TOTAL ACOMPTE :<b> '.number_format($totalAcompte, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='TOTAL RESTE :<b> '.number_format($totalAchat-$totalAcompte, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function listeAchatsClientByPeriodePdf($debut,$fin,$client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeAchatsClientByPeriodes($debut,$fin,$client));
        return $pdf->stream('liste_achats_client_du_'.$debut.'_au_'.$fin.'_.pdf');
    }
    public function listeAchatsClientByPeriodes($debut,$fin,$client){
        $infoClient = Client::find($client);
         $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Vente::where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->leftjoin('depots','depots.id','=','ventes.depot_id')
                ->select('ventes.*','ventes.id as idVente','depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as sommeRemise'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->whereDate('ventes.date_vente','>=',$date1)
                ->whereDate('ventes.date_vente','<=', $date2)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste de tous les achats du client '.$infoClient->full_name_client.' sur la période du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th colspan="1" cellspacing="0" border="2" width="17%" align="center">Montant TTC</th>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Remise</th>
                            <th colspan="1" cellspacing="0" border="2" width="15%" align="center">Acompte</th>
                            <th colspan="2" cellspacing="0" border="2" width="15%" align="center">Reste</th>
                        </tr>';
        $totalAchat = 0;$totalAcompte=0;$totalRemise=0;
        foreach ($datas as $data){
            if($data->proformat==0){
                $totalAchat = $totalAchat + $data->sommeTotale;
                $totalAcompte = $totalAcompte + $data->acompte_facture;
                $totalRemise = $totalRemise + $data->sommeRemise;
            }
            $data->proformat==0 ? $numero_facture = $data->numero_facture : $numero_facture = "Proforma";
           $outPut .= '<tr>
                            <td  colspan="1" cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_ventes.'</td>
                            <td  colspan="1" cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$numero_facture.'</td>
                            <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($data->sommeRemise, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($data->acompte_facture, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  colspan="2" cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale-$data->acompte_facture, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>';
           if($data->libelle_depot!=null){
                $articlesDiversVentes = ArticleVente::with('article','unite')
                                                    ->join('articles','articles.id','=','article_ventes.article_id')
                                                    ->join('unites','unites.id','=','article_ventes.unite_id')
                                                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                                                    ->select('article_ventes.*','unites.libelle_unite','articles.description_article', 'param_tvas.montant_tva')
                                                    ->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne', 0],['article_ventes.vente_id',$data->idVente]])
                                                    ->get();
                $outPut .='<tr><td colspan="7" align="center" cellspacing="0" border="2" align="left"><b>Détails</b></td></tr>
                            <tr>
                                <th cellspacing="0" border="2" align="left">Article</th>
                                <th cellspacing="0" border="2" align="left">Colis </th>
                                <th cellspacing="0" border="2" align="left">Prix HT</th>
                                <th cellspacing="0" border="2" align="left">Prix TTC</th>
                                <th cellspacing="0" border="2" align="left">Quantité</th>
                                <th cellspacing="0" border="2" align="left">Montant TTC</th>
                                <th cellspacing="0" border="2" align="left">Remise</th>
                            </tr>';
                foreach ($articlesDiversVentes as $article){
                    $article->montant_tva!=null ? $montantHT = round($article->prix/($article->montant_tva + 1), 0) : $montantHT = $article->prix;
                            
                    $outPut .= '<tr>
                                    <td cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$article->description_article.'</td>
                                    <td cellspacing="0" border="2" align="right">'.$article->libelle_unite.'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($montantHT, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($article->prix, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.$article->quantite.'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($article->prix*$article->quantite, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td cellspacing="0" border="2" align="right">'.number_format($article->remise_sur_ligne, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                </tr>';
                }
                $outPut .='<tr>
                            <td colspan="4" cellspacing="0" border="2" align="left">&nbsp;&nbsp;<b>Total TTC</b></td>
                            <td colspan="3" cellspacing="0" border="2" align="right"><b>'.$data->sommeTotale.'&nbsp;&nbsp;</b></td>
                          </tr>';
           }else{
                $articlesDiversVentes =  ArticleVente::with('divers')
                                                    ->join('divers','divers.id','=','article_ventes.divers_id')
                                                    ->Where([['article_ventes.deleted_at', NULL],['article_ventes.vente_id',$data->idVente]])
                                                    ->select('article_ventes.*','divers.libelle_divers')
                                                    ->get();
                $outPut .='<tr><td colspan="7" align="center" cellspacing="0" border="2" align="left"><b>Détails</b></td></tr>
                            <tr>
                            <th colspan="3" cellspacing="0" border="2" align="left">Libellé</th>
                            <th colspan="1" cellspacing="0" border="2" align="left">Quantité </th>
                            <th colspan="1" cellspacing="0" border="2" align="left">Prix unitaire</th>
                            <th colspan="2" cellspacing="0" border="2" align="left">Montant</th>
                        </tr>';
                foreach ($articlesDiversVentes as $article){
                    $outPut .= '<tr>
                                    <td  colspan="3" cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$article->libelle_divers.'</td>
                                    <td  colspan="1" cellspacing="0" border="2" align="right">'.$article->quantite.'&nbsp;&nbsp;</td>
                                    <td  colspan="1" cellspacing="0" border="2" align="right">'.number_format($article->prix, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                    <td  colspan="2" cellspacing="0" border="2" align="right">'.number_format($article->prix*$article->quantite, 0, ',', ' ').'&nbsp;&nbsp;</td>
                                </tr>';
                }
                $outPut .='<tr>
                            <td colspan="4" cellspacing="0" border="2" align="left">&nbsp;&nbsp;<b>Total TTC</b></td>
                            <td colspan="3" cellspacing="0" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</b></td>
                          </tr>';
           }
        }
        $outPut .='</table></div>';
        $outPut.='TOTAL ACHAT :<b> '.number_format($totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='TOTAL REMISE :<b> '.number_format($totalRemise, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='TOTAL ACOMPTE :<b> '.number_format($totalAcompte, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='TOTAL RESTE :<b> '.number_format($totalAchat-$totalAcompte, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function listeReglementsClientPdf($client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementsClients($client));
        return $pdf->stream('liste_reglements_client.pdf');
    }
    public function listeReglementsClients($client){
        $infoClient = Client::find($client);
        $datas = Reglement::where([['reglements.deleted_at', NULL],['ventes.client_id',$client]])
                ->join('ventes','ventes.id','=','reglements.vente_id')
                ->join('clients','clients.id','=','ventes.client_id')
                ->join('moyen_reglements','moyen_reglements.id','=','reglements.moyen_reglement_id')
                ->select('reglements.*','moyen_reglements.libelle_moyen_reglement','ventes.client_id as id_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->orderBy('reglements.id', 'DESC')
                ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste de tous les règlements du client '.$infoClient->full_name_client.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="25%" align="center">N° virement ou chèque</th>
                        </tr>';
        $totalMontant = 0;
        foreach ($datas as $data){
            $totalMontant = $totalMontant + $data->montant_reglement;
            
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_reglement, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;Facture N° '.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="right">'.$data->numero_cheque_virement.'&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .='</table></div>';
        $outPut.='MONTANT TOTAL :<b> '.number_format($totalMontant, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function listeReglementsClientByPeriodePdf($debut,$fin,$client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementsClientByPeriodes($debut,$fin,$client));
        return $pdf->stream('liste_reglements_client.pdf');
    }
    public function listeReglementsClientByPeriodes($debut,$fin,$client){
        $infoClient = Client::find($client);
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Reglement::with('moyen_reglement')
                ->join('ventes','ventes.id','=','reglements.vente_id')
                ->join('clients','clients.id','=','ventes.client_id')
                ->select('reglements.*','ventes.client_id as id_client','ventes.numero_facture',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id',$client]])
                ->whereDate('reglements.date_reglement','>=',$date1)
                ->whereDate('reglements.date_reglement','<=', $date2)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste de tous les règlements du client '.$infoClient->full_name_client.' sur la période du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="25%" align="center">N° virement ou chèque</th>
                        </tr>';
        $totalMontant = 0;
        foreach ($datas as $data){
            $totalMontant = $totalMontant + $data->montant_reglement;
            
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_reglement, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;Facture N° '.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="right">'.$data->numero_cheque_virement.'&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .='</table></div>';
        $outPut.='MONTANT TOTAL :<b> '.number_format($totalMontant, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    public function listeArticlesPlusAchetesPdf($client){
         $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeArticlesPlusAchetess($client));
        return $pdf->stream('liste_articles_achats_client.pdf');
    }
    public function listeArticlesPlusAchetess($client){
        $infoClient = Client::find($client);
        $datas = Vente::where([['ventes.deleted_at',null],['ventes.client_id',$client],['ventes.proformat',0]])
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->join('articles','articles.id','=','article_ventes.article_id')
                ->select('articles.description_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.quantite) as qteTotale'),DB::raw('sum(article_ventes.remise_sur_ligne) as remiseTotale'))
                ->groupBy('article_ventes.article_id')
                ->orderBy('qteTotale','DESC')
                ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des articles achetés par quantité pour le client '.$infoClient->full_name_client.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="50%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Quantité</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Montant</th>
                        </tr>';
        foreach ($datas as $data){
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->qteTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .='</table></div>';
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
