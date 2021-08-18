<?php

namespace App\Http\Controllers;

use App\Models\Boutique\BonCommande;
use App\Models\Boutique\DepotArticle;
use App\Models\Boutique\Vente;
use App\Models\Canal\Agence;
use App\Models\Parametre\Article;
use App\Models\Parametre\Client;
use App\Models\Parametre\Depot;
use App\Models\Parametre\Fournisseur;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function view;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {   $now = date("Y-m-d");
        
        if(Auth::user()->role=="Agence"){
            $agence_id = Auth::user()->agence_id;
            $agence = Agence::find($agence_id);
            $reference_agence = $agence->libelle_agence;
        }
        
        $get_configuration_infos = \App\Helpers\ConfigurationHelper\Configuration::get_configuration_infos(1);        
        
        $clients = Client::where('deleted_at',null)->get();
        $depots = Depot::where('deleted_at',null)->get();
        $articles = Article::where('deleted_at',null)->get();
        $fournisseurs = Fournisseur::where('deleted_at',null)->get();
       
        $articleRupture = Article::with('categorie','sous_categorie')
                            ->join('depot_articles','depot_articles.article_id','=','articles.id')
                            ->join('depots','depots.id','=','depot_articles.depot_id')
                            ->select('articles.*','depots.libelle_depot','depot_articles.quantite_disponible as totalStock')
                            ->Where([['articles.deleted_at', NULL],['articles.stockable',1]])
                            ->whereRaw('articles.stock_mini >= depot_articles.quantite_disponible')
                            ->orderBy('totalStock', 'ASC')
                            ->get()->take(10);
       
        $commande_encours = BonCommande::with('fournisseur')
                            ->join('article_bons','article_bons.bon_commande_id','=','bon_commandes.id')
                            ->select('bon_commandes.*',DB::raw('sum(article_bons.quantite_demande*article_bons.prix_article) as montantBon'),DB::raw('DATE_FORMAT(bon_commandes.date_bon_commande, "%d-%m-%Y") as date_bon_commandes'))
                            ->Where([['bon_commandes.deleted_at', NULL],['livrer',0]])
                            ->orderBy('bon_commandes.date_bon_commande', 'DESC')
                            ->groupBy('bon_commandes.id')
                            ->get();
         if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur'){
            $caisse_ouvertes = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('users','users.id','=','caisse_ouvertes.user_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                        ->select('caisse_ouvertes.*','users.full_name', 'caisses.libelle_caisse', 'depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null]])
                                        ->groupBy('caisse_ouvertes.id')
                                        ->orderBy('caisse_ouvertes.date_ouverture','DESC')
                                        ->get();
        }else{
            $caisse_ouvertes = Vente::with('depot','caisse_ouverte')
                                        ->join('caisse_ouvertes', 'caisse_ouvertes.id', '=', 'ventes.caisse_ouverte_id')->where('caisse_ouvertes.date_fermeture',null)
                                        ->join('caisses','caisses.id','=','caisse_ouvertes.caisse_id')
                                        ->join('depots','depots.id','=','ventes.depot_id')
                                        ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where('article_ventes.deleted_at', NULL)
                                        ->select('caisse_ouvertes.*','depots.libelle_depot',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('DATE_FORMAT(caisse_ouvertes.date_ouverture, "%d-%m-%Y à %H:%i:%s") as date_ouvertures'))
                                        ->Where([['ventes.deleted_at', NULL],['ventes.client_id',null],['caisses.depot_id',Auth::user()->depot_id]])
                                        ->groupBy('article_ventes.vente_id')
                                        ->orderBy('caisse_ouvertes.date_ouverture','DESC')
                                        ->get();
          
        }
        
        $tenBesteClients = Vente::where([['ventes.deleted_at',null],['ventes.proformat',0]])
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->join('clients','clients.id','=','ventes.client_id')
                            ->select('clients.full_name_client','clients.contact_client',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->groupBy('ventes.client_id')
                            ->orderBy('sommeTotale','DESC')
                            ->take(5)->get();
        $tenBadClients = Vente::where([['ventes.deleted_at',null],['ventes.proformat',0]])
                            ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                            ->join('clients','clients.id','=','ventes.client_id')
                            ->select('clients.full_name_client','clients.contact_client',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                            ->groupBy('ventes.client_id')
                            ->orderBy('sommeTotale','ASC')
                            ->take(5)->get();   
        $articlesPlusVendus = Vente::where([['ventes.deleted_at',null],['ventes.proformat',0]])
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->join('articles','articles.id','=','article_ventes.article_id')
                                ->select('articles.description_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.quantite) as qteTotale'))
                                ->groupBy('article_ventes.article_id')
                                ->orderBy('qteTotale','DESC')
                                ->take(5)->get();
         $articlesMoinsVendus = Vente::where([['ventes.deleted_at',null],['ventes.proformat',0]])
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->join('articles','articles.id','=','article_ventes.article_id')
                                ->select('articles.description_article',DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'),DB::raw('sum(article_ventes.quantite) as qteTotale'))
                                ->groupBy('article_ventes.article_id')
                                ->orderBy('qteTotale','ASC')
                                ->take(5)->get();
        $clientsPlusEndettes = Vente::where([['ventes.deleted_at', NULL],['ventes.proformat',0]])
                                ->join('clients','clients.id','=','ventes.client_id')
                                ->join('article_ventes','article_ventes.vente_id','=','ventes.id')->Where([['article_ventes.deleted_at', NULL],['article_ventes.retourne',0]])
                                ->select('clients.full_name_client','clients.contact_client','clients.adresse_client',DB::raw('sum(ventes.acompte_facture) as accompteTotale'),DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne) as sommeTotale'))
                                ->groupBy('ventes.client_id')
                                ->orderBy(DB::raw('sum(article_ventes.quantite*article_ventes.prix-article_ventes.remise_sur_ligne)-sum(ventes.acompte_facture)'),'DESC')
                                ->take(5)->get();
        
        $article_envoie_peremptions = DepotArticle::where([['depot_articles.deleted_at',null],['depot_articles.date_peremption','>',$now]])
                                    ->join('depots','depots.id','=','depot_articles.depot_id')
                                    ->join('articles','articles.id','=','depot_articles.article_id')->where('articles.stockable',1)
                                    ->join('unites','unites.id','=','depot_articles.unite_id')
                                    ->select('depot_articles.date_peremption', 'articles.description_article','depots.libelle_depot','unites.libelle_unite',DB::raw('DATE_FORMAT(depot_articles.date_peremption, "%d-%m-%Y") as date_peremptions'))
                                    ->orderBy('depot_articles.date_peremption','ASC')
                                    ->take(5)->get();

        $menuPrincipal = "Accueil";
        if(Auth::user()->role=="Agence"){
        $titleControlleur = "Tableau de bord agence ".$reference_agence;
        }else{
            $titleControlleur = "Tableau de bord";
        }
        $btnModalAjout = "FALSE";
        return view('home',compact('get_configuration_infos', 'article_envoie_peremptions', 'clientsPlusEndettes', 'articlesMoinsVendus','articlesPlusVendus','tenBesteClients','tenBadClients', 'caisse_ouvertes','clients','fournisseurs','commande_encours', 'articles','depots','articleRupture', 'menuPrincipal', 'titleControlleur', 'btnModalAjout'));
    }
}
