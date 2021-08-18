<?php

namespace App\Http\Controllers\Etat;

use App\Http\Controllers\Controller;
use App\Models\Boutique\BonCommande;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Client;
use App\Models\Parametre\Fournisseur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EtatComptabiliteController extends Controller
{
    public function vuSoldeClient(){
       $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Solde des clients";
       $btnModalAjout = "FALSE";
       return view('comptabilite.solde-client',compact('clients', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuSoldeFournisseur(){
       $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', NULL)->orderBy('full_name_fournisseur', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Solde des fournisseurs";
       $btnModalAjout = "FALSE";
       return view('comptabilite.solde-fournisseur',compact('fournisseurs', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuMargeVente(){
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Marge sur vente du jour";
       $btnModalAjout = "FALSE";
       return view('comptabilite.marge-vente',compact('depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuTimbreFiscal(){
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Timbre fiscal sur les ventes du jour";
       $btnModalAjout = "FALSE";
       return view('comptabilite.timbre-fiscal',compact('depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuDeclarationFiscal(){
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Déclaration TVA sur les ventes du jour";
       $btnModalAjout = "FALSE";
       return view('comptabilite.declaration-fiscal',compact('depots','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    public function vuTvaAirsi(){
       $menuPrincipal = "Etat";
       $titleControlleur = "TVA et AIRSI";
       $btnModalAjout = "FALSE";
       return view('comptabilite.tva-airsi',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuPointCaisseCloture(){
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Les points de caisse cloturés du jour";
       $btnModalAjout = "FALSE";
       return view('comptabilite.point-caisse-cloture',compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function listeSoldeClient(){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $ventes = Vente::with('client')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                ->where([['ventes.deleted_at', NULL],['ventes.client_id','!=', NULL],['ventes.proformat',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('article_ventes.vente_id','DESC')
                ->get();
        foreach ($ventes as $vente){
            $MontantTotalDu = $MontantTotalDu + $vente->sommeTotale;
            $MontantTotalAcompte = $MontantTotalAcompte + $vente->sommeAcompte;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["MontantTotalDu"] = $MontantTotalDu;
        $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
        return response()->json($jsonData);
    }
    public function listeSoldeClientByClient($client){
          $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $ventes = Vente::with('client')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_facture'))
                ->where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.proformat',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('article_ventes.vente_id','DESC')
                ->get();
        foreach ($ventes as $vente){
            $MontantTotalDu = $MontantTotalDu + $vente->sommeTotale;
            $MontantTotalAcompte = $MontantTotalAcompte + $vente->sommeAcompte;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["MontantTotalDu"] = $MontantTotalDu;
        $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
        return response()->json($jsonData);
    }
    public function listeSoldeFournisseur(){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
       $bon_commandes = BonCommande::with('fournisseur')
                        ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                        ->select('bon_commandes.*','bon_commandes.accompteFournisseur',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                        ->Where([['bon_commandes.deleted_at', NULL],['livrer',1]])
                        ->groupBy('bon_commandes.id')
                        ->get();
        foreach ($bon_commandes as $bon_commande){
            $MontantTotalDu = $MontantTotalDu + $bon_commande->montantCommande;
            $MontantTotalAcompte = $MontantTotalAcompte + $bon_commande->accompteFournisseur;
        }
        $jsonData["rows"] = $bon_commandes->toArray();
        $jsonData["total"] = $bon_commandes->count();
        $jsonData["MontantTotalDu"] = $MontantTotalDu;
        $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
        return response()->json($jsonData);
    }
    public function listeSoldeFournisseurByFournisseur($fournisseur){
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
        $bon_commandes = BonCommande::with('fournisseur')
                        ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                        ->select('bon_commandes.*','bon_commandes.accompteFournisseur',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                        ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.fournisseur_id',$fournisseur],['livrer',1]])
                        ->groupBy('bon_commandes.id')
                        ->get();
        foreach ($bon_commandes as $bon_commande){
            $MontantTotalDu = $MontantTotalDu + $bon_commande->montantCommande;
            $MontantTotalAcompte = $MontantTotalAcompte + $bon_commande->accompteFournisseur;
        }
        $jsonData["rows"] = $bon_commandes->toArray();
        $jsonData["total"] = $bon_commandes->count();
        $jsonData["MontantTotalDu"] = $MontantTotalDu;
        $jsonData["MontantTotalAcompte"] = $MontantTotalAcompte;
        return response()->json($jsonData);
    }
    
    public function listeTimbreVentes(){
        $date_jour = date("Y-m-d");
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.montant_tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente',$date_jour)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();

        return response()->json($jsonData);
    }
    public function listeTimbreVentesByDepot($depot){
        $date_jour = date("Y-m-d");
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.montant_tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.depot_id', $depot],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente',$date_jour)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();

        return response()->json($jsonData);
    }
    public function listeTimbreVentesPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.montant_tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente','>=',$dateDebut)
                    ->whereDate('ventes.date_vente','<=', $dateFin)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeTimbreVentesPeriodeDepot($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.montant_tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.depot_id', $depot],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente','>=',$dateDebut)
                    ->whereDate('ventes.date_vente','<=', $dateFin)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();

        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    
    public function listeDeclarationVentes(){
        $date_jour = date("Y-m-d");
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.numero_ticket','param_tvas.montant_tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.montant_tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','article_ventes.remise_sur_ligne as montant_remise','articles.description_article','unites.libelle_unite')
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente',$date_jour)
                    ->orderBy('ventes.id','DESC')
                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }
    public function listeDeclarationVentesByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.numero_ticket','param_tvas.montant_tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.montant_tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','article_ventes.remise_sur_ligne as montant_remise','articles.description_article','unites.libelle_unite')
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente','>=',$dateDebut)
                    ->whereDate('ventes.date_vente','<=', $dateFin)
                    ->orderBy('ventes.id','DESC')
                    ->get();
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        return response()->json($jsonData);
    }

    public function listeAllVentesMarge(){
        $date_jour = date("Y-m-d");
        $totalTTC=0; $totalAchat=0;
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente',$date_jour)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
    public function listeAllVentesMargeDepot($depot){
      $date_jour = date("Y-m-d");
        $totalTTC=0; $totalAchat=0;
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null],['ventes.depot_id',$depot]])
                    ->whereDate('ventes.date_vente',$date_jour)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
    public function listeAllVentesMargePeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $totalTTC=0; $totalAchat=0;
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereDate('ventes.date_vente','>=',$dateDebut)
                    ->whereDate('ventes.date_vente','<=', $dateFin)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }
    public function listeAllVentesMargePeriodeDepot($debut,$fin,$depot){
         $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $totalTTC=0; $totalAchat=0;
        $ventes = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->where('article_ventes.retourne',0)
                    ->join('articles','articles.id','=','article_ventes.article_id')
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->select('ventes.*',DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null],['ventes.depot_id',$depot]])
                    ->whereDate('ventes.date_vente','>=',$dateDebut)
                    ->whereDate('ventes.date_vente','<=', $dateFin)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();
        foreach ($ventes as $vente) {
            $totalAchat = $totalAchat + $vente->montantAchat;
            $totalTTC = $totalTTC + $vente->montantTTC;
        }
        $jsonData["rows"] = $ventes->toArray();
        $jsonData["total"] = $ventes->count();
        $jsonData["totalAchat"] = $totalAchat;
        $jsonData["totalTTC"] = $totalTTC;
        return response()->json($jsonData);
    }

    public function listePointCaisseCloturesJour(){
        $date_jour = date("Y-m-d");
        $caisses = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->whereDate('caisse_ouvertes.date_ouverture',$date_jour)
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }
    public function listePointCaisseCloturesJourDepot($depot){
       
        $caisses = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')->where('caisses.depot_id',$depot)
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }
    public function listePointCaisseCloturesPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $caisses = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->whereDate('caisse_ouvertes.created_at','>=',$dateDebut)
                                        ->whereDate('caisse_ouvertes.created_at','<=', $dateFin)
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }
    public function listePointCaisseCloturesPeriodeDepot($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $caisses = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')->where('caisses.depot_id',$depot)
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->whereDate('caisse_ouvertes.created_at','>=',$dateDebut)
                                        ->whereDate('caisse_ouvertes.created_at','<=', $dateFin)
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();
        $jsonData["rows"] = $caisses->toArray();
        $jsonData["total"] = $caisses->count();
        return response()->json($jsonData);
    }

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
    //Etat
    public function pointCaisseCloturesJourPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->pointCaisseCloturesJour());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('point_caisse_clotures.pdf');
    }
    public function pointCaisseCloturesJour(){
        $date_jour = date("Y-m-d");
        $datas = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->whereDate('caisse_ouvertes.date_ouverture',$date_jour)
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Point de caisse cloturés du jour</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Ouverture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Fermeture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Ouver.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Entrée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Sortie</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Solde</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caissier</th>
                        </tr>
                    </div>';
        $TotalSortie = 0; $TotalEntree = 0; $TotalOuverture = 0;
       foreach ($datas as $data){
           $TotalEntree = $TotalEntree + $data->sommeTotale+$data->entree;
           $TotalSortie = $TotalSortie + $data->sortie;
           $TotalOuverture = $TotalOuverture + $data->montant_ouverture;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ouvertures.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_fermetures.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale+$data->entree, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture+$data->sommeTotale+$data->entree-$data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->full_name.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total entrée : <b> '.number_format($TotalEntree, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total sortie : <b> '.number_format($TotalSortie, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total solde : <b> '.number_format($TotalOuverture+$TotalEntree-$TotalSortie, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    public function pointCaisseCloturesPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->pointCaisseCloturesPeriode($debut,$fin));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('point_caisse_clotures_periode.pdf');
    }
    public function pointCaisseCloturesPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->whereDate('caisse_ouvertes.created_at','>=',$dateDebut)
                                        ->whereDate('caisse_ouvertes.created_at','<=', $dateFin)
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Point de caisse cloturés du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Ouverture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Fermeture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Ouver.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Entrée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Sortie</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Solde</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caissier</th>
                        </tr>
                    </div>';
        $TotalSortie = 0; $TotalEntree = 0; $TotalOuverture = 0;
       foreach ($datas as $data){
           $TotalEntree = $TotalEntree + $data->sommeTotale+$data->entree;
           $TotalSortie = $TotalSortie + $data->sortie;
           $TotalOuverture = $TotalOuverture + $data->montant_ouverture;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ouvertures.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_fermetures.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale+$data->entree, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture+$data->sommeTotale+$data->entree-$data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->full_name.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total entrée : <b> '.number_format($TotalEntree, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total sortie : <b> '.number_format($TotalSortie, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total solde : <b> '.number_format($TotalOuverture+$TotalEntree-$TotalSortie, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    public function pointCaisseCloturesPeriodeDepotPdf($debut,$fin,$depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->pointCaisseCloturesPeriodeDepot($debut,$fin,$depot));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        return $pdf->stream('point_caisse_clotures_du_'.$debut.'_au_'.$fin.'_depot_'.$info_depot->libelle_depot.'.pdf');
    }
    public function pointCaisseCloturesPeriodeDepot($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        $datas = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')->where('caisses.depot_id',$depot)
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->whereDate('caisse_ouvertes.created_at','>=',$dateDebut)
                                        ->whereDate('caisse_ouvertes.created_at','<=', $dateFin)
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Point de caisse cloturés du '.$debut.' au '.$fin.' dans le dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Ouverture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Fermeture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Ouver.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Entrée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Sortie</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Solde</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caissier</th>
                        </tr>
                    </div>';
        $TotalSortie = 0; $TotalEntree = 0; $TotalOuverture = 0;
       foreach ($datas as $data){
           $TotalEntree = $TotalEntree + $data->sommeTotale+$data->entree;
           $TotalSortie = $TotalSortie + $data->sortie;
           $TotalOuverture = $TotalOuverture + $data->montant_ouverture;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ouvertures.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_fermetures.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale+$data->entree, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture+$data->sommeTotale+$data->entree-$data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->full_name.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total entrée : <b> '.number_format($TotalEntree, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total sortie : <b> '.number_format($TotalSortie, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total solde : <b> '.number_format($TotalOuverture+$TotalEntree-$TotalSortie, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    public function pointCaisseCloturesDepotPdf($depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->pointCaisseCloturesDepot($depot));
        $pdf->setPaper('A4', 'landscape');
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        return $pdf->stream('point_caisse_clotures_du_depot_'.$info_depot->libelle_depot.'.pdf');
    }
    public function pointCaisseCloturesDepot($depot){
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        $datas = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture','!=',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')->where('caisses.depot_id',$depot)
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_fermeture, "%d-%m-%Y à %H:%i:%s") as date_fermetures'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->groupBy('caisse_ouvertes.id')
                                        ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Point de caisse cloturés du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Ouverture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Fermeture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Ouver.</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Entrée</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Sortie</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Solde</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caissier</th>
                        </tr>
                    </div>';
        $TotalSortie = 0; $TotalEntree = 0; $TotalOuverture = 0;
       foreach ($datas as $data){
           $TotalEntree = $TotalEntree + $data->sommeTotale+$data->entree;
           $TotalSortie = $TotalSortie + $data->sortie;
           $TotalOuverture = $TotalOuverture + $data->montant_ouverture;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ouvertures.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_fermetures.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale+$data->entree, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_ouverture+$data->sommeTotale+$data->entree-$data->sortie, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->full_name.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total entrée : <b> '.number_format($TotalEntree, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total sortie : <b> '.number_format($TotalSortie, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total solde : <b> '.number_format($TotalOuverture+$TotalEntree-$TotalSortie, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Marge sur vente
    public function allVenteMargePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMarge());
        return $pdf->stream('marge_sur_ventes.pdf');
    }
    public function allVenteMarge(){
        $date_jour = date("Y-m-d");
       
        $datas = Vente::with('depot')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')->Where('article_ventes.retourne', 0)
                ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
                ->select('ventes.*', DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'), DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.divers', 0], ['ventes.client_id', null]])
                ->whereDate('ventes.date_vente', $date_jour)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Marge sur vente du jour</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Achat</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Vente</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Marge</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Taux Marge</th>
                        </tr>
                    </div>';
        $totalAchat=0; $totalVente=0;
       foreach ($datas as $data){
            $totalAchat = $totalAchat + $data->montantAchat;
            $totalVente = $totalVente + $data->montantTTC;
            $margeCommercial = $data->montantTTC - $data->montantAchat;
            $tauxMrge = ($margeCommercial/$data->montantAchat)*100;
          
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC-$data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.round($tauxMrge, 2).'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total prix achat : <b> '.number_format($totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total prix vente : <b> '.number_format($totalVente, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total marge : <b> '.number_format($totalVente-$totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function allVenteMargeDepotPdf($depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMargeDepot($depot));
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        return $pdf->stream('marge_sur_ventes_depot_'.$info_depot->libelle_depot.'.pdf');
    }
    public function allVenteMargeDepot($depot){
        $date_jour = date("Y-m-d");
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        $datas = Vente::with('depot')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')->Where('article_ventes.retourne', 0)
                ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
                ->select('ventes.*', DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'), DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.divers', 0], ['ventes.client_id', null], ['ventes.depot_id', $depot]])
                ->whereDate('ventes.date_vente', $date_jour)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Marge sur vente du jour du dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Achat</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Vente</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Marge</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Taux Marge</th>
                        </tr>
                    </div>';
        $totalAchat=0; $totalVente=0;
       foreach ($datas as $data){
            $totalAchat = $totalAchat + $data->montantAchat;
            $totalVente = $totalVente + $data->montantTTC;
            $margeCommercial = $data->montantTTC - $data->montantAchat;
            $tauxMrge = ($margeCommercial/$data->montantAchat)*100;
          
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC-$data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.round($tauxMrge, 2).'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total prix achat : <b> '.number_format($totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total prix vente : <b> '.number_format($totalVente, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total marge : <b> '.number_format($totalVente-$totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function allVenteMargePeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMargePeriode($debut,$fin));
        return $pdf->stream('marge_sur_ventes_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function allVenteMargePeriode($debut,$fin){
       $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Vente::with('depot')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')->Where('article_ventes.retourne', 0)
                ->join('unites', 'unites.id', '=', 'article_ventes.unite_id')
                ->select('ventes.*', DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'), DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.divers', 0], ['ventes.client_id', null]])
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Marge sur vente du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Achat</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Vente</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Marge</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Taux Marge</th>
                        </tr>
                    </div>';
        $totalAchat=0; $totalVente=0;
       foreach ($datas as $data){
            $totalAchat = $totalAchat + $data->montantAchat;
            $totalVente = $totalVente + $data->montantTTC;
             $margeCommercial = $data->montantTTC - $data->montantAchat;
            $tauxMrge = ($margeCommercial/$data->montantAchat)*100;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC-$data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.round($tauxMrge, 2).'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total prix achat : <b> '.number_format($totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total prix vente : <b> '.number_format($totalVente, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total marge : <b> '.number_format($totalVente-$totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function allVenteMargePeriodeDepotPdf($debut,$fin,$depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->allVenteMargePeriodeDepot($debut,$fin,$depot));
        $info_depot = \App\Models\Parametre\Depot::find($depot);
        return $pdf->stream('marge_sur_ventes_du_depot_'.$info_depot->libelle_depot.' du '.$debut.'_au_'.$fin.'.pdf');
    }
    public function allVenteMargePeriodeDepot($debut,$fin,$depot){
        $info_depot = \App\Models\Parametre\Depot::find($depot);
       $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Vente::with('depot')
                ->join('article_ventes', 'article_ventes.vente_id', '=', 'ventes.id')
                ->join('articles', 'articles.id', '=', 'article_ventes.article_id')->Where('article_ventes.retourne', 0)
                ->select('ventes.*', DB::raw('sum(articles.prix_achat_ttc*article_ventes.quantite*unites.quantite_unite) as montantAchat'), DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'), DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL], ['ventes.divers', 0], ['ventes.client_id', null]])
                ->Where([['ventes.deleted_at', NULL], ['ventes.divers', 0], ['ventes.client_id', null], ['ventes.depot_id', $depot]])
                ->whereDate('ventes.date_vente', '>=', $dateDebut)
                ->whereDate('ventes.date_vente', '<=', $dateFin)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id', 'DESC')
                ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Marge sur vente du dépôt '.$info_depot->libelle_depot.' sur la période du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Achat</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Vente</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant Marge</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Taux Marge</th>
                        </tr>
                    </div>';
        $totalAchat=0; $totalVente=0;
       foreach ($datas as $data){
            $totalAchat = $totalAchat + $data->montantAchat;
            $totalVente = $totalVente + $data->montantTTC;
             $margeCommercial = $data->montantTTC - $data->montantAchat;
            $tauxMrge = ($margeCommercial/$data->montantAchat)*100;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC-$data->montantAchat, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.round($tauxMrge, 2).'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total prix achat : <b> '.number_format($totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total prix vente : <b> '.number_format($totalVente, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total marge : <b> '.number_format($totalVente-$totalAchat, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Vente 
    public function timbreFiscalPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->timbreFiscal());
        return $pdf->stream('ventes_caisse.pdf');
    }
    public function timbreFiscal(){
        $array = json_decode($_GET["array"]);
        $ids = [];
        foreach ($array as $indext => $arr){
           $ids[$indext] = $arr->id;
        }
        $datas = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                    ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.*',DB::raw('SUM(article_ventes.quantite*(article_ventes.prix/(1+param_tvas.montant_tva))) AS  totalHT'),DB::raw('sum(article_ventes.quantite*article_ventes.prix) as montantTTC'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereIn('ventes.id', $ids)
                    ->groupBy('article_ventes.vente_id')
                    ->orderBy('ventes.id','DESC')
                    ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Timbre fiscal</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant HT</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Net à payer</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Timbre</th>
                        </tr>
                    </div>';
        $totalTTC=0; $totalHT=0; $totalTimbre=0; $timbre=0;
       foreach ($datas as $data){
            $totalHT = $totalHT + $data->totalHT;
            $totalTTC = $totalTTC + $data->montantTTC;
            $data->montantTTC >5000 ? $totalTimbre = $totalTimbre+100 : $totalTimbre = $totalTimbre+0;
            $data->montantTTC >5000 ? $timbre = 100 : $timbre = 0;
            
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->totalHT, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantTTC-$data->sommeRemise, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($timbre, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total HT : <b> '.number_format($totalHT, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total TTC : <b> '.number_format($totalTTC, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total Timbre : <b> '.number_format($totalTimbre, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function declarationTvaPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->declarationTva());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('ventes_caisse.pdf');
    }
    public function declarationTva(){
        $array = json_decode($_GET["array"]);
        $ids = [];
       
        foreach ($array as $indext => $arr){
           $ids[$indext] = $arr->id;
        }
        $datas = Vente::with('depot')
                    ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                    ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                    ->join('unites','unites.id','=','article_ventes.unite_id')
                    ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                    ->select('ventes.id as idVente','ventes.numero_ticket','param_tvas.montant_tva','article_ventes.id as idArticleVente','article_ventes.quantite',DB::raw('(article_ventes.prix/(1+param_tvas.montant_tva)) AS  prix_ht'),'article_ventes.prix as prix_vente_ttc','article_ventes.remise_sur_ligne as montant_remise','articles.description_article','unites.libelle_unite')
                    ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                    ->whereIn('article_ventes.id', $ids)
                    ->orderBy('ventes.id','DESC')
                    ->get();
        
        //Requête pour regrouper les montants par tva 
        $groupeByTvas = Vente::with('depot')
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')
                            ->join('articles','articles.id','=','article_ventes.article_id')->Where('article_ventes.retourne',0)
                            ->join('unites','unites.id','=','article_ventes.unite_id')
                            ->leftjoin('param_tvas','param_tvas.id','=','articles.param_tva_id')
                            ->select('param_tvas.montant_tva','article_ventes.quantite',DB::raw('SUM(article_ventes.prix*article_ventes.quantite) AS prix_vente_ttc'),DB::raw('SUM(article_ventes.remise_sur_ligne) AS montant_remise'))
                            ->Where([['ventes.deleted_at', NULL],['ventes.divers',0],['ventes.client_id',null]])
                            ->whereIn('article_ventes.id', $ids)
                            ->groupBy('param_tvas.id')
                            ->get();

        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Déclaration  TVA</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">N° Ticket</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix HT</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Prix TTC</th>
                            <th cellspacing="0" border="2" width="18%" align="center">Montant HT</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Mont. TVA</th>
                            <th cellspacing="0" border="2" width="5%" align="center">TVA</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Remise</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant Net</th>
                        </tr>
                    </div>';
        
        //Enregistrement des declarations dans la base
        $declaration = new \App\Models\Boutique\TvaDeclaree;
        $declaration->date_declaration = now();
        $declaration->save();
        
        $totalTTC=0; $totalHT=0; $montantTva=0;$totalTva=0;
       foreach ($datas as $data){
            $montantTva = $data->prix_ht*$data->montant_tva*$data->quantite;
            $totalHT = $totalHT + $data->prix_ht*$data->quantite;
            $totalTTC = $totalTTC + $data->prix_vente_ttc*$data->quantite;
            $totalTva = $totalTva + $montantTva;
            
            $ticket_in_tva = new \App\Models\Boutique\TicketInTva;
            $ticket_in_tva->ticket = $data->idArticleVente;
            $ticket_in_tva->declaration = $declaration->id;
            $ticket_in_tva->save();
            
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_ticket.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_ht, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente_ttc, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_ht*$data->quantite, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente_ttc*$data->quantite, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($montantTva, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.($data->montant_tva*100).' %&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montant_remise, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->prix_vente_ttc*$data->quantite-$data->montant_remise, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total HT : <b> '.number_format($totalHT, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total TVA : <b> '.number_format($totalTva, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total TTC : <b> '.number_format($totalTTC, 0, ',', ' ').' F CFA</b><br/><br/>';
        
        $outPut.='Répartition des monatnt par TVA<br/>';
        foreach ($groupeByTvas as $resp){
             $outPut.='Chiffre d\'affaires TTC TVA '.($resp->montant_tva*100).'% : <b> '.number_format(($resp->prix_vente_ttc-$resp->montant_remise), 0, ',', ' ').' F CFA</b><br/>';  
        }
        $outPut.= $this->footer();
        return $outPut;
    }
    

    public function soldeClientPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->soldeClient());
        return $pdf->stream('solde_clients.pdf');
    }
    public function soldeClient(){
       
        $datas = Vente::with('client')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.retourne',0)
                ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->where([['ventes.deleted_at', NULL],['ventes.client_id','!=', NULL],['ventes.proformat',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('article_ventes.vente_id','DESC')
                ->get();
       
        $outPut = $this->header();
        
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des soldes des clients</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Client</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Crédit</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Acompte</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Doit</th>
                        </tr>
                    </div>';
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
       foreach ($datas as $data){
           $MontantTotalDu = $MontantTotalDu + $data->sommeTotale;
           $MontantTotalAcompte = $MontantTotalAcompte + $data->sommeAcompte;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->client->full_name_client.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->client->contact_client.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeAcompte, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale-$data->sommeAcompte, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total crédit : <b> '.number_format($MontantTotalDu, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total acompte : <b> '.number_format($MontantTotalAcompte, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total du : <b> '.number_format($MontantTotalDu-$MontantTotalAcompte, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    public function soldeClientByClientPdf($client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->soldeClientByClient($client));
        $info_client = Client::find($client);
        return $pdf->stream('solde_du_client_'.$info_client->full_name_client.'.pdf');
    }
    public function soldeClientByClient($client){
         $datas = Vente::with('client')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.retourne',0)
                ->select('ventes.*','acompte_facture as sommeAcompte',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->where([['ventes.deleted_at', NULL],['ventes.client_id',$client],['ventes.proformat',0]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('article_ventes.vente_id','DESC')
                ->get();
        $info_client = Client::find($client);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des soldes du client '.$info_client->full_name_client.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Crédit</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Acompte</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Doit</th>
                        </tr>
                    </div>';
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
       foreach ($datas as $data){
           $MontantTotalDu = $MontantTotalDu + $data->sommeTotale;
           $MontantTotalAcompte = $MontantTotalAcompte + $data->sommeAcompte;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_facture.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeAcompte, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale-$data->sommeAcompte, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total crédit : <b> '.number_format($MontantTotalDu, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total acompte : <b> '.number_format($MontantTotalAcompte, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total du : <b> '.number_format($MontantTotalDu-$MontantTotalAcompte, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function soldeFournisseurPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->soldeFournisseur());
        return $pdf->stream('solde_fournisseurs.pdf');
    }
    public function soldeFournisseur(){
       
        $datas = BonCommande::with('fournisseur')
                        ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                        ->select('bon_commandes.*','bon_commandes.accompteFournisseur',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                        ->Where([['bon_commandes.deleted_at', NULL],['livrer',1]])
                        ->groupBy('bon_commandes.id')
                        ->get();
       
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des soldes des fournisseurs</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">N° Bon</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Fournisseur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Crédit</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Acompte</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Doit</th>
                        </tr>
                    </div>';
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
       foreach ($datas as $data){
           $MontantTotalDu = $MontantTotalDu + $data->montantCommande;
           $MontantTotalAcompte = $MontantTotalAcompte + $data->accompteFournisseur;
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_bon.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->fournisseur->full_name_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->fournisseur->contact_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantCommande, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->accompteFournisseur, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantCommande-$data->accompteFournisseur, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total crédit : <b> '.number_format($MontantTotalDu, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total acompte : <b> '.number_format($MontantTotalAcompte, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total du : <b> '.number_format($MontantTotalDu-$MontantTotalAcompte, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    public function soldeFournisseurByFournisseurPdf($fournisseur){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->soldeFournisseurByFournisseur($fournisseur));
        $info_fournisseur = Fournisseur::find($fournisseur);
        return $pdf->stream('solde_du_fournisseur_'.$info_fournisseur->full_name_fournisseur.'.pdf');
    }
    public function soldeFournisseurByFournisseur($fournisseur){
         $datas = BonCommande::with('fournisseur')
                        ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                        ->select('bon_commandes.*','bon_commandes.accompteFournisseur',DB::raw('sum(article_bons.quantite_recu*article_bons.prix_article) as montantCommande'))
                        ->Where([['bon_commandes.deleted_at', NULL],['bon_commandes.fournisseur_id',$fournisseur],['livrer',1]])
                        ->groupBy('bon_commandes.id')
                        ->get();
        $info_fournisseur = Fournisseur::find($fournisseur);
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des soldes du fournisseur '.$info_fournisseur->full_name_fournisseur.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">N° BON</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Crédit</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Acompte</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Doit</th>
                        </tr>
                    </div>';
        $MontantTotalDu = 0; $MontantTotalAcompte = 0;
       foreach ($datas as $data){
           $MontantTotalDu = $MontantTotalDu + $data->montantCommande;
           $MontantTotalAcompte = $MontantTotalAcompte + $data->accompteFournisseur;
                $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->numero_bon.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantCommande, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->accompteFournisseur, 0, ',', ' ').'&nbsp;</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->montantCommande-$data->accompteFournisseur, 0, ',', ' ').'&nbsp;</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Total crédit : <b> '.number_format($MontantTotalDu, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total acompte : <b> '.number_format($MontantTotalAcompte, 0, ',', ' ').' F CFA</b><br/>';
        $outPut.='Total du : <b> '.number_format($MontantTotalDu-$MontantTotalAcompte, 0, ',', ' ').' F CFA</b>';
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
