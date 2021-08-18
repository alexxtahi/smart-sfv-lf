<?php

namespace App\Http\Controllers\Etat;

use App\Http\Controllers\Controller;
use App\Models\Boutique\Approvisionnement;
use App\Models\Boutique\ArticleRetourne;
use App\Models\Boutique\ArticleVente;
use App\Models\Boutique\Destockage;
use App\Models\Boutique\Inventaire;
use App\Models\Boutique\Reglement;
use App\Models\Boutique\RetourArticle;
use App\Models\Boutique\TransfertStock;
use App\Models\Boutique\Vente;
use App\Models\Parametre\Article;
use App\Models\Parametre\Categorie;
use App\Models\Parametre\Client;
use App\Models\Parametre\Depot;
use App\Models\Parametre\Fournisseur;
use App\Models\Parametre\Nation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EtatController extends Controller
{
    public function vuApprovisionnement(){
       $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', NULL)->orderBy('full_name_fournisseur', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Approvisionnement";
       $btnModalAjout = "FALSE";
       return view('etat.approvisionnement',compact('fournisseurs', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuVente(){
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Vente caisse";
       $btnModalAjout = "FALSE";
       return view('etat.vente',compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuArticle(){
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $categories = DB::table('categories')->Where('deleted_at', NULL)->orderBy('libelle_categorie', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Article";
       $btnModalAjout = "FALSE";
       return view('etat.article',compact('depots','categories', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuReglement(){
       $clients = DB::table('clients')->Where('deleted_at', NULL)->orderBy('full_name_client', 'asc')->get();
       $fournisseurs = DB::table('fournisseurs')->Where('deleted_at', NULL)->orderBy('full_name_fournisseur', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Règlement de facture";
       $btnModalAjout = "FALSE";
       return view('etat.reglement',compact('clients','fournisseurs','menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuFournisseur(){
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Fournisseur";
       $btnModalAjout = "FALSE";
       return view('etat.fournisseur',compact('nations', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuClient(){
       $nations = DB::table('nations')->Where('deleted_at', NULL)->orderBy('libelle_nation', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Client";
       $btnModalAjout = "FALSE";
       return view('etat.client',compact('nations', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuDepot(){
       $menuPrincipal = "Etat";
       $titleControlleur = "Dépôt";
       $btnModalAjout = "FALSE";
       return view('etat.depot',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuInventaire(){
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Inventaire";
       $btnModalAjout = "FALSE";
       return view('etat.inventaire',compact('depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuTransfertStock(){
       $articles = DB::table('articles')->Where('deleted_at', NULL)->orderBy('description_article', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Transfert de stock";
       $btnModalAjout = "FALSE";
       return view('etat.transfert-stock',compact('articles', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuDestockage(){
       $menuPrincipal = "Etat";
       $titleControlleur = "Déstockage";
       $btnModalAjout = "FALSE";
       return view('etat.destockage',compact('menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    
    public function vuArticleVenduParQuantite(){
        $articles = DB::table('articles')->Where('deleted_at', NULL)->orderBy('description_article', 'asc')->get();
        $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Articles vendus aujourd'hui";
       $btnModalAjout = "FALSE";
       return view('etat.articles-vendus-par-quantite',compact('articles','depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    public function vuArticleRecusParQuantite(){
       $articles = DB::table('articles')->Where('deleted_at', NULL)->orderBy('description_article', 'asc')->get();
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Articles reçus aujourd'hui";
       $btnModalAjout = "FALSE";
       return view('etat.articles-recus-par-quantite',compact('articles','depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
    public function vuArticleRetournees(){
       $articles = DB::table('articles')->Where('deleted_at', NULL)->orderBy('description_article', 'asc')->get();
       $depots = DB::table('depots')->Where('deleted_at', NULL)->orderBy('libelle_depot', 'asc')->get();
       $menuPrincipal = "Etat";
       $titleControlleur = "Articles retournés";
       $btnModalAjout = "FALSE";
       return view('etat.articles-retournes',compact('articles','depots', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }

    //Fonction pour recuperer les infos de Helpers
    public function infosConfig(){
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);
        return $get_configuration_infos;
    }
        // ***** Les Etats ***** //
    //Retour PDF
    public function listeRetourArticlePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->retourArticle());
        return $pdf->stream('liste_retour_article.pdf');
    }
    public function retourArticle(){
        $datas = RetourArticle::where('retour_articles.deleted_at', NULL)
                    ->join('ventes','ventes.id','=','retour_articles.vente_id')
                    ->join('depots','depots.id','=','ventes.depot_id')
                    ->join('article_retournes','article_retournes.retour_article_id','=','retour_articles.id')
                    ->select('retour_articles.*','retour_articles.id as id_ligne','depots.libelle_depot',DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'),'ventes.numero_facture','ventes.numero_ticket',DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
                    ->groupBy('article_retournes.retour_article_id')
                    ->orderBy('retour_articles.date_retour', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des articles retournés</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" colspan="1" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" colspan="2" border="2" width="30%" align="center">Ticket/Facture</th>
                            <th cellspacing="0" colspan="2" border="2" width="35%" align="center">Dépôt</th>
                            <th cellspacing="0" colspan="2" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
         $total = 0; $montant_global = 0;
       foreach ($datas as $data){
           $vente = Vente::find($data->vente_id);
           $vente->numero_ticket!=null? $numero_facture_ticket=$vente->numero_ticket:$numero_facture_ticket='FACT'.$vente->numero_facture;
           $total = $total + 1;
           $montant_global = $montant_global + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" colspan="1" border="2" align="left">&nbsp;&nbsp;<b>'.$data->date_retours.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$numero_facture_ticket.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$data->libelle_depot.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'</b>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté vendue</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté retournée</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                       ';
         
           $articles_retournes = ArticleRetourne::with('article','unite')
                                ->join('articles','articles.id','=','article_retournes.article_id')
                                ->join('unites','unites.id','=','article_retournes.unite_id')
                                ->select('article_retournes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                ->Where('article_retournes.retour_article_id',$data->id_ligne)
                                ->get();
           foreach ($articles_retournes as $item){
               $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.number_format($item->prix_unitaire, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite_vendue.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($item->quantite*$item->prix_unitaire, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
           }
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> retours pour un montant global de <b>'.number_format($montant_global, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Retour par période PDF
    public function listeRetourArticlePeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->retourArticlePeriode($debut,$fin));
        return $pdf->stream('liste_retour_article_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function retourArticlePeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = RetourArticle::where('retour_articles.deleted_at', NULL)
                    ->join('ventes','ventes.id','=','retour_articles.vente_id')
                    ->join('depots','depots.id','=','ventes.depot_id')
                    ->join('article_retournes','article_retournes.retour_article_id','=','retour_articles.id')
                    ->select('retour_articles.*','retour_articles.id as id_ligne','depots.libelle_depot',DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'),'ventes.numero_facture','ventes.numero_ticket',DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
                    ->whereDate('retour_articles.date_retour','>=', $dateDebut)
                    ->whereDate('retour_articles.date_retour','<=', $dateFin)
                    ->groupBy('article_retournes.retour_article_id')
                    ->orderBy('retour_articles.date_retour', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des articles retournés du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" colspan="1" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" colspan="2" border="2" width="30%" align="center">Ticket/Facture</th>
                            <th cellspacing="0" colspan="2" border="2" width="35%" align="center">Dépôt</th>
                            <th cellspacing="0" colspan="2" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
         $total = 0; $montant_global = 0;
       foreach ($datas as $data){
           $vente = Vente::find($data->vente_id);
           $vente->numero_ticket!=null? $numero_facture_ticket=$vente->numero_ticket:$numero_facture_ticket='FACT'.$vente->numero_facture;
           $total = $total + 1;
           $montant_global = $montant_global + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" colspan="1" border="2" align="left">&nbsp;&nbsp;<b>'.$data->date_retours.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$numero_facture_ticket.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$data->libelle_depot.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'</b>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté vendue</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté retournée</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                       ';
         
           $articles_retournes = ArticleRetourne::with('article','unite')
                                ->join('articles','articles.id','=','article_retournes.article_id')
                                ->join('unites','unites.id','=','article_retournes.unite_id')
                                ->select('article_retournes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                ->Where('article_retournes.retour_article_id',$data->id_ligne)
                                ->get();
           foreach ($articles_retournes as $item){
               $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.number_format($item->prix_unitaire, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite_vendue.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($item->quantite*$item->prix_unitaire, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
           }
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> retours pour un montant global de <b>'.number_format($montant_global, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Retour par période dépôt PDF
    public function listeRetourArticlePeriodeDepotPdf($debut,$fin,$depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->retourArticlePeriodeDepot($debut,$fin,$depot));
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_retour_article_du_'.$debut.'_au_'.$fin.'_au_depot_'.$info_depot->libelle_depot.'.pdf');
    }
    public function retourArticlePeriodeDepot($debut,$fin,$depot){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_depot = Depot::find($depot);
        $datas = RetourArticle::where([['retour_articles.deleted_at', NULL],['ventes.depot_id',$depot]])
                    ->join('ventes','ventes.id','=','retour_articles.vente_id')
                    ->join('depots','depots.id','=','ventes.depot_id')
                    ->join('article_retournes','article_retournes.retour_article_id','=','retour_articles.id')
                    ->select('retour_articles.*','retour_articles.id as id_ligne','depots.libelle_depot',DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'),'ventes.numero_facture','ventes.numero_ticket',DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
                    ->whereDate('retour_articles.date_retour','>=', $dateDebut)
                    ->whereDate('retour_articles.date_retour','<=', $dateFin)
                    ->groupBy('article_retournes.retour_article_id')
                    ->orderBy('retour_articles.date_retour', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des articles retournés du '.$debut.' au '.$fin.' dans le dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" colspan="1" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" colspan="2" border="2" width="30%" align="center">Ticket/Facture</th>
                            <th cellspacing="0" colspan="2" border="2" width="35%" align="center">Dépôt</th>
                            <th cellspacing="0" colspan="2" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
         $total = 0; $montant_global = 0;
       foreach ($datas as $data){
           $vente = Vente::find($data->vente_id);
           $vente->numero_ticket!=null? $numero_facture_ticket=$vente->numero_ticket:$numero_facture_ticket='FACT'.$vente->numero_facture;
           $total = $total + 1;
           $montant_global = $montant_global + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" colspan="1" border="2" align="left">&nbsp;&nbsp;<b>'.$data->date_retours.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$numero_facture_ticket.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$data->libelle_depot.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'</b>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté vendue</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté retournée</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                       ';
         
           $articles_retournes = ArticleRetourne::with('article','unite')
                                ->join('articles','articles.id','=','article_retournes.article_id')
                                ->join('unites','unites.id','=','article_retournes.unite_id')
                                ->select('article_retournes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                ->Where('article_retournes.retour_article_id',$data->id_ligne)
                                ->get();
           foreach ($articles_retournes as $item){
               $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.number_format($item->prix_unitaire, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite_vendue.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($item->quantite*$item->prix_unitaire, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
           }
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> retours pour un montant global de <b>'.number_format($montant_global, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Retour par période dépôt PDF
    public function listeRetourArticlePeriodeArticlePdf($debut,$fin,$article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->retourArticlePeriodeArticle($debut,$fin,$article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_retour_article_du_'.$debut.'_au_'.$fin.'_concernant_'.$info_article->description_article.'.pdf');
    }
    public function retourArticlePeriodeArticle($debut,$fin,$article){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $info_article = Article::find($article);
        $datas = RetourArticle::where([['retour_articles.deleted_at', NULL],['article_retournes.article_id',$article]])
                    ->join('ventes','ventes.id','=','retour_articles.vente_id')
                    ->join('depots','depots.id','=','ventes.depot_id')
                    ->join('article_retournes','article_retournes.retour_article_id','=','retour_articles.id')
                    ->select('retour_articles.*','retour_articles.id as id_ligne','depots.libelle_depot',DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'),'ventes.numero_facture','ventes.numero_ticket',DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
                    ->whereDate('retour_articles.date_retour','>=', $dateDebut)
                    ->whereDate('retour_articles.date_retour','<=', $dateFin)
                    ->groupBy('article_retournes.retour_article_id')
                    ->orderBy('retour_articles.date_retour', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des articles retournés du '.$debut.' au '.$fin.' concernant '.$info_article->description_article.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" colspan="1" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" colspan="2" border="2" width="30%" align="center">Ticket/Facture</th>
                            <th cellspacing="0" colspan="2" border="2" width="35%" align="center">Dépôt</th>
                            <th cellspacing="0" colspan="2" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
         $total = 0; $montant_global = 0;
       foreach ($datas as $data){
           $vente = Vente::find($data->vente_id);
           $vente->numero_ticket!=null? $numero_facture_ticket=$vente->numero_ticket:$numero_facture_ticket='FACT'.$vente->numero_facture;
           $total = $total + 1;
           $montant_global = $montant_global + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" colspan="1" border="2" align="left">&nbsp;&nbsp;<b>'.$data->date_retours.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$numero_facture_ticket.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$data->libelle_depot.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'</b>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté vendue</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté retournée</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                       ';
         
           $articles_retournes = ArticleRetourne::with('article','unite')
                                ->join('articles','articles.id','=','article_retournes.article_id')
                                ->join('unites','unites.id','=','article_retournes.unite_id')
                                ->select('article_retournes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                ->Where([['article_retournes.retour_article_id',$data->id_ligne],['article_retournes.article_id',$article]])
                                ->get();
           foreach ($articles_retournes as $item){
               $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.number_format($item->prix_unitaire, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite_vendue.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($item->quantite*$item->prix_unitaire, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
           }
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> retours pour un montant global de <b>'.number_format($montant_global, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Retour par période dépôt PDF
    public function listeRetourArticleByArticlePdf($article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->retourArticleByArticle($article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_retour_concernant_'.$info_article->description_article.'.pdf');
    }
    public function retourArticleByArticle($article){
        $info_article = Article::find($article);
        $datas = RetourArticle::where([['retour_articles.deleted_at', NULL],['article_retournes.article_id',$article]])
                    ->join('ventes','ventes.id','=','retour_articles.vente_id')
                    ->join('depots','depots.id','=','ventes.depot_id')
                    ->join('article_retournes','article_retournes.retour_article_id','=','retour_articles.id')
                    ->select('retour_articles.*','retour_articles.id as id_ligne','depots.libelle_depot',DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'),'ventes.numero_facture','ventes.numero_ticket',DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
                    ->groupBy('article_retournes.retour_article_id')
                    ->orderBy('retour_articles.date_retour', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des retours concernant '.$info_article->description_article.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" colspan="1" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" colspan="2" border="2" width="30%" align="center">Ticket/Facture</th>
                            <th cellspacing="0" colspan="2" border="2" width="35%" align="center">Dépôt</th>
                            <th cellspacing="0" colspan="2" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
         $total = 0; $montant_global = 0;
       foreach ($datas as $data){
           $vente = Vente::find($data->vente_id);
           $vente->numero_ticket!=null? $numero_facture_ticket=$vente->numero_ticket:$numero_facture_ticket='FACT'.$vente->numero_facture;
           $total = $total + 1;
           $montant_global = $montant_global + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" colspan="1" border="2" align="left">&nbsp;&nbsp;<b>'.$data->date_retours.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$numero_facture_ticket.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$data->libelle_depot.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'</b>&nbsp;&nbsp;</td>
                        </tr>
                         <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté vendue</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté retournée</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                       ';
         
           $articles_retournes = ArticleRetourne::with('article','unite')
                                ->join('articles','articles.id','=','article_retournes.article_id')
                                ->join('unites','unites.id','=','article_retournes.unite_id')
                                ->select('article_retournes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                ->Where([['article_retournes.retour_article_id',$data->id_ligne],['article_retournes.article_id',$article]])
                                ->get();
           foreach ($articles_retournes as $item){
               $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.number_format($item->prix_unitaire, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite_vendue.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($item->quantite*$item->prix_unitaire, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
           }
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> retours pour un montant global de <b>'.number_format($montant_global, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Retour par période dépôt PDF
    public function listeRetourArticleByDepotPdf($depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->retourArticleByDepot($depot));
        $info_depot = Depot::find($depot);
        return $pdf->stream('liste_retour_article_concernant_depot'.$info_depot->libelle_depot.'.pdf');
    }
    public function retourArticleByDepot($depot){
        $info_depot = Depot::find($depot);
        $datas = RetourArticle::where([['retour_articles.deleted_at', NULL],['ventes.depot_id',$depot]])
                    ->join('ventes','ventes.id','=','retour_articles.vente_id')
                    ->join('depots','depots.id','=','ventes.depot_id')
                    ->join('article_retournes','article_retournes.retour_article_id','=','retour_articles.id')
                    ->select('retour_articles.*','retour_articles.id as id_ligne','depots.libelle_depot',DB::raw('sum(article_retournes.quantite*article_retournes.prix_unitaire) as sommeTotale'),'ventes.numero_facture','ventes.numero_ticket',DB::raw('DATE_FORMAT(retour_articles.date_retour, "%d-%m-%Y") as date_retours'))
                    ->groupBy('article_retournes.retour_article_id')
                    ->orderBy('retour_articles.date_retour', 'DESC')
                    ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des retours articles concernant le dépôt '.$info_depot->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" colspan="1" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" colspan="2" border="2" width="30%" align="center">Ticket/Facture</th>
                            <th cellspacing="0" colspan="2" border="2" width="35%" align="center">Dépôt</th>
                            <th cellspacing="0" colspan="2" border="2" width="20%" align="center">Montant</th>
                        </tr>
                    </div>';
         $total = 0; $montant_global = 0;
       foreach ($datas as $data){
           $vente = Vente::find($data->vente_id);
           $vente->numero_ticket!=null? $numero_facture_ticket=$vente->numero_ticket:$numero_facture_ticket='FACT'.$vente->numero_facture;
           $total = $total + 1;
           $montant_global = $montant_global + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" colspan="1" border="2" align="left">&nbsp;&nbsp;<b>'.$data->date_retours.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$numero_facture_ticket.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="left">&nbsp;&nbsp;<b>'.$data->libelle_depot.'</b></td>
                            <td  cellspacing="0" colspan="2" border="2" align="right"><b>'.number_format($data->sommeTotale, 0, ',', ' ').'</b>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Code</th>
                            <th cellspacing="0" border="2" width="35%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Unité</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Prix</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté vendue</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Qté retournée</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant</th>
                        </tr>
                       ';
         
           $articles_retournes = ArticleRetourne::with('article','unite')
                                ->join('articles','articles.id','=','article_retournes.article_id')
                                ->join('unites','unites.id','=','article_retournes.unite_id')
                                ->select('article_retournes.*','articles.code_barre','articles.description_article','unites.libelle_unite')
                                ->Where('article_retournes.retour_article_id',$data->id_ligne)
                                ->get();
           foreach ($articles_retournes as $item){
               $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.number_format($item->prix_unitaire, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite_vendue.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$item->quantite.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($item->quantite*$item->prix_unitaire, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
           }
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').'</b> retours pour un montant global de <b>'.number_format($montant_global, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Approvisionnement PDF
    public function listeApprovisionnementPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->approvisionnement());
        return $pdf->stream('liste_approvisionnement.pdf');
    }
    public function approvisionnement(){
        $datas = Approvisionnement::with('fournisseur','depot')
                ->select('approvisionnements.*',DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
                ->Where('deleted_at', NULL)
                ->orderBy('approvisionnements.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des approvisionnements</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="12%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Fournisseur</th>
                        </tr>
                    </div>';
         $total = 0;
       foreach ($datas as $data){
           $total = $total + 1;
           $data->fournisseur_id != null ? $fournisseur = $data->fournisseur->full_name_fournisseur : $fournisseur = "";
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_approvisionnements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.' '.$data->depot->adresse_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$fournisseur.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' approvisionnement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Approvisionnement par période PDF
    public function listeApprovisionnementByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->approvisionnementByPeriode($debut,$fin));
        return $pdf->stream('liste_approvisionnement_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function approvisionnementByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Approvisionnement::with('fournisseur','depot')
                ->select('approvisionnements.*',DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
                ->Where('deleted_at', NULL)
                ->whereDate('approvisionnements.date_approvisionnement','>=',$date1)
                ->whereDate('approvisionnements.date_approvisionnement','<=', $date2)
                ->orderBy('approvisionnements.date_approvisionnement', 'ASC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des approvisionnements du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="12%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Fournisseur</th>
                        </tr>
                    </div>';
         $total = 0;
       foreach ($datas as $data){
           $total = $total + 1;
           $data->fournisseur_id != null ? $fournisseur = $data->fournisseur->full_name_fournisseur : $fournisseur = ""; 
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_approvisionnements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$fournisseur.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' approvisionnement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Approvisionnement par fournisseur PDF
    public function listeApprovisionnementByFournisseurPdf($fournisseur){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->approvisionnementByFournisseur($fournisseur));
        $infos_fournisseur = Fournisseur::find($fournisseur);
        return $pdf->stream('liste_approvisionnement_du_fournisseur'.$infos_fournisseur->full_name_fournisseur.'.pdf');
    }
    public function approvisionnementByFournisseur($fournisseur){
        $infos_fournisseur = Fournisseur::find($fournisseur);
        $datas = Approvisionnement::with('depot')
                ->select('approvisionnements.*',DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
                ->Where([['deleted_at', NULL],['approvisionnements.fournisseur_id',$fournisseur]])
                ->orderBy('approvisionnements.date_approvisionnement', 'ASC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des approvisionnements du fournisseur '.$infos_fournisseur->full_name_fournisseur.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="12%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Dépôt</th>
                        </tr>
                    </div>';
         $total = 0;
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_approvisionnements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.' '.$data->depot->adresse_depot.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' approvisionnement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Approvisionnement par fournisseur PDF
    public function listeApprovisionnementByPeriodeFournisseurPdf($debut,$fin,$fournisseur){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->approvisionnementByPeriodeFournisseur($debut,$fin,$fournisseur));
        $infos_frs = Fournisseur::find($fournisseur);
        return $pdf->stream('liste_approvisionnements_du_fournisseur_'.$infos_frs->full_name_fournisseur.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function approvisionnementByPeriodeFournisseur($debut,$fin,$fournisseur){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $infos_frs = Fournisseur::find($fournisseur);
        $datas = Approvisionnement::with('fournisseur','depot')
                ->select('approvisionnements.*',DB::raw('DATE_FORMAT(approvisionnements.date_approvisionnement, "%d-%m-%Y") as date_approvisionnements'))
                ->Where([['deleted_at', NULL],['approvisionnements.fournisseur_id',$fournisseur]])
                ->whereDate('approvisionnements.date_approvisionnement','>=',$date1)
                ->whereDate('approvisionnements.date_approvisionnement','<=', $date2)
                ->orderBy('approvisionnements.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des approvisionnements du fournisseur '.$infos_frs->full_name_fournisseur.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="12%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                        </tr>
                    </div>';
         $total = 0;
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_approvisionnements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.' '.$data->depot->adresse_depot.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' approvisionnement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    //Vente PDF
    public function listeVentePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->vente());
        return $pdf->stream('liste_ventes.pdf');
    }
    public function vente(){
        $datas = Vente::with('client','depot','caisse')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where('ventes.deleted_at', NULL)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des ventes</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Client</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                        </tr>
                    </div>';
         $montantTtotal = 0; $total=0;
       foreach ($datas as $data){
            $data->proformat == 0 ? $numero_fature = $data->numero_facture : $numero_fature = "Proforma";
            $montantTtotal = $montantTtotal + $data->sommeTotale;
            $total=$total+1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$numero_fature.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->client->full_name_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->caisse->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->sommeTotale, 0, ',', ' ').'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' vente(s)</b> pour une somme totale de <b>'.number_format($montantTtotal, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Vente par période PDF
    public function listeVenteByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->venteByPeriode($debut,$fin));
        return $pdf->stream('liste_ventes_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function venteByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Vente::with('client','depot','caisse')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where('ventes.deleted_at', NULL)
                ->whereDate('ventes.date_vente','>=',$date1)
                ->whereDate('ventes.date_vente','<=', $date2)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
              
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des ventes du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Client</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                        </tr>
                    </div>';
        $montantTtotal = 0; $total=0;
       foreach ($datas as $data){
            $data->proformat == 0 ? $numero_fature = $data->numero_facture : $numero_fature = "Proforma";
            $montantTtotal = $montantTtotal + $data->sommeTotale;
            $total=$total+1;
           $outPut .= '
                       <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$numero_fature.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->client->full_name_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->caisse->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->sommeTotale, 0, ',', ' ').'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' vente(s)</b> pour une somme totale de <b>'.number_format($montantTtotal, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Vente par client PDF
    public function listeVenteByClientPdf($client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->venteByClient($client));
        $info_client = Client::find($client);
        return $pdf->stream('liste_ventes_du_client_'.$info_client->full_name_client.'.pdf');
    }
    public function venteByClient($client){
        $info_client = Client::find($client);
        $datas = Vente::with('client','depot','caisse')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des ventes au client '.$info_client->full_name_client.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                         <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                        </tr>
                    </div>';
        $montantTtotal = 0; $total=0;
       foreach ($datas as $data){
            $data->proformat == 0 ? $numero_fature = $data->numero_facture : $numero_fature = "Proforma";
            $montantTtotal = $montantTtotal + $data->sommeTotale;
            $total=$total+1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$numero_fature.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->caisse->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->sommeTotale, 0, ',', ' ').'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' vente(s)</b> pour une somme totale de <b>'.number_format($montantTtotal, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Vente par période client
    public function listeVenteByPeriodeClientPdf($debut,$fin,$client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->venteByPeriodeClient($debut,$fin,$client));
        $info_client = Client::find($client);
        return $pdf->stream('liste_ventes_du_client_'.$info_client->full_name_client.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function venteByPeriodeClient($debut,$fin,$client){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $info_client = Client::find($client);
        $datas = Vente::with('client','depot','caisse')
                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('ventes.*',DB::raw('sum(article_ventes.quantite*article_ventes.prix) as sommeTotale'),DB::raw('DATE_FORMAT(ventes.date_vente, "%d-%m-%Y") as date_ventes'))
                ->Where([['ventes.deleted_at', NULL],['ventes.client_id',$client]])
                ->whereDate('ventes.date_vente','>=',$date1)
                ->whereDate('ventes.date_vente','<=', $date2)
                ->groupBy('article_ventes.vente_id')
                ->orderBy('ventes.id','DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des ventes du client '.$info_client->full_name_client.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                       <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Facture</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Caisse</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Montant TTC</th>
                        </tr>
                    </div>';
        $montantTtotal = 0; $total=0;
       foreach ($datas as $data){
            $data->proformat == 0 ? $numero_fature = $data->numero_facture : $numero_fature = "Proforma";
            $montantTtotal = $montantTtotal + $data->sommeTotale;
            $total=$total+1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_ventes.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$numero_fature.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->caisse->libelle_caisse.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->sommeTotale, 0, ',', ' ').'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' vente(s)</b> pour une somme totale de <b>'.number_format($montantTtotal, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    //Article PDF
    public function listeArticlePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->article());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_articles.pdf');
    }
    public function article(){
        $datas = Article::with('categorie','sous_categorie')
                ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('articles.*','param_tvas.montant_tva',DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
                ->Where('articles.deleted_at', NULL)
                ->orderBy('description_article', 'ASC')
                ->groupBy('depot_articles.article_id')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des articles</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Catégorie</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Sous Catégorie</th>
                            <th cellspacing="0" border="2" width="5%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="5%" align="center">PA HT</th>
                            <th cellspacing="0" border="2" width="5%" align="center">PA TTC</th>
                            <th cellspacing="0" border="2" width="5%" align="center">PV HT</th>
                            <th cellspacing="0" border="2" width="5%" align="center">PV TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">TVA</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $data->param_tva_id !=null ? $tva = ($data->montant_tva*100).' %' : $tva = "Pas de TVA"; 
           $data->param_tva_id !=null ? $prix_achat_ht = ($data->prix_achat_ttc /($data->montant_tva+1)) : $prix_achat_ht = $data->prix_achat_ttc; 
           $data->param_tva_id !=null ? $prix_vente_ht = ($data->prix_vente_ttc_base /($data->montant_tva+1)) : $prix_vente_ht = $data->prix_vente_ttc_base; 
           $data->sous_categorie_id!= null ? $sous_categorie = $data->sous_categorie->libelle_sous_categorie : $sous_categorie= "";
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->categorie->libelle_categorie.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$sous_categorie.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->totalStock, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.round($prix_achat_ht,0).'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->prix_achat_ttc.'</td>
                            <td  cellspacing="0" border="2" align="center">'.round($prix_vente_ht,0).'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->prix_vente_ttc_base.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$tva.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' article(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Article par catégorie PDF
    public function listeArticleByCategoriePdf($categorie){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->articleByCategorie($categorie));
        $infos_categorie = Categorie::find($categorie);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_articles_de_categorie_'.$infos_categorie->libelle_categorie.'.pdf');
    }
    public function articleByCategorie($categorie){
        $infos_categorie = Categorie::find($categorie);
        $datas = Article::with('categorie','sous_categorie')
                ->join('param_tvas','param_tvas.id','=','articles.param_tva_id')
                ->join('depot_articles','depot_articles.article_id','=','articles.id')
                ->select('articles.*','param_tvas.montant_tva',DB::raw('sum(depot_articles.quantite_disponible) as totalStock'))
                ->Where([['articles.deleted_at', NULL],['articles.categorie_id',$categorie]])
                ->orderBy('description_article', 'ASC')
                ->groupBy('depot_articles.article_id')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" style="font-size:12px;"><h3 align="center"><u>Liste des articles de la catégorie '.$infos_categorie->libelle_categorie.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                         <tr>
                            <th cellspacing="0" border="2" width="10%" align="center">Code barre</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Sous Catégorie</th>
                            <th cellspacing="0" border="2" width="10%" align="center">En stock</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Prix Achat HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Prix Achat TTC</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Prix Vente HT</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Prix Vente TTC</th>
                            <th cellspacing="0" border="2" width="5%" align="center">TVA</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $data->param_tva_id !=null ? $tva = ($data->montant_tva*100).' %' : $tva = "Pas de TVA"; 
           $data->param_tva_id !=null ? $prix_achat_ht = ($data->prix_achat_ttc /($data->montant_tva+1)) : $prix_achat_ht = $data->prix_achat_ttc; 
           $data->param_tva_id !=null ? $prix_vente_ht = ($data->prix_vente_ttc_base /($data->montant_tva+1)) : $prix_vente_ht = $data->prix_vente_ttc_base; 
           $data->sous_categorie_id!= null ? $sous_categorie = $data->sous_categorie->libelle_sous_categorie : $sous_categorie= "";
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->code_barre.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->description_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$sous_categorie.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->totalStock, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.round($prix_achat_ht,0).'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->prix_achat_ttc.'</td>
                            <td  cellspacing="0" border="2" align="center">'.round($prix_vente_ht,0).'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->prix_vente_ttc_base.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$tva.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' article(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Fournisseur PDF
    public function listeFournisseurPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->fournisseur());
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('liste_fournisseurs.pdf');
    }
    public function fournisseur(){
        $datas = Fournisseur::with('nation')
                ->select('fournisseurs.*')
                ->Where('deleted_at', NULL)
                ->orderBy('full_name_fournisseur', 'ASC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des fournisseurs</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Fournisseur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Pays</th>
                            <th cellspacing="0" border="2" width="20%" align="center">E-mail</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Adresse</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->full_name_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->contact_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->nation->libelle_nation.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->email_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->adresse_fournisseur.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' fournisseur(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Fournisseur par pays PDF
    public function listeFournisseurByNationPdf($pays){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->fournisseurByNation($pays));
        $info_pays = Nation::find($pays);
        return $pdf->stream('liste_fournisseurs_de_'.$info_pays->libelle_nation.'.pdf');
    }
    public function fournisseurByNation($pays){
        $info_pays = Nation::find($pays);
        $datas = Fournisseur::with('nation')
                ->select('fournisseurs.*')
                ->Where([['deleted_at', NULL],['fournisseurs.nation_id',$pays]])
                ->orderBy('full_name_fournisseur', 'ASC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des fournisseurs du pays '.$info_pays->libelle_nation.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="30%" align="center">Fournisseur</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="25%" align="center">E-mail</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Adresse</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->full_name_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->contact_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->email_fournisseur.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->adresse_fournisseur.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' fournisseur(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
     //Client PDF
    public function listeClientPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->client());
        return $pdf->stream('liste_clients.pdf');
    }
    public function client(){
        $datas = Client::with('nation')
                ->select('clients.*')
                ->Where('deleted_at', NULL)
                ->orderBy('full_name_client', 'ASC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des clients</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="30%" align="center">Client</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Pays</th>
                            <th cellspacing="0" border="2" width="20%" align="center">E-mail</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Adresse</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->full_name_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->contact_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->nation->libelle_nation.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->email_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->adresse_client.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' client(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Client par pays PDF
    public function listeClientByNationPdf($pays){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->clientByNation($pays));
        $info_pays = Nation::find($pays);
        return $pdf->stream('liste_clients_de_'.$info_pays->libelle_nation.'.pdf');
    }
    public function clientByNation($pays){
        $info_pays = Nation::find($pays);
        $datas = Client::with('nation')
                ->select('clients.*')
                ->Where([['deleted_at', NULL],['clients.nation_id',$pays]])
                ->orderBy('full_name_client', 'ASC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des clients du pays '.$info_pays->libelle_nation.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="30%" align="center">Client</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="25%" align="center">E-mail</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Adresse</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->full_name_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->contact_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->email_client.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->adresse_client.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' client(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Dépôt PDF
    public function listeDepotPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->depot());
        return $pdf->stream('liste_depots.pdf');
    }
    public function depot(){
        $datas = DB::table('depots')->select('depots.*')->Where('deleted_at', NULL)->orderBy('depots.libelle_depot', 'ASC')->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des dépôts</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="50%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Adresse</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->adresse_depot.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;'.$data->contact_depot.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' dépôt(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Inventaire PDF
    public function listeInventairePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->inventaire());
        return $pdf->stream('liste_inventaires.pdf');
    }
    public function inventaire(){
        $datas = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where('inventaires.deleted_at', NULL)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des inventaires</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="50%" align="center">Période</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Dépôt</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_inventaires.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->libelle_inventaire.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' inventaire(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Inventaire par dépôt
    public function listeInventaireByDepotPdf($depot){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->inventaireByDepot($depot));
        $info_depots= Depot::find($depot);
        return $pdf->stream('liste_inventaires_du_depot_'.$info_depots->libelle_depot.'.pdf');
    }
    public function inventaireByDepot($depot){
        $info_depots = Depot::find($depot);
        $datas = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where([['inventaires.deleted_at', NULL],['inventaires.depot_id',$depot]])
                ->orderBy('inventaires.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des inventaires du dépôt '.$info_depots->libelle_depot.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="30%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="70%" align="center">Description</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_inventaires.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->libelle_inventaire.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' inventaire(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    } 

    //Inventaire par période
    public function listeInventaireByPeriodePdf($debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->inventaireByPeriode($debut,$fin));
        return $pdf->stream('liste_inventaires_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function inventaireByPeriode($debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where('inventaires.deleted_at', NULL)
                ->whereDate('inventaires.date_inventaire','>=',$date1)
                ->whereDate('inventaires.date_inventaire','<=', $date2)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des inventaires du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="20%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="50%" align="center">Description</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Dépôt</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_inventaires.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->libelle_inventaire.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' inventaire(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Inventaire par dépôt période
    public function listeInventaireByDepotPeriodePdf($depot,$debut,$fin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->inventaireByDepotPeriode($depot,$debut,$fin));
        $info_depots= Depot::find($depot);
        return $pdf->stream('liste_inventaires_du_depot_'.$info_depots->libelle_depot.'_du_'.$debut.'_au_'.$fin.'.pdf');
    }
    public function inventaireByDepotPeriode($depot,$debut,$fin){
        $date1 = Carbon::createFromFormat('d-m-Y', $debut);
        $date2 = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Inventaire::with('depot')
                ->select('inventaires.*',DB::raw('DATE_FORMAT(inventaires.date_inventaire, "%d-%m-%Y") as date_inventaires'))
                ->Where([['inventaires.deleted_at', NULL],['inventaires.depot_id',$depot]])
                ->whereDate('inventaires.date_inventaire','>=',$date1)
                ->whereDate('inventaires.date_inventaire','<=', $date2)
                ->orderBy('inventaires.id', 'DESC')
                ->get();
        $info_depots = Depot::find($depot);
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des inventaires du dépôt '.$info_depots->libelle_depot.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="25%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="75%" align="center">Description</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_inventaires.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->libelle_inventaire.'</td>
                        </tr>
                       ';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' inventaire(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    //Reçu de vente
    public function printRecuVentePdf($vente){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->printRecuVente($vente));
        return $pdf->stream('recu.pdf');
    }
    public function printRecuVente($vente){
        $datas = ArticleVente::with('article')
                ->join('articles','articles.id','=','article_ventes.article_id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                ->select('article_ventes.*','articles.libelle_article')
                ->Where([['article_ventes.deleted_at', NULL],['article_ventes.vente_id',$vente]])
                ->get();
        $outPut = '<html>
                    <head>
                        <style>
                             @page { size: 15cm 15cm landscape; }
                             header{
                                    position: absolute;
                                    top: -60px;
                                    left: 0px;
                                    align: center;
                                    height:30px;
                                }
                            .container-table{        
                                            margin:90px 0;
                                            width: 100%;
                                        }
                        </style>
                    </head>';
        $outPut.= '<body>
            <header>
                <p style="margin:0; position:left; font-size:10px;">
                    <img src="images/logo.jpg" width="100" height="80"><br/>
                    SOCIETE DE CONGELATION DE COTE D IVOIRE<br/>
                    Treichville Bvd VGE<br/>
                    +225 30 30 30 30 / 45 45 45 45
                </p>
        </header>';
         $outPut.='<table class="container-table" border="0" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="0" width="50%" align="center">Article</th>
                            <th cellspacing="0" border="0" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="0" width="20%" align="center">Prix U.</th>
                            <th cellspacing="0" border="0" width="20%" align="center">Montant</th>
                        </tr>';
         $montantHT = 0;
        foreach ($datas as $data){
           $montantHT = $montantHT + $data->prix*$data->quantite;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="0" align="center">'.$data->article->libelle_article.'</td>
                            <td  cellspacing="0" border="0" align="center">'.$data->quantite.'</td>
                            <td  cellspacing="0" border="0" align="center">'.number_format($data->prix, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="0" align="center">'.number_format($data->prix*$data->quantite, 0, ',', ' ').'</td>
                        </tr>';
       }
       $outPut.='</table>';
       $outPut.='<div class="row">
                    <div>Montant HT &nbsp;&nbsp;&nbsp;<b>'.number_format($montantHT,0, ',', ' ').'</b></div>
                    <div>TVA 18% &nbsp;&nbsp;&nbsp;<b>'.number_format($montantHT*0.18,0, ',', ' ').'</b></div>
                    <div>Montant TTC &nbsp;&nbsp;&nbsp;<b>'.number_format($montantHT*0.18+$montantHT,0, ',', ' ').'</b></div>
                </div>';
        $outPut.='</body></html>'; 
        return $outPut;
    }

    //Transfert stock PDF
    public function listeTransfertStockPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeTransfertStock());
        return $pdf->stream('liste_transfert_stock.pdf');
    }
    public function listeTransfertStock(){
        $datas = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where('transfert_stocks.deleted_at', NULL)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des transferts de stock</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt du départ</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt de destination</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_transferts.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_depart->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_arrivee->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' transfèrts(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    
    //Transfert stock par Article PDF
    public function listeTransfertStockByArticlePdf($article){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeTransfertStockByArticle($article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_transfert_stock_de_'.$info_article->description_article.'_.pdf');
    }
    public function listeTransfertStockByArticle($article){
        $info_article = Article::find($article);
        $datas = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where([['transfert_stocks.deleted_at', NULL],['transfert_stocks.article_id',$article]])
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des transferts de stock de '.$info_article->description_article.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt du départ</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt de destination</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_transferts.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_depart->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_arrivee->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' transfèrts(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Transfert stock par période PDF
    public function listeTransfertStockByPeriodePdf($dateDebut,$dateFin){
         $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeTransfertStockByPeriode($dateDebut,$dateFin));
        return $pdf->stream('liste_transfert_stock_du_'.$dateDebut.'_au_'.$dateFin.'_pdf');
    }
    public function listeTransfertStockByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where('transfert_stocks.deleted_at', NULL)
                ->whereDate('transfert_stocks.date_transfert','>=', $dateDebut)
                ->whereDate('transfert_stocks.date_transfert','<=', $dateFin)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des transferts de stock du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt du départ</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt de destination</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_transferts.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_depart->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_arrivee->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' transfèrts(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Transfert stock par article sur une période PDF
    public function listeTransfertStockByPeriodeArticlePdf($dateDebut,$dateFin,$article){
         $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeTransfertStockByPeriodeArticle($dateDebut,$dateFin,$article));
        $info_article = Article::find($article);
        return $pdf->stream('liste_transfert_stock_de_'.$info_article->description_article.'_du_'.$dateDebut.'_au_'.$dateFin.'_pdf');
    }
    public function listeTransfertStockByPeriodeArticle($debut,$fin,$article){
        $info_article = Article::find($article);
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = TransfertStock::with('article','depot_depart','depot_arrivee','unite')
                ->select('transfert_stocks.*',DB::raw('DATE_FORMAT(transfert_stocks.date_transfert, "%d-%m-%Y") as date_transferts'))
                ->Where([['transfert_stocks.deleted_at', NULL],['transfert_stocks.article_id',$article]])
                ->whereDate('transfert_stocks.date_transfert','>=', $dateDebut)
                ->whereDate('transfert_stocks.date_transfert','<=', $dateFin)
                ->orderBy('transfert_stocks.date_transfert', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des transferts de stock de '.$info_article->description_article.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt du départ</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Dépôt de destination</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_transferts.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_depart->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot_arrivee->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' transfèrts(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    //Déstockage PDF
    public function listeDestockagePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeDestockage());
        return $pdf->stream('liste_destockage.pdf');
    }
    public function listeDestockage(){
        $datas = Destockage::with('article','depot','unite')
                ->select('destockages.*',DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
                ->Where('destockages.deleted_at', NULL)
                ->orderBy('destockages.date_destockage', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste de déstockage</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Motif</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_destockages.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->motif.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' déstockage(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Déstockage par période PDF
    public function listeDestockageByPeriodePdf($dateDebut,$dateFin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeDestockageByPeriode($dateDebut,$dateFin));
        return $pdf->stream('liste_destockage_du_'.$dateDebut.'_au_'.$dateFin.'_.pdf');
    }
    public function listeDestockageByPeriode($dateDebuts,$dateFins){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $dateDebuts);
         $dateFin = Carbon::createFromFormat('d-m-Y', $dateFins);
        $datas = Destockage::with('article','depot','unite')
                ->select('destockages.*',DB::raw('DATE_FORMAT(destockages.date_destockage, "%d-%m-%Y") as date_destockages'))
                ->Where('destockages.deleted_at', NULL)
                ->whereDate('destockages.date_destockage','>=', $dateDebut)
                ->whereDate('destockages.date_destockage','<=', $dateFin)
                ->orderBy('destockages.date_destockage', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste de déstockage du '.$dateDebuts.' au '.$dateFins.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Dépôt</th>
                            <th cellspacing="0" border="2" width="25%" align="center">Article</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Lot</th>
                            <th cellspacing="0" border="2" width="10%" align="center">Qté</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Motif</th>
                        </tr>
                    </div>';
         $total = 0; 
       foreach ($datas as $data){
           $total = $total + 1;
            $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_destockages.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->depot->libelle_depot.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->article->description_article.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->unite->libelle_unite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->quantite.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->motif.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' déstockage(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Règlement PDF
    public function listeReglementPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglement());
        return $pdf->stream('liste_reglements.pdf');
    }
    public function listeReglement(){
        $datas = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where('reglements.deleted_at', NULL)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des règlements</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">N° virement ou ch&egrave;que</th>
                        </tr>
                    </div>';
         $total = 0; $concerne = "Caisse"; $objet = "Objet";
       foreach ($datas as $data){
           $total = $total + 1;
           if($data->id_client !=null){ $concerne = "Client ".$data->full_name_client;}
           if($data->commande_id !=null){ $concerne = "Fournisseur ".$data->full_name_fournisseur;}
           if($data->vente_id !=null){ $objet = "Facture N° ".$data->numero_facture;}
           if($data->commande_id !=null){ $objet = "Commande N° ".$data->numero_bon;}
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->moyen_reglement->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->montant_reglement, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$objet.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->numero_cheque_virement.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' règlement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Règlement par période
    public function listeReglementByPeriodePdf($dateDebut,$dateFin){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementByPeriode($dateDebut,$dateFin));
        return $pdf->stream('liste_reglements_du_'.$dateDebut.'_au_'.$dateFin.'_.pdf');
    }
    public function listeReglementByPeriode($debut,$fin){
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where('reglements.deleted_at', NULL)
                ->whereDate('reglements.date_reglement','>=',$dateDebut)
                ->whereDate('reglements.date_reglement','<=',$dateFin)
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des règlements du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Concerne</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">N° virement ou ch&egrave;que</th>
                        </tr>
                    </div>';
         $total = 0; $concerne = "Caisse"; $objet = "Objet";
       foreach ($datas as $data){
           $total = $total + 1;
           if($data->id_client !=null){ $concerne = "Client ".$data->full_name_client;}
           if($data->commande_id !=null){ $concerne = "Fournisseur ".$data->full_name_fournisseur;}
           if($data->vente_id !=null){ $objet = "Facture N° ".$data->numero_facture;}
           if($data->commande_id !=null){ $objet = "Commande N° ".$data->numero_bon;}
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->moyen_reglement->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->montant_reglement, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.$concerne.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$objet.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->numero_cheque_virement.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' règlement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Règlement par Fournisseur PDF
    public function listeReglementByFournisseurPdf($fournisseur){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementByFournisseur($fournisseur));
        $info_fournisseur = Fournisseur::find($fournisseur);
        return $pdf->stream('liste_reglements_du_fournisseur_'.$info_fournisseur->full_name_fournisseur.'_.pdf');
    }
    public function listeReglementByFournisseur($fournisseur){
        $info_fournisseur = Fournisseur::find($fournisseur);
        $datas = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['fournisseurs.id',$fournisseur]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des règlements du fournisseur '.$info_fournisseur->full_name_fournisseur.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">N° virement ou ch&egrave;que</th>
                        </tr>
                    </div>';
         $total = 0; $objet = "Objet";
       foreach ($datas as $data){
           $total = $total + 1;
           if($data->vente_id !=null){ $objet = "Facture N° ".$data->numero_facture;}
           if($data->commande_id !=null){ $objet = "Commande N° ".$data->numero_bon;}
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->moyen_reglement->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->montant_reglement, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.$objet.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->numero_cheque_virement.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' règlement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Règlement par Client PDF
    public function listeReglementByClientPdf($client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementByClient($client));
        $info_client = Client::find($client);
        return $pdf->stream('liste_reglements_du_client_'.$info_client->full_name_client.'_.pdf');
    }
    public function listeReglementByClient($client){
        $info_client = Client::find($client);
        $datas = Reglement::with('moyen_reglement')
                ->leftJoin('ventes','ventes.id','=','reglements.vente_id')
                ->leftJoin('clients','clients.id','=','ventes.client_id')
                ->leftJoin('bon_commandes','bon_commandes.id','=','reglements.commande_id')
                ->leftJoin('fournisseurs','fournisseurs.id','=','bon_commandes.fournisseur_id')
                ->select('reglements.*','ventes.client_id as id_client','bon_commandes.numero_bon','fournisseurs.full_name_fournisseur', 'ventes.numero_facture','clients.full_name_client',DB::raw('DATE_FORMAT(reglements.date_reglement, "%d-%m-%Y") as date_reglements'))
                ->Where([['reglements.deleted_at', NULL],['clients.id',$client]])
                ->orderBy('reglements.id', 'DESC')
                ->get();
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des règlements du client '.$info_client->full_name_client.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">N° virement ou ch&egrave;que</th>
                        </tr>
                    </div>';
         $total = 0; $objet = "Objet";
       foreach ($datas as $data){
           $total = $total + 1;
           if($data->vente_id !=null){ $objet = "Facture N° ".$data->numero_facture;}
           if($data->commande_id !=null){ $objet = "Commande N° ".$data->numero_bon;}
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->moyen_reglement->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->montant_reglement, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.$objet.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->numero_cheque_virement.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' règlement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Règlement du fournisseur sur une période
    public function listeReglementByPeriodeFournisseurPdf($debut,$fin,$fournisseur){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementByPeriodeFournisseur($debut,$fin,$fournisseur));
        $info_fournisseur = Fournisseur::find($fournisseur);
        return $pdf->stream('liste_reglements_du_fournisseur_'.$info_fournisseur->full_name_fournisseur.'_du_'.$debut.'_au_'.$fin.'_.pdf');
    }
    public function listeReglementByPeriodeFournisseur($debut,$fin,$fournisseur){
        $info_fournisseur = Fournisseur::find($fournisseur);
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Reglement::with('moyen_reglement')
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
        $outPut = $this->header();
        $outPut .= '<div class="container-table" style="font-size:12px;"><h3 align="center"><u>Liste des règlements du fournisseur '.$info_fournisseur->full_name_fournisseur.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">N° virement ou ch&egrave;que</th>
                        </tr>
                    </div>';
         $total = 0; $objet = "Objet";
       foreach ($datas as $data){
           $total = $total + 1;
           if($data->vente_id !=null){ $objet = "Facture N° ".$data->numero_facture;}
           if($data->commande_id !=null){ $objet = "Commande N° ".$data->numero_bon;}
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->moyen_reglement->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->montant_reglement, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.$objet.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->numero_cheque_virement.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' règlement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    //Règlement du client sur une période
    public function listeReglementByPeriodeClientPdf($debut,$fin,$client){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeReglementByPeriodeClient($debut,$fin,$client));
        $info_client = Client::find($client);
        return $pdf->stream('liste_reglements_du_fournisseur_'.$info_client->full_name_client.'_du_'.$debut.'_au_'.$fin.'_.pdf');
    }
    public function listeReglementByPeriodeClient($debut,$fin,$client){
        $info_client = Client::find($client);
        $dateDebut = Carbon::createFromFormat('d-m-Y', $debut);
        $dateFin = Carbon::createFromFormat('d-m-Y', $fin);
        $datas = Reglement::with('moyen_reglement')
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
        $outPut = $this->header();
        $outPut .= '<div class="container-table" font-size:12px;><h3 align="center"><u>Liste des règlements du client '.$info_client->full_name_client.' du '.$debut.' au '.$fin.'</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="15%" align="center">Date</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Moyen de payement</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Montant</th>
                            <th cellspacing="0" border="2" width="15%" align="center">Objet</th>
                            <th cellspacing="0" border="2" width="20%" align="center">N° virement ou ch&egrave;que</th>
                        </tr>
                    </div>';
         $total = 0; $objet = "Objet";
       foreach ($datas as $data){
           $total = $total + 1;
           if($data->vente_id !=null){ $objet = "Facture N° ".$data->numero_facture;}
           if($data->commande_id !=null){ $objet = "Commande N° ".$data->numero_bon;}
           $outPut .= '<tr>
                            <td  cellspacing="0" border="2" align="center">'.$data->date_reglements.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->moyen_reglement->libelle_moyen_reglement.'</td>
                            <td  cellspacing="0" border="2" align="center">'.number_format($data->montant_reglement, 0, ',', ' ').'</td>
                            <td  cellspacing="0" border="2" align="center">'.$objet.'</td>
                            <td  cellspacing="0" border="2" align="center">'.$data->numero_cheque_virement.'</td>
                        </tr>';
       }
       
        $outPut .='</table>';
        $outPut.='<br/> Nombre totale:<b> '.number_format($total, 0, ',', ' ').' règlement(s)</b>';
        $outPut.= $this->footer();
        return $outPut;
    }
    
    public function chiffreAffaireClientPdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->chiffreAffaireClient());
        return $pdf->stream('liste_chiffre_affaire_by_client.pdf');
    }
    public function chiffreAffaireClient(){
         $datas = Vente::where([['ventes.deleted_at',null],['ventes.proformat',0]])
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->join('clients','clients.id','=','ventes.client_id')
                            ->select('clients.full_name_client','clients.contact_client',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->groupBy('ventes.client_id')
                            ->orderBy('sommeTotale','DESC')
                            ->get();
        
        $outPut = $this->header();
        $montantTotal = 0;
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste chiffre affaires par client</h3>
                    <table border="2" cellspacing="0" width="100%">
                        <tr>
                            <th cellspacing="0" border="2" width="50%" align="center">Client</th>
                            <th cellspacing="0" border="2" width="20%" align="center">Contact</th>
                            <th cellspacing="0" border="2" width="30%" align="center">Chiffre affaires</th>
                        </tr>';
        foreach ($datas as $data){
            $montantTotal = $montantTotal + $data->sommeTotale;
           $outPut .= '
                        <tr>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->full_name_client.'</td>
                            <td  cellspacing="0" border="2" align="left">&nbsp;&nbsp;'.$data->contact_client.'</td>
                            <td  cellspacing="0" border="2" align="right">'.number_format($data->sommeTotale, 0, ',', ' ').'&nbsp;&nbsp;</td>
                        </tr>
                       ';
        }
        $outPut .='</table></div>';
        $outPut.='<br/> Montant totale:<b> '.number_format($montantTotal, 0, ',', ' ').' F CFA</b>';
        $outPut.= $this->footer();
        return $outPut;
    }

    public function listeArticleVenteByQuantitePdf(){
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadHTML($this->listeArticleVenteByQuantite());
        return $pdf->stream('liste_article_vendu_par_qte.pdf');
    }
    
    public function listeArticleVenteByQuantite(){
         $datas = Vente::where([['ventes.deleted_at',null],['ventes.proformat',0]])
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->join('articles','articles.id','=','article_ventes.article_id')
                                ->select('articles.description_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.quantite) as qteTotale'))
                                ->groupBy('article_ventes.article_id')
                                ->orderBy('qteTotale','DESC')
                                ->get();
        
        $outPut = $this->header();
        $outPut .= '<div class="container-table"><h3 align="center"><u>Liste des articles vendus par quantité</h3>
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
                            <td  cellspacing="0" border="2" align="center">'.$data->qteTotale.'</td>
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
