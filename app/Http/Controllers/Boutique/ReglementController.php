<?php

namespace App\Http\Controllers\Boutique;

use App\Http\Controllers\Controller;
use App\Models\Boutique\BonCommande;
use App\Models\Boutique\Reglement;
use App\Models\Boutique\Vente;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReglementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $moyenReglements = DB::table('moyen_reglements')->Where('deleted_at', NULL)->orderBy('libelle_moyen_reglement', 'asc')->get();
        $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
        $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', NULL)->orderBy('full_name_fournisseur', 'asc')->get();
        $menuPrincipal = "Boutique";
        $titleControlleur = "Règlement de facture";
        $btnModalAjout = "TRUE";
        return view('boutique.reglement.index',compact('moyenReglements','clients','fournisseurs','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
   
    public function listeReglement(){
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id','!=',null]])
                ->orWhere([['reglements.deleted_at', NULL],['reglements.commande_id','!=',null]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }

    public function listeReglementByDate($dates){
        $date = Carbon::createFromFormat('d-m-Y', $dates);
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id','!=',null]])
                ->orWhere([['reglements.deleted_at', NULL],['reglements.commande_id','!=',null]])
                ->whereDate('reglements.date_reglement',$date)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    
    public function listeReglementByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id','!=',null]])
                ->orWhere([['reglements.deleted_at', NULL],['reglements.commande_id','!=',null]])
                ->whereDate('reglements.date_reglement','>=',$dateDebut)
                ->whereDate('reglements.date_reglement','<=',$dateFin)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeReglementByPeriodeClient($debut,$fin,$client){
         $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['clients.id',$client]])
                ->whereDate('reglements.date_reglement','>=',$dateDebut)
                ->whereDate('reglements.date_reglement','<=',$dateFin)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    public function listeReglementByPeriodeFournisseur($debut,$fin,$fournisseur){
         $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['fournisseurs.id',$fournisseur]])
                ->whereDate('reglements.date_reglement','>=',$dateDebut)
                ->whereDate('reglements.date_reglement','<=',$dateFin)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }

    public function listeReglementByFournisseur($fournisseur){
       $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['fournisseurs.id',$fournisseur]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    
    public function listeReglementByClient($client){
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['clients.id',$client]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    
    public function listeReglementByMoyenReglement($moyen){
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['ventes.client_id','!=',null],['reglements.moyen_reglement_id',$moyen]])
                ->orWhere([['reglements.deleted_at', NULL],['reglements.commande_id','!=',null],['reglements.moyen_reglement_id',$moyen]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    
    public function listeReglementsByCommande($commande){
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['reglements.commande_id',$commande]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
        return response()->json($jsonData);
    }
    
    public function listeReglementsByVente($vente){
        $reglements = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['reglements.vente_id',$vente]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $jsonData["rows"] = $reglements->toArray();
        $jsonData["total"] = $reglements->count();
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
        if($request->isMethod('post') && $request->input('date_reglement')) {
            $data = $request->all();
            try {
                //*****Vérifions si le montant du est inferieur au montant saisi****//
                //Si reglement concerne un client
                if(isset($data['vente_id']) && !empty($data['vente_id'])){
                    $Vente = Vente::with('client','depot')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.id',$data['vente_id']]])
                            ->groupBy('article_ventes.vente_id')
                            ->first();
                    
                    $montantDu = $Vente->sommeTotale - $Vente->acompte_facture;
                    $reserve = 0;
                    if($data['montant_reglement'] > $montantDu){
                        $reserve = $data['montant_reglement'] - $montantDu;
                    }
                }
                
                //Si reglement concerne un fournisseur
                if(isset($data['commande_id']) && !empty($data['commande_id'])){
                    $BonCommande =  BonCommande::with('fournisseur')
                                    ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                                    ->select('bon_commandes.*',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                                    ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.id',$data['commande_id']]])
                                    ->groupBy('bon_commandes.id')->first();
                    $montantDu = $BonCommande->montantCommande - $BonCommande->accompteFournisseur;
                }
                
                $reglement = new Reglement; 
                $reglement->date_reglement = Carbon::createFromFormat('d-m-Y', $data['date_reglement']);
                $reglement->moyen_reglement_id = $data['moyen_reglement_id'];
                $reglement->montant_reglement = $data['montant_reglement'];
                $reglement->numero_cheque_virement = isset($data['numero_cheque_virement']) && !empty($data['numero_cheque_virement']) ? $data['numero_cheque_virement'] : null;
                $reglement->commande_id = isset($data['commande_id']) && !empty($data['commande_id']) ? $data['commande_id'] : null;
                $reglement->vente_id = isset($data['vente_id']) && !empty($data['vente_id']) ? $data['vente_id'] : null;
                
                //Ajout du scanne du chèque s'il y a en
                if(isset($data['scan_cheque']) && !empty($data['scan_cheque'])){
                    $scan_cheque = request()->file('scan_cheque');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$scan_cheque->getClientOriginalName()));
                    $path = public_path().'/documents/cheque/';
                    $scan_cheque->move($path,$file_name);
                    $reglement->scan_cheque = 'documents/cheque/'.$file_name;
                }
                $reglement->created_by = Auth::user()->id;
                $reglement->save();
                
                $reste_a_payer = 0;
                
                //Si reglement concerne un client
                if($reglement && isset($data['vente_id']) && !empty($data['vente_id'])){
                    $vente = Vente::find($data['vente_id']);
                    $vente->acompte_facture = $vente->acompte_facture + $data['montant_reglement'];
                    $vente->save();
                    $reste_a_payer = $Vente->sommeTotale - ($vente->acompte_facture + $reserve);
                }
                
                //Si reglement concerne un fournisseur
                if($reglement && isset($data['commande_id']) && !empty($data['commande_id'])){
                    $bon_commande = BonCommande::find($data['commande_id']);
                    $bon_commande->accompteFournisseur = $bon_commande->accompteFournisseur + $data['montant_reglement'];
                    $bon_commande->save();
                    $reste_a_payer = $BonCommande->montantCommande - $bon_commande->accompteFournisseur;
                }
                
                //Enregistrement du montant restant du
                $reglement->reste_a_payer = $reste_a_payer;
                $reglement->updated_by = Auth::user()->id;
                $reglement->save();
                
                $jsonData["data"] = json_decode($reglement);
                return response()->json($jsonData);
            }catch (Exception $exc) {
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
     * @param  \App\Reglement  $reglement
     * @return Response
     */
    public function updateReglement(Request $request)
    {
        $jsonData = ["code" => 1, "msg" => "Modification effectuée avec succès."];
        $reglement = Reglement::find($request->get('idReglement'));
        if($reglement){
            $data = $request->all(); 
             try {
                 $oldValue = $reglement->montant_reglement;
                 //Ajustement si concerne client
                if($reglement->vente_id!=null){
                    $vente = Vente::find($reglement->vente_id);
                    $vente->acompte_facture = $vente->acompte_facture - $reglement->montant_reglement;
                    $vente->save();
                }
                
                //Ajustement si concerne fournisseur
                if($reglement->commande_id!=null){
                    $bon_commande = BonCommande::find($data['commande_id']);
                    $bon_commande->accompteFournisseur = $bon_commande->accompteFournisseur - $reglement->montant_reglement;
                    $bon_commande->save();
                }
                
                //*****Vérifions si le montant du est inferieur au montant saisi****//
                //Si reglement concerne un client
                if(isset($data['vente_id']) && !empty($data['vente_id'])){
                     $Vente = Vente::with('client','depot')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                            ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.id',$data['vente_id']]])
                            ->groupBy('article_ventes.vente_id')
                            ->first();
                    
                    $montantDu = $Vente->sommeTotale - $Vente->acompte_facture;
                    $reserve = 0;
                    if($data['montant_reglement'] > $montantDu){
                        
                        if($reglement->vente_id!=null){
                            $Rgvente = Vente::find($reglement->vente_id);
                            $Rgvente->acompte_facture = $Rgvente->acompte_facture + $oldValue;
                            $Rgvente->save();
                        }else{
                            $Rgbon_commande = BonCommande::find($reglement->commande_id);
                            $Rgbon_commande->accompteFournisseur = $Rgbon_commande->accompteFournisseur + $oldValue;
                            $Rgbon_commande->save();
                        }
                       $reserve = $data['montant_reglement'] - $montantDu;
                    }
                }
                
                //Si reglement concerne un fournisseur
                if(isset($data['commande_id']) && !empty($data['commande_id'])){
                     $BonCommande =  BonCommande::with('fournisseur')
                                    ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                                    ->select('bon_commandes.*',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                                    ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.id',$data['commande_id']]])
                                    ->groupBy('bon_commandes.id')->first();
                    $montantDu = $BonCommande->montantCommande - $BonCommande->accompteFournisseur;
                }

                $reglement->date_reglement = Carbon::createFromFormat('d-m-Y', $data['date_reglement']);
                $reglement->moyen_reglement_id = $data['moyen_reglement_id'];
                $reglement->montant_reglement = $data['montant_reglement'];
                $reglement->numero_cheque_virement = isset($data['numero_cheque_virement']) && !empty($data['numero_cheque_virement']) ? $data['numero_cheque_virement'] : null;
                $reglement->commande_id = isset($data['commande_id']) && !empty($data['commande_id']) ? $data['commande_id'] : null;
                $reglement->vente_id = isset($data['vente_id']) && !empty($data['vente_id']) ? $data['vente_id'] : null;
                
                //Ajout du scanne du chèque s'il y a en
                if(isset($data['scan_cheque']) && !empty($data['scan_cheque'])){
                    $scan_cheque = request()->file('scan_cheque');
                    $file_name = str_replace(' ', '_', strtolower(time().'.'.$scan_cheque->getClientOriginalName()));
                    $path = public_path().'/documents/cheque/';
                    $scan_cheque->move($path,$file_name);
                    $reglement->scan_cheque = 'documents/cheque/'.$file_name;
                }
                $reglement->updated_by = Auth::user()->id;
                $reglement->save();
                
                $reste_a_payer = 0;
                //Si reglement concerne un client
                if($reglement && isset($data['vente_id']) && !empty($data['vente_id'])){
                    $vente = Vente::find($data['vente_id']);
                    $vente->acompte_facture = $vente->acompte_facture + $data['montant_reglement'];
                    $vente->save();
                    $reste_a_payer = $Vente->sommeTotale - ($vente->acompte_facture + $reserve);
                }
                
                //Si reglement concerne un fournisseur
                if($reglement && isset($data['commande_id']) && !empty($data['commande_id'])){
                    $bonCommande = BonCommande::find($data['commande_id']);
                    $bonCommande->accompteFournisseur = $bonCommande->accompteFournisseur + $data['montant_reglement'];
                    $bonCommande->save();
                    $reste_a_payer = $BonCommande->montantCommande - $bonCommande->accompteFournisseur;
                }
                
                //Enregistrement du montant restant du
                $reglement->reste_a_payer = $reste_a_payer;
                $reglement->updated_by = Auth::user()->id;
                $reglement->save();
                $jsonData["data"] = json_decode($reglement);
                return response()->json($jsonData);
            }catch (Exception $exc) {
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
     * @param  \App\Reglement  $reglement
     * @return Response
     */
    public function destroy(Reglement $reglement)
    {
        $jsonData = ["code" => 1, "msg" => " Opération effectuée avec succès."];
            if($reglement){
                 try{
                 //Ajustement si concerne client
                    if($reglement->vente_id!=null){
                        $vente = Vente::find($reglement->vente_id);
                        $vente->acompte_facture = $vente->acompte_facture - $reglement->montant_reglement;
                        $vente->save();
                    }

                    //Ajustement si concerne fournisseur
                    if($reglement->commande_id!=null){
                        $bonCommande = BonCommande::find($reglement->commande_id);
                        $bonCommande->accompteFournisseur = $bonCommande->accompteFournisseur - $reglement->montant_reglement;
                        $bonCommande->save();
                    }
                  
                    $reglement->update(['deleted_by' => Auth::user()->id]);
                    $reglement->delete();
                    $jsonData["data"] = json_decode($reglement);
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
    //Etat reçu
    public function recuReglementPdf($reglement){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->recuReglement($reglement));
        $recu = Reglement::find($reglement);
        return $pdf->stream('Recu'.$recu->id.'.pdf');
    }
    
    public function recuReglement($reglement){
        $outPut = $this->header($reglement);
        $outPut.= $this->content($reglement);
        //$outPut.= $this->footer();
        return $outPut;
    }
    
    public function header($reglement){
        $info_en_tete = Reglement::with('moyen_reglement')
                        ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                        ->leftJoin('clients','clients.id','=','ventes.client_id')
                        ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                        ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                        ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                        ->Where([['reglements.deleted_at', NULL],['reglements.id',$reglement]])
                        ->orderBy('reglements.id', 'DESC')
                        ->first();
        $info_en_tete->vente_id!=null ? $client_frs = "Client" : $client_frs = "Fournisseur";
        $info_en_tete->vente_id!=null ? $client_frs_name = $info_en_tete->full_name_client : $client_frs_name = $info_en_tete->full_name_fournisseur;
                          
        $header = "<html>
                        <head>
                            <meta charset='utf-8'>
                            <title></title>
                                    <style>
                                        @page { size: 10cm 21cm landscape; }
                                        
                                       .container{
                                            width: 100%;
                                            margin: 0 5px;
                                            font-size:15px;
                                        }
                                        .fixed-header-left{
                                            width: 30%;
                                            height:5%;
                                            position: absolute; 
                                            line-height:1;
                                            top: 0;
                                        }
                                        .container-recu{
                                            width: 100%;
                                            margin: 170px 0;
                                            font-size:17px;
                                            position: absolute;
                                        }
                                        .fixed-header-right{
                                            width: 40%;
                                            height:25%;
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
                                            height: 50px; 
                                            text-align:center;
                                        }     
                                        .titre-style{
                                         text-align:center;
                                         text-decoration: underline;
                                        }
                                    </style>
                        </head>
                <body style='margin-bottom:0; margin-top:0px;'>
                <div class='fixed-header-left'>
                    <div class='container'>
                         <img src=".$this->infosConfig()->logo." width='200' height='160'/> 
                    </div>
                </div>
               
                <div class='fixed-header-right'>
                    <div class='container'>
                       ".$client_frs." : <b>".$client_frs_name."</b><br/>
                       RECU N° : <b>".$info_en_tete->id."</b><br/>
                       Date : <b>".$info_en_tete->date_reglements."</b>
                    </div>
                </div>";     
        return $header;
    }
    
    public function content($reglement){
        $info_recu = Reglement::with('moyen_reglement')
                        ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                        ->leftJoin('clients','clients.id','=','ventes.client_id')
                        ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                        ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                        ->select('reglements.*','ventes.acompte_facture','bon_commandes.accompteFournisseur', 'ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                        ->Where([['reglements.deleted_at', NULL],['reglements.id',$reglement]])
                        ->orderBy('reglements.id', 'DESC')
                        ->first();
     
        $info_recu->vente_id!=null ? $bon_facture_numero = $info_recu->numero_facture : $bon_facture_numero = $info_recu->numero_bon;
        $info_recu->vente_id!=null ? $bon_facture = " la facture " : $bon_facture = " le bon ";
        $info_recu->vente_id!=null ? $client_frs = "Client" : $client_frs = "Fournisseur";
        $info_recu->vente_id!=null ? $client_frs_name = $info_recu->full_name_client : $client_frs_name = $info_recu->full_name_fournisseur;
        $info_recu->numero_cheque_virement!=null ? $numero_cheque_virement = " avec le numéro suivant ".$info_recu->numero_cheque_virement : $numero_cheque_virement = "";
        
        $content ="<div class='container-recu'> 
                        <p>Reçu de payement du ".$client_frs." <b><i>".$client_frs_name."</i></b> pour un acompte sur".$bon_facture."N° <b><i>".$bon_facture_numero."</i></b></p>
                        <p>Montant reglé <b><i>".number_format($info_recu->montant_reglement, 0, ',', ' ')." F CFA</i></b> reglé par <b><i>".strtolower($info_recu->moyen_reglement->libelle_moyen_reglement)."</i> ".$numero_cheque_virement."</b> à la date du <b><i>".$info_recu->date_reglements."</i></b></p>
                        <p>Montant restant à ce jour <b>".number_format($info_recu->reste_a_payer, 0, ',', ' ')." F CFA<b><p>
                    </div>";
        return $content;
    }
}
