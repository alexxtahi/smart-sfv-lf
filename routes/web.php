<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
   return view('auth.login');
});

Auth::routes();
Route::get('/confirmer_compte/{id}/{token}', 'Auth\RegisterController@confirmationCompte');
Route::post('/update_password', 'Auth\RegisterController@updatePassword')->name('update_password');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/configuration', 'ConfigurationController@index')->name('configuration')->middleware('auth');
Route::post('/configuration/store', 'ConfigurationController@store')->name('configuration.store')->middleware('auth');
Route::get('/configuration/infos-update', 'ConfigurationController@show')->name('configuration.infos-update')->middleware('auth');
Route::put('/configuration/update', 'ConfigurationController@update')->name('configuration.update')->middleware('auth');

//les routes du module Parametre 
Route::namespace('Parametre')->middleware('auth')->name('parametre.')->prefix('parametre')->group(function () {
    //Route resources 
    Route::resource('categories', 'CategorieController');
    Route::resource('nations', 'NationController');
    Route::resource('depots', 'DepotController');
    Route::resource('fournisseurs', 'FournisseurController');
    Route::resource('clients', 'ClientController');
    Route::resource('articles', 'ArticleController');
    Route::resource('moyen-reglements', 'MoyenReglementController');
    Route::resource('casiers', 'CasierController');
    Route::resource('rangees', 'RangeeController');
    Route::resource('rayons', 'RayonController');
    Route::resource('unites', 'UniteController');
    Route::resource('param-tva', 'ParamTvaController');
    Route::resource('sous-categories', 'SousCategorieController');
    Route::resource('regimes', 'RegimeController');
    Route::resource('banques', 'BanqueController');
    Route::resource('caisses', 'CaisseController');
    Route::resource('divers', 'DiversController');
    Route::resource('tailles', 'TailleController');
    Route::resource('categorie-depenses', 'CategorieDepenseController');
   
    //Route particulières 
    Route::post('update-article', 'ArticleController@updateArticle')->name('update-article');
    
    //Route pour les listes dans boostrap table  
    Route::get('liste-clients', 'ClientController@listeClient')->name('liste-clients');
    Route::get('liste-fournisseurs', 'FournisseurController@listeFournisseur')->name('liste-fournisseurs');
    Route::get('liste-depots', 'DepotController@listeDepot')->name('liste-depots');
    Route::get('liste-categories', 'CategorieController@listeCategorie')->name('liste-categories');
    Route::get('liste-nations', 'NationController@listeNation')->name('liste-nations');
    Route::get('liste-articles', 'ArticleController@listeArticle')->name('liste-articles');
    Route::get('liste-moyen-reglements', 'MoyenReglementController@listeMoyenReglement')->name('liste-moyen-reglements');
    Route::get('liste-casiers', 'CasierController@listeCasier')->name('liste-casiers');
    Route::get('liste-rangees', 'RangeeController@listeRangee')->name('liste-rangees');
    Route::get('liste-rayons', 'RayonController@listeRayon')->name('liste-rayons');
    Route::get('liste-unites', 'UniteController@listeUnite')->name('liste-unites');
    Route::get('liste-param-tva', 'ParamTvaController@listeParamTva')->name('liste-param-tva');
    Route::get('liste-sous-categories', 'SousCategorieController@listeSousCategorie')->name('liste-sous-categories');
    Route::get('liste-regimes', 'RegimeController@listeRegime')->name('liste-regimes');
    Route::get('liste-banques', 'BanqueController@listeBanque')->name('liste-banques');
    Route::get('liste-caisses', 'CaisseController@listeCaisse')->name('liste-caisses');
    Route::get('liste-divers', 'DiversController@listeDivers')->name('liste-divers');
    Route::get('liste-tailles', 'TailleController@listeTaille')->name('liste-tailles');
    Route::get('liste-categorie-depenses', 'CategorieDepenseController@listeCategorieDepense')->name('liste-categorie-depenses');

        
    //Routes parametrées 
    Route::get('find-fournisseur-by-commande/{commande}', 'FournisseurController@findFournisseurByCommande');
    Route::get('find-param-tva/{id}', 'ParamTvaController@findParamTva');
    Route::get('find-article/{id}', 'ArticleController@findArticle');
    Route::get('liste-clients-by-nation/{nation}', 'ClientController@listeClientByNation');
    Route::get('find-client-by-id/{id}', 'ClientController@findClientById');
    Route::get('liste-fournisseurs-by-nation/{nation}', 'FournisseurController@listeFournisseurByNation');
    Route::get('liste-articles-by-categorie/{categorie}', 'ArticleController@listeArticleByCategorie');
    Route::get('liste-articles-by-fournisseur/{fournisseur}', 'ArticleController@listeArticleByFournisseur');
    Route::get('article-by-code/{code}', 'ArticleController@articleByCode');
    Route::get('article-by-name/{name}', 'ArticleController@articleByName');
    Route::get('find-categorie-by-article/{article}', 'CategorieController@findCategorieByArticle');
    Route::get('inventaire-liste-articles-by-depot/{depot}', 'ArticleController@inventaireListeArticlesByDepot');
    Route::get('find-client-by-vente/{vente}', 'ClientController@findClientByVente');
    Route::get('last-client', 'ClientController@lastClient');
    Route::get('find-fournisseur-by-approvisionnement/{approvisionnement}', 'FournisseurController@findFournisseurByApprovisionnement');
    Route::get('liste-articles-by-depots/{depot}', 'ArticleController@listeArticleDepots');
    Route::get('liste-article-by-depot-categorie/{depot}/{categorie}', 'ArticleController@listeArticleByDepotCategorie');
    Route::get('liste-sous-famille-by-categorie/{categorie}', 'SousCategorieController@listeSousFamilleByCategorie');
    Route::get('liste-caisses-by-depot/{depot}', 'CaisseController@listeCaisseByDepot');
    Route::get('categorie-articles-by-depot/{depot}', 'CategorieController@categorieArticleByDepot');
    Route::get('sous-categorie-articles-by-depot/{categorie}/{depot}', 'SousCategorieController@sousCategorieArticlesByDepot');
    Route::get('sous-categorie-id-articles-by-depot/{id}/{depot}', 'SousCategorieController@sousCategorieIdArticlesByDepot');
    Route::get('inventaire-liste-articles-categorie-in-depot/{categorie}/{depot}', 'ArticleController@inventaireListeArticlesCategorieInDepot');
    Route::get('inventaire-liste-articles-sous-categorie-in-depot/{sous_categorie}/{depot}', 'ArticleController@inventaireListeArticlesSousCategorieInDepot');
    Route::get('liste-articles-grouper-by-sous-categorie-in-depot/{sous_categorie}/{depot}', 'ArticleController@listeArticleGrouperBySousCategorieInDepot');
    Route::get('inventaire-liste-articles-code_barre-in-depot/{code}/{depot}', 'ArticleController@inventaireListeArticlesCodeBarreInDepot');
    Route::get('inventaire-liste-articles-id-in-depot/{id}/{depot}', 'ArticleController@inventaireListeArticlesIdInDepot');
    Route::get('liste-articles-grouper-code_barre-in-depot/{code}/{depot}', 'ArticleController@listeArticlesGroupeByCodeBarreInDepot');
    Route::get('liste-articles-grouper-id-in-depot/{id}/{depot}', 'ArticleController@listeArticlesGroupeByIdInDepot');
    Route::get('liste-caisses-fermees-by-depot/{depot}', 'CaisseController@listeCaissesFermeesByDepot');
    Route::get('find-caisse-by-id/{id}', 'CaisseController@findCaisseById');
    Route::get('get-all-doit-client/{client}', 'ClientController@getAllDoitClient');
    Route::get('fiche-client/{client}', 'ClientController@ficheClient');
    
    //Informations des achats du client 
    Route::get('liste-achats-client/{client}', 'ClientController@listeAchatsClient');
    Route::get('liste-articles-plus-achetes/{client}', 'ClientController@listeArticlePlusAchatsClient');
    Route::get('liste-achats-client-by-facture/{facture}/{client}', 'ClientController@listeAchatsClientByFacture');
    Route::get('liste-achats-client-by-periode/{debut}/{fin}/{client}', 'ClientController@listeAchatsClientByPeriode');
    //règlements 
    Route::get('liste-reglements-client/{client}', 'ClientController@listeReglementsClient');
    Route::get('liste-reglements-client-by-periode/{debut}/{fin}/{client}', 'ClientController@listeReglementsClientByPeriode');
     
    //Etats du fiche client 
    Route::get('liste-achats-client-pdf/{client}', 'ClientController@listeAchatsClientPdf');
    Route::get('liste-articles-plus-achetes-pdf/{client}', 'ClientController@listeArticlesPlusAchetesPdf');
    Route::get('liste-achats-client-by-periode-pdf/{debut}/{fin}/{client}', 'ClientController@listeAchatsClientByPeriodePdf');
    Route::get('liste-reglements-client-pdf/{client}', 'ClientController@listeReglementsClientPdf');
    Route::get('liste-reglements-client-by-periode-pdf/{debut}/{fin}/{client}', 'ClientController@listeReglementsClientByPeriodePdf');
});

//les routes du module Boutique
Route::namespace('Boutique')->middleware('auth')->name('boutique.')->prefix('boutique')->group(function () {
    //Route resources 
    Route::resource('approvisionnements', 'ApprovisionnementController');
    Route::resource('ventes', 'VenteController');
    Route::resource('articles-vente', 'ArticleVenteController');
    Route::resource('approvisionnements-articles', 'ArticleApprovisionnementController');
    Route::resource('depot-articles', 'DepotArticleController');
    Route::resource('reglements', 'ReglementController');
    Route::resource('inventaires', 'InventaireController');
    Route::resource('detail-inventaires', 'DetailInventaireController');
    Route::resource('remises', 'RemiseController');
    Route::resource('destockages', 'DestockageController');
    Route::resource('transfert-stocks', 'TransfertStockController');
    Route::resource('bon-commandes', 'BonCommandeController');
    Route::resource('articles-bon', 'ArticleBonController');
    Route::resource('promotions', 'PromotionsController');
    Route::resource('caisse-ouverte', 'CaisseOuverteController');
    Route::resource('article-transferts', 'ArticleTransfertController');
    Route::resource('article-destockers', 'ArticleDestockerController');
    Route::resource('operations', 'OperationController');
    Route::resource('mouvements-stocks', 'MouvementStockController');
    Route::resource('retour-articles', 'RetourArticleController');
    Route::resource('article-retournes', 'ArticleRetourneController');
    Route::resource('depenses', 'DepenseController');
    Route::resource('declarations', 'TvaDeclareeController');
    
    
    //Route pour les listes dans boostrap table 
    Route::get('liste-approvisionnements', 'ApprovisionnementController@listeApprovisionnement')->name('liste-approvisionnements');
    Route::get('liste-ventes', 'VenteController@listeVente')->name('liste-ventes');
    Route::get('liste-ventes-caisse', 'VenteController@listeVentesCaisse')->name('liste-ventes-caisse');
    Route::get('liste-reglements', 'ReglementController@listeReglement')->name('liste-reglements');
    Route::get('liste-inventaires', 'InventaireController@listeInventaire')->name('liste-inventaires');
    Route::get('liste-remises/{vente_id}', 'RemiseController@listeRemise')->name('liste-remises');
    Route::get('liste-destockages', 'DestockageController@listeDestockage')->name('liste-destockages');
    Route::get('liste-transferts-stocks', 'TransfertStockController@listeTransfertStock')->name('liste-transferts-stocks');
    Route::get('liste-bon-commandes', 'BonCommandeController@listeBonCommande')->name('liste-bon-commandes');
    Route::get('liste-reception-commande', 'BonCommandeController@listeReceptionCommande')->name('liste-reception-commande');
    Route::get('liste-promotions', 'PromotionsController@listePromotion')->name('liste-promotions');
    Route::get('liste-ventes-divers', 'VenteController@listeVentesDivers')->name('liste-ventes-divers');
    Route::get('liste-operations', 'OperationController@listeOperation')->name('operations');
    Route::get('liste-mouvements-stocks', 'MouvementStockController@listeMouvementStock')->name('liste-mouvements-stocks');
    Route::get('liste-mouvements-stocks-grouper', 'MouvementStockController@listeMouvementStockGrouper')->name('liste-mouvements-stocks-grouper');
    Route::get('liste-retour-articles', 'RetourArticleController@listeRetourArticle')->name('retour-articles');
    Route::get('liste-depenses', 'DepenseController@listeDepense')->name('liste-depenses');
    Route::get('liste-declarations', 'TvaDeclareeController@listeDeclaration')->name('liste-declarations');
    Route::get('liste-retour-materiel', 'RetourArticleController@listeRetourMateriel');
    
    //Route particulières 
    Route::post('update-retour-article', 'RetourArticleController@updateRetourArticle')->name('update-retour-article');
    Route::post('update-approvisionnement', 'ApprovisionnementController@updateApprovisionnement')->name('update-approvisionnement');
    Route::post('update-reglement', 'ReglementController@updateReglement')->name('update-reglement');
    Route::post('update-vente', 'VenteController@updateVente')->name('update-vente');
    Route::get('ponit-caisse', 'VenteController@vuPointVente')->name('ponit-caisse');
    Route::get('update-remise', 'RemiseController@updateRemise')->name('update-remise');
    Route::get('reception-commande', 'BonCommandeController@vuReceptionCommande')->name('reception-commande');
    Route::post('editer-commande', 'BonCommandeController@editerCommande')->name('editer-commande');
    Route::get('point-caisse-admin', 'VenteController@pointCaisseAdmin')->name('point-caisse-admin');
    Route::post('ponit-caisse-vu-by-admin-gerant', 'VenteController@ponitCaisseVuByAdminGerant')->name('ponit-caisse-vu-by-admin-gerant');
    Route::post('update-transfert-stocks', 'TransfertStockController@updateTransfertStocks')->name('update-transfert-stocks');
    Route::post('update-destockage', 'DestockageController@updateDestockage')->name('update-destockage');
    Route::post('update-inventaire', 'InventaireController@updateInventaire')->name('update-inventaire');
    Route::post('vu-liste-article-by-unite-in-depot', 'DepotArticleController@vuArticleByUniteInDepot')->name('vu-liste-article-by-unite-in-depot');
    Route::get('point-vente', 'VenteController@vueVente')->name('point-vente');
    Route::get('vente-divers', 'VenteController@vueVenteDivers')->name('vente-divers');
    Route::get('operation-caisses', 'OperationController@vueOperationCaisse')->name('operation-caisses');
    Route::get('operation-caisses-admin', 'OperationController@vueOperationCaisseAdmin')->name('operation-caisses-admin');
    Route::post('operation-caisses-admin', 'OperationController@operationCaisseAdmin')->name('operation-caisses-admin');
    Route::post('femeture-caisse', 'CaisseOuverteController@fermetureCaisse')->name('femeture-caisse');
    Route::get('mouvement-stocks-grouper', 'MouvementStockController@vueMouvementStockGrouper')->name('mouvement-stocks-grouper');
    Route::get('ticket-declare', 'TvaDeclareeController@index')->name('ticket-declare');
    Route::get('retour-materiel', 'RetourArticleController@vueRetourMateriel')->name('retour-materiel');
        
    
    //Routes parametrées 
    Route::get('liste-articles-approvisionnes/{approvisionnement}', 'ArticleApprovisionnementController@listeArticlesApprovisionnes');
    Route::get('liste-approvisionnements-by-fournisseur/{fournisseur}', 'ApprovisionnementController@listeApprovisionnementsByBournisseur');
    Route::get('liste-approvisionnements-by-depot/{depot}', 'ApprovisionnementController@listeApprovisionnementsByDepot');
    Route::get('liste-approvisionnements-by-date/{date}', 'ApprovisionnementController@listeApprovisionnementsByDate');
    Route::get('find-article-in-depot/{article}/{depot}', 'DepotArticleController@findArticleInDepot');
    Route::get('liste-articles-vente/{vente}', 'ArticleVenteController@listeArticlesVente');
    Route::get('find-one-article-on-vente/{vente}/{article}', 'ArticleVenteController@findOneArticleOnVente');
    Route::get('liste-articles-vente-divers/{vente}', 'ArticleVenteController@listeArticlesVenteDivers');
    Route::get('liste-ventes-by-client/{client}', 'VenteController@listeVentesByClient');
    Route::get('get-all-facture-client/{client}', 'VenteController@getAllFactureClient');
    Route::get('liste-ventes-by-depot/{depot}', 'VenteController@listeVentesByDepot');
    Route::get('liste-ventes-by-date/{date}', 'VenteController@listeVentesByDate');
    Route::get('find-vente-by-id/{id}', 'VenteController@findVenteById');
    Route::get('find-one-vente/{id}', 'VenteController@findOneVente');
    Route::get('find-article-sur-vente-by-code-barre/{code_barr}/{vente}', 'VenteController@findArticleSurVenteByCodeBarre');
    Route::get('find-approvisionnement-by-id/{id}', 'ApprovisionnementController@findApprovisionnementById');
    Route::get('liste-reglements-by-date/{date}', 'ReglementController@listeReglementByDate');
    Route::get('liste-reglements-by-fournisseur/{fournisseur}', 'ReglementController@listeReglementByFournisseur');
    Route::get('liste-reglements-by-client/{client}', 'ReglementController@listeReglementByClient');
    Route::get('liste-reglements-by-moyen-reglement/{moyen}', 'ReglementController@listeReglementByMoyenReglement');
    Route::get('liste-reglements-by-commande/{commande}', 'ReglementController@listeReglementsByCommande');
    Route::get('liste-reglements-by-vente/{vente}', 'ReglementController@listeReglementsByVente');
    Route::get('liste-articles-by-depot/{depot}', 'DepotArticleController@listeArticlesByDepot');
    Route::get('liste-details-inventaire/{inventaire}', 'DetailInventaireController@listeDetailsInventaire');
    Route::get('liste-inventaires-by-date/{date}', 'InventaireController@listeInventaireByDate');
    Route::get('liste-inventaires-by-depot/{depot}', 'InventaireController@listeInventaireByDepot');
    Route::get('liste-approvisionnements-by-periode/{debut}/{fin}', 'ApprovisionnementController@listeApprovisionnementsByPeriode');
    Route::get('liste-approvisionnements-by-periode-fournisseur/{debut}/{fin}/{fournisseur}', 'ApprovisionnementController@listeApprovisionnementsByPeriodeFournisseur');
    Route::get('liste-ventes-by-periode/{debut}/{fin}', 'VenteController@listeVentesByPeriode');
    Route::get('liste-ventes-by-periode-client/{debut}/{fin}/{client}', 'VenteController@listeVentesByPeriodeClient');
    Route::get('liste-inventaires-by-periode/{debut}/{fin}', 'InventaireController@listeInventaireByPeriode');
    Route::get('liste-inventaires-by-depot-periode/{depot}/{debut}/{fin}', 'InventaireController@listeInventaireByDepotPeriode');
    Route::get('liste-destockages-by-date/{date}', 'DestockageController@listeDestockageByDate');
    Route::get('liste-destockages-by-article/{article}', 'DestockageController@listeDestockageByArticle');
    Route::get('liste-destockages-by-depot/{depot}', 'DestockageController@listeDestockageByDepot');
    Route::get('liste-destockages-by-article-depot/{article}/{depot}', 'DestockageController@listeDestockageByArticleDepot');
    Route::get('liste-transferts-stocks-by-date/{date}', 'TransfertStockController@listeTransfertStockByDate');
    Route::get('liste-transferts-stocks-by-article/{article}', 'TransfertStockController@listeTransfertStockByArticle');
    Route::get('liste-articles-bon/{bon_id}', 'ArticleBonController@listeArticleBon');
    Route::get('liste-bon-commandes-by-numero_bon/{numero_bon}', 'BonCommandeController@listeBonCommandeByNumeroBon');
    Route::get('liste-bon-commandes-by-date/{date}', 'BonCommandeController@listeBonCommandeByDate');
    Route::get('liste-bon-commandes-by-fournisseur/{fournisseur}', 'BonCommandeController@listeBonCommandeByFournisseur');
    Route::get('liste-reception-commande-by-numero_bon/{numero_bon}', 'BonCommandeController@listeReceptionCommandeByNumeroBon');
    Route::get('liste-reception-commande-by-date/{date}', 'BonCommandeController@listeReceptionCommandeByByDate');
    Route::get('liste-reception-commande-by-fournisseur/{fournisseur}', 'BonCommandeController@listeReceptionCommandeByFournisseur');
    Route::get('liste-depot-by-article/{article}', 'DepotArticleController@listeDepotByArticle');
    Route::get('liste-article-by-unite-in-depot/{depot}', 'DepotArticleController@listeArticleByUniteInDepot');
    Route::get('liste-article-by-unite-in-depot-by-code/{code}', 'DepotArticleController@listeArticleByUniteInDepotByCode');
    Route::get('find-article-in-depot-by-unite/{article}/{depot}/{unite}', 'DepotArticleController@findArticleInDepotByUnite');
    Route::get('find-article-in-depot-by-unite-caisse/{article}/{depot}/{unite}', 'DepotArticleController@findArticleInDepotByUniteCaisse');
    Route::get('liste-unites-by-depot-article/{depot}/{article}', 'DepotArticleController@listeUniteByDepotArticle');
    Route::get('get-article-informations-in-depot-unite/{article}/{depot}/{unite}', 'DepotArticleController@getArticleInformationsInDepotUnite');
    Route::get('liste-ventes-by-numero-facture/{numero}', 'VenteController@listeVenteByNumeroFacture');
    Route::get('liste-ventes-divers-by-numero-facture/{numero}', 'VenteController@listeVenteDiversByNumeroFacture');
    Route::get('liste-ventes-divers-by-client/{client}', 'VenteController@listeVentesDiversByClient');
    Route::get('liste-ventes-divers-by-date/{date}', 'VenteController@listeVentesDiversByDate');
    Route::get('find-reception-commande-by-id/{id}', 'BonCommandeController@findReceptionCommandeById');
    Route::get('liste-transferts-stocks-by-periode-article/{debut}/{fin}/{article}', 'TransfertStockController@listeTransfertStockByPeriodeArticle');
    Route::get('liste-transferts-stocks-by-periode/{debut}/{fin}', 'TransfertStockController@listeTransfertStockByPeriode');
    Route::get('liste-destockages-by-periode/{debut}/{fin}', 'DestockageController@listeDestockageByPeriode');
    Route::get('liste-reglements-by-periode/{debut}/{fin}', 'ReglementController@listeReglementByPeriode');
    Route::get('liste-reglements-by-periode-client/{debut}/{fin}/{client}', 'ReglementController@listeReglementByPeriodeClient');
    Route::get('liste-reglements-by-periode-fournisseur/{debut}/{fin}/{fournisseur}', 'ReglementController@listeReglementByPeriodeFournisseur');
    Route::get('get-caisse-ouverte-by-id/{id}', 'CaisseOuverteController@getCaisseOuverteById');
    Route::get('get-one-caisse-ouverte-by-caisse/{caisse}', 'CaisseOuverteController@getOneCaisseOuverteByCaisse');
    Route::get('liste-ventes-by-caisse/{caisse}', 'VenteController@listeVentesByCaisse');
    Route::get('liste-ventes-by-numero-ticket/{caisse}/{ticket}', 'VenteController@listeVenteByNumeroTicket');
    Route::get('liste-vente-by-caisse-date-vente/{caisse}/{date}', 'VenteController@listeVenteByCaisseDateVente');
    Route::get('liste-articles-transferts/{transfert_stock}', 'ArticleTransfertController@listeArticleTransferts');
    Route::get('liste-article-destockers/{destockage_id}', 'ArticleDestockerController@listeArticleDestockers');
    Route::get('get-all-article-in-one-depot/{depot_id}', 'DepotArticleController@getAllArticleInOneDepot');
    Route::get('get-all-article-in-one-depot-by-categorie/{depot_id}/{categorie}', 'DepotArticleController@getAllArticleInOneDepotByCategorie');
    Route::get('get-all-article-in-one-depot-by-quantite/{depot_id}/{quantite}', 'DepotArticleController@getAllArticleInOneDepotByQuantite');
    Route::get('get-all-article-in-one-depot-by-categorie-quantite/{depot_id}/{categorie}/{quantite}', 'DepotArticleController@getAllArticleInOneDepotByCategorieQuantite');
    Route::get('liste-operations-by-caisse/{caisse}', 'OperationController@listeOperationsByCaisse');
    Route::get('liste-operations-by-caisse-date/{caisse}/{date}', 'OperationController@listeOperationsByCaisseDate');
    Route::get('liste-operations-by-caisse-fournisseur/{caisse}/{fournisseur}', 'OperationController@listeOperationsByCaisseFournisseur');
    Route::get('liste-operations-by-caisse-client/{caisse}/{client}', 'OperationController@listeOperationsByCaisseClient');
    Route::get('liste-raports-caisses', 'VenteController@listeVenteAllcaisses');
    Route::get('liste-mouvements-stocks-article-by-depot-on-periode/{debut}/{fin}/{article}/{depot}', 'MouvementStockController@listeMouvementStockArticleByDepotOnPeriode');
    Route::get('liste-mouvements-stocks-by-periode/{debut}/{fin}', 'MouvementStockController@listeMouvementStockByPeriode');
    Route::get('liste-mouvements-stocks-by-article-on-periode/{debut}/{fin}/{article}', 'MouvementStockController@listeMouvementStockByArticleOnPeriode');
    Route::get('liste-mouvements-stocks-by-article/{article}', 'MouvementStockController@listeMouvementStockByArticle');
    Route::get('liste-mouvements-stocks-by-article-depot/{article}/{depot}', 'MouvementStockController@listeMouvementStockByArticleDepot');
    Route::get('liste-mouvements-stocks-by-depot/{depot}', 'MouvementStockController@listeMouvementStockByDepot');
    Route::get('liste-mouvements-stocks-by-depot-on-periode/{debut}/{fin}/{depot}', 'MouvementStockController@listeMouvementStockByDepotOnPeriode');
    Route::get('liste-raports-caisses-by-depot/{depot}', 'CaisseOuverteController@listeRaportCaisseByDepot');
    Route::get('liste-raports-caisses-by-depot-periode/{debut}/{fin}/{depot}', 'CaisseOuverteController@listeRaportCaisseByDepotPeriode');
    Route::get('liste-raports-caisses-by-periode/{debut}/{fin}', 'CaisseOuverteController@listeRaportCaisseByPeriode');
    Route::get('liste-articles-vendus-by-quantite', 'ArticleVenteController@listeArticlesVendusByQuantite')->name('liste-articles-vendus-by-quantite');
    Route::get('liste-articles-vendus-by-quantite-article/{article}', 'ArticleVenteController@listeArticlesVendusByQuantiteArtice');
    Route::get('liste-articles-vendus-by-quantite-periode/{debut}/{fin}', 'ArticleVenteController@listeArticlesVendusByQuantitePeriode');
    Route::get('liste-articles-vendus-by-quantite-periode-article/{debut}/{fin}/{article}', 'ArticleVenteController@listeArticlesVendusByQuantiteArticlePeriode');
    Route::get('liste-articles-vendus-by-quantite-depot/{depot}', 'ArticleVenteController@listeArticlesVendusByQuantiteDepot');
    Route::get('liste-articles-vendus-by-quantite-depot-article/{depot}/{article}', 'ArticleVenteController@listeArticlesVendusByQuantiteDepotArticle');
    Route::get('liste-articles-vendus-by-quantite-depot-periode/{depot}/{debut}/{fin}', 'ArticleVenteController@listeArticlesVendusByQuantiteDepotPeriode');
    Route::get('liste-articles-vendus-by-quantite-depot-article-periode/{debut}/{fin}/{depot}/{article}', 'ArticleVenteController@listeArticlesVendusByQuantiteDepotArticlePeriode');
    Route::get('liste-articles-recus-by-quantite', 'ArticleApprovisionnementController@listeArticlesRecusByQuantite')->name('liste-articles-recus-by-quantite');
    Route::get('liste-articles-recus-by-quantite-article/{article}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantiteArtice');
    Route::get('liste-articles-recus-by-quantite-periode/{debut}/{fin}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantitePeriode');
    Route::get('liste-articles-recus-by-quantite-periode-article/{debut}/{fin}/{article}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantiteArticlePeriode');
    Route::get('liste-articles-recus-by-quantite-periode-depot/{debut}/{fin}/{depot}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantiteDepotPeriode');
    Route::get('liste-articles-recus-by-quantite-depot-article/{depot}/{article}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantiteDepotArticle');
    Route::get('liste-articles-recus-by-quantite-depot/{depot}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantiteDepot');
    Route::get('liste-articles-recus-by-quantite-periode-depot-article/{debut}/{fin}/{depot}/{article}', 'ArticleApprovisionnementController@listeArticlesRecusByQuantitePeriodeDepotArticle');
    Route::get('liste-mouvements-stocks-grouper-by-depot-article/{depot}/{article}', 'MouvementStockController@listeMouvementStockGrouperByDepotArticle');
    Route::get('liste-mouvements-stocks-grouper-by-article/{article}', 'MouvementStockController@listeMouvementStockGrouperByArticle');
    Route::get('liste-mouvements-stocks-grouper-by-depot/{depot}', 'MouvementStockController@listeMouvementStockGrouperByDepot');
    Route::get('liste-articles-retournes/{retour_article}', 'ArticleRetourneController@listeArticleRetourne');
    Route::get('liste-retour-article-by-vente/{vente}', 'RetourArticleController@listeRetourArticleByVente');
    Route::get('liste-retour-article-by-date/{dete}', 'RetourArticleController@listeRetourArticleByDate');
    Route::get('liste-retour-article-by-periode/{debut}/{fin}', 'RetourArticleController@listeRetourArticleByPeriode');
    Route::get('liste-retour-article-by-depot/{depot}', 'RetourArticleController@listeRetourArticleByDepot');
    Route::get('liste-retour-article-by-article/{article}', 'RetourArticleController@listeRetourArticleByArticle');
    Route::get('liste-retour-article-by-periode-depot/{debut}/{fin}/{depot}', 'RetourArticleController@listeRetourArticleByPeriodeDepot');
    Route::get('liste-retour-article-by-periode-article/{debut}/{fin}/{article}', 'RetourArticleController@listeRetourArticleByPeriodeArticle');
    Route::get('liste-depenses-by-categorie/{categorie}', 'DepenseController@listeDepenseByCategorie');
    Route::get('liste-depenses-by-periode/{debut}/{fin}', 'DepenseController@listeDepenseByPeriode');
    Route::get('liste-depenses-by-categorie-periode/{debut}/{fin}/{categorie}', 'DepenseController@listeDepenseByCategoriePeriode');
    Route::get('liste-declaration-by-periode/{debut}/{fin}', 'TvaDeclareeController@listeDeclarationByPeriode');
    Route::get('liste-declaration-depot/{depot}', 'TvaDeclareeController@listeDeclarationByDepot');
    Route::get('liste-declaration-depot-periode/{depot}/{debut}/{fin}', 'TvaDeclareeController@listeDeclarationByDepotPeriode');
    Route::get('liste-tickets-declares/{declaration}', 'TicketInTvaController@listeTicketDeclare');

    
    //Etat  
    Route::get('fiche-bon-commande/{bon_commande_id}', 'BonCommandeController@ficheBonCommandePdf');
    Route::get('fiche-reception-bon-commande/{bon_commande_id}', 'BonCommandeController@ficheReceptionBonCommandePdf');
    Route::get('facture-vente-pdf/{vente}', 'VenteController@factureVentePdf');
    Route::get('facture-vente-divers-pdf/{vente}', 'VenteController@factureVenteDiversPdf');
    Route::get('recu-reglement-pdf/{reglement}', 'ReglementController@recuReglementPdf');
    Route::get('fiche-inventaire-pdf/{inventaire}', 'InventaireController@ficheInventairePdf');
    Route::get('fiche-approvisionnement-pdf/{approvisionnement}', 'ApprovisionnementController@ficheApprovisionnementPdf');
    Route::get('ticket-vente-pdf/{vente}', 'VenteController@ticketVentePdf');
    Route::get('transfert-stock-pdf/{transfert_stock}', 'TransfertStockController@transfertStockPdf');
    Route::get('destockage-pdf/{destockage_id}', 'DestockageController@destockagePdf');
    Route::get('liste-articles-by-depot-pdf/{depot}', 'DepotArticleController@listeArticleByDepotPdf');
    
    Route::get('liste-articles-by-depot-by-categorie-pdf/{depot}/{categorie}', 'DepotArticleController@listeArticleByDepotByCategoriePdf');
    Route::get('liste-articles-by-depot-by-quantite-pdf/{depot}/{quantite}', 'DepotArticleController@listeArticleByDepotByQuantitePdf');
    Route::get('liste-articles-by-depot-by-categorie-quantite-pdf/{depot}/{categorie}/{quantite}', 'DepotArticleController@listeArticleByDepotByCategorieQuantitePdf');

    
    
    Route::get('mouvements-stocks-pdf', 'MouvementStockController@mouvementStockPdf');
    Route::get('mouvements-stocks-article-by-depot-on-periode-pdf/{debut}/{fin}/{article}/{depot}', 'MouvementStockController@mouvementStockArticleByDepotOnPeriodePdf');
    Route::get('mouvements-stocks-by-periode-pdf/{debut}/{fin}', 'MouvementStockController@mouvemenStockByPeriodePdf');
    Route::get('mouvements-stocks-by-article-on-periode-pdf/{debut}/{fin}/{article}', 'MouvementStockController@mouvementStockByArticleOnPeriodePdf');
    Route::get('mouvements-stocks-by-depot-on-periode-pdf/{debut}/{fin}/{depot}', 'MouvementStockController@mouvementStockByDepotOnPeriodePdf');
    Route::get('mouvements-stocks-article-pdf/{article}', 'MouvementStockController@mouvementStockArticlePdf');
    Route::get('mouvements-stocks-depot-pdf/{depot}', 'MouvementStockController@mouvementStockDepotPdf');
    Route::get('mouvements-stocks-by-article-depot-pdf/{article}/{depot}', 'MouvementStockController@mouvementStockArticleByDepotPdf');
    Route::get('billetage-pdf/{caisse_ouverte}', 'BilletageController@billetagePdf');
    Route::get('raports-caisses-pdf', 'CaisseOuverteController@raportCaissesPdf');
    Route::get('raports-caisses-by-periode-pdf/{debut}/{fin}', 'CaisseOuverteController@raportCaisseByPeriodePdf');
    Route::get('articles-vendus-by-quantite-pdf', 'ArticleVenteController@articlesVendusByQuantitePdf');
    Route::get('articles-vendus-by-quantite-article-pdf/{article}', 'ArticleVenteController@articlesVendusByQuantiteArticlePdf');
    Route::get('articles-vendus-by-quantite-periode-pdf/{debut}/{fin}', 'ArticleVenteController@articlesVendusByQuantitePeriodePdf');
    Route::get('articles-vendus-by-quantite-periode-article-pdf/{debut}/{fin}/{article}', 'ArticleVenteController@articlesVendusByQuantitePeriodeArticlePdf');
    Route::get('articles-vendus-by-quantite-depot-pdf/{depot}', 'ArticleVenteController@articlesVendusByDepotPdf');
    Route::get('articles-vendus-by-quantite-depot-article-pdf/{depot}/{article}', 'ArticleVenteController@articlesVendusByDepotArticlePdf');
    Route::get('articles-vendus-by-quantite-depot-periode-pdf/{depot}/{debut}/{fin}', 'ArticleVenteController@articlesVendusByDepotPeriodePdf');
    Route::get('articles-vendus-by-quantite-depot-article-periode-pdf/{debut}/{fin}/{depot}/{article}', 'ArticleVenteController@articlesVendusByDepotArticlePeriodePdf');
    Route::get('articles-recus-by-quantite-pdf', 'ArticleApprovisionnementController@articlesRecusByQuantitePdf');
    Route::get('articles-recus-by-quantite-article-pdf/{article}', 'ArticleApprovisionnementController@articlesRecusByQuantiteArticlePdf');
    Route::get('articles-recus-by-quantite-periode-pdf/{debut}/{fin}', 'ArticleApprovisionnementController@articlesRecusByQuantitePeriodePdf');
    Route::get('articles-recus-by-quantite-periode-article-pdf/{debut}/{fin}/{article}', 'ArticleApprovisionnementController@articlesRecusByQuantitePeriodeArticlePdf');
    Route::get('articles-recus-by-quantite-depot-pdf/{depot}', 'ArticleApprovisionnementController@articlesRecusByQuantiteDepotPdf');
    Route::get('articles-recus-by-quantite-depot-article-pdf/{depot}/{article}', 'ArticleApprovisionnementController@articlesRecusByQuantiteDepotArticlePdf');
    Route::get('articles-recus-by-quantite-periode-depot-pdf/{depot}/{debut}/{fin}', 'ArticleApprovisionnementController@articlesRecusByQuantiteDepotPeriodePdf');
    Route::get('articles-recus-by-quantite-periode-depot-article-pdf/{debut}/{fin}/{depot}/{article}', 'ArticleApprovisionnementController@articlesRecusByQuantitePeriodeDepotArticlePdf');
    Route::get('mouvements-stocks-grouper-pdf', 'MouvementStockController@mouvementStockGrouperPdf');
    Route::get('mouvements-stocks-grouper-by-article-pdf/{article}', 'MouvementStockController@mouvementStockGrouperByArticlePdf');
    Route::get('mouvements-stocks-grouper-by-depot-pdf/{depot}', 'MouvementStockController@mouvementStockGrouperByDepotPdf');
    Route::get('mouvements-stocks-grouper-by-depot-article-pdf/{depot}/{article}', 'MouvementStockController@mouvementStockGrouperByDepotArticlePdf');
    Route::get('fiche-retour-article-pdf/{retour_id}', 'RetourArticleController@recuRetourArticlePdf');
    Route::get('articles-en-voie-peremption-pdf', 'DepotArticleController@listeArticleEnVoiePeremptionPdf')->name('articles-en-voie-peremption-pdf');
    Route::get('liste-depenses-pdf', 'DepenseController@listeDepensePdf');
    Route::get('liste-depenses-by-categorie-pdf/{categorie}', 'DepenseController@listeDepenseByCategoriePdf');
    Route::get('liste-depenses-by-periode-pdf/{debut}/{fin}', 'DepenseController@listeDepenseByPeriodePdf');
    Route::get('liste-depenses-by-periode-categorie-pdf/{debut}/{fin}/{categorie}', 'DepenseController@listeDepenseByPeriodeCategoriePdf');
    Route::get('ticket-declares-pdf/{declaration}', 'TicketInTvaController@ticketDeclarePdf');

    //Export excel  
    Route::get('export-excel-articl-by-depot/{depot}', 'DepotArticleController@exportExcelArticlByDepot')->name('export-excel-articl-by-depot');
});

//Etats
Route::namespace('Etat')->middleware('auth')->name('etat.')->prefix('etat')->group(function () {
    //Ecrans
    Route::get('etat-approvisionnements', 'EtatController@vuApprovisionnement')->name('etat-approvisionnements');
    Route::get('etat-ventes', 'EtatController@vuVente')->name('etat-ventes');
    Route::get('etat-articles', 'EtatController@vuArticle')->name('etat-articles');
    Route::get('etat-reglements', 'EtatController@vuReglement')->name('etat-reglements'); //TODO
    Route::get('etat-fournisseurs', 'EtatController@vuFournisseur')->name('etat-fournisseurs');
    Route::get('etat-clients', 'EtatController@vuClient')->name('etat-clients');
    Route::get('etat-depots', 'EtatController@vuDepot')->name('etat-depots');
    Route::get('etat-inventaires', 'EtatController@vuInventaire')->name('etat-inventaires');
    Route::get('etat-transfert-stock', 'EtatController@vuTransfertStock')->name('etat-transfert-stock');
    Route::get('etat-destockage', 'EtatController@vuDestockage')->name('etat-destockage');
    Route::get('etat-reglements', 'EtatController@vuReglement')->name('etat-reglements');
    Route::get('solde-client', 'EtatComptabiliteController@vuSoldeClient')->name('solde-client');
    Route::get('solde-fournisseur', 'EtatComptabiliteController@vuSoldeFournisseur')->name('solde-fournisseur');
    Route::get('marge-vente', 'EtatComptabiliteController@vuMargeVente')->name('marge-vente');
    Route::get('timbre-fiscal', 'EtatComptabiliteController@vuTimbreFiscal')->name('timbre-fiscal');
    
    Route::get('declaration-fiscal', 'EtatComptabiliteController@vuDeclarationFiscal')->name('declaration-fiscal');
    
    Route::get('tva-airsi', 'EtatComptabiliteController@vuTvaAirsi')->name('tva-airsi');
    Route::get('articles-vendus-par-quantite', 'EtatController@vuArticleVenduParQuantite')->name('articles-vendus-par-quantite');
    Route::get('articles-recus-par-quantite', 'EtatController@vuArticleRecusParQuantite')->name('articles-recus-par-quantite');
    Route::get('points-caisses-clotures', 'EtatComptabiliteController@vuPointCaisseCloture')->name('points-caisses-clotures');
    Route::get('articles-retournes', 'EtatController@vuArticleRetournees')->name('articles-retournes');
    
    //Liste dans boostrap table 
    Route::get('liste-solde-client', 'EtatComptabiliteController@listeSoldeClient')->name('liste-solde-client');
    Route::get('liste-solde-fournisseurs', 'EtatComptabiliteController@listeSoldeFournisseur')->name('liste-solde-fournisseurs');
    Route::get('liste-timbre-ventes', 'EtatComptabiliteController@listeTimbreVentes')->name('liste-timbre-ventes');
    Route::get('liste-timbre-ventes-periode/{debut}/{fin}', 'EtatComptabiliteController@listeTimbreVentesPeriode');
    Route::get('liste-timbre-ventes-by-depot/{depot}', 'EtatComptabiliteController@listeTimbreVentesByDepot');
    Route::get('liste-timbre-ventes-by-periode-depot/{debut}/{fin}/{depot}', 'EtatComptabiliteController@listeTimbreVentesPeriodeDepot');
    
    Route::get('liste-declaration-ventes', 'EtatComptabiliteController@listeDeclarationVentes');
    Route::get('liste-declaration-ventes-by-periode/{debut}/{fin}', 'EtatComptabiliteController@listeDeclarationVentesByPeriode');

    Route::get('liste-point-caisse-clotures-jour', 'EtatComptabiliteController@listePointCaisseCloturesJour')->name('liste-point-caisse-clotures-jour');
    Route::get('liste-point-caisse-clotures-depot/{depot}', 'EtatComptabiliteController@listePointCaisseCloturesJourDepot');
    Route::get('liste-point-caisse-clotures-periode/{debut}/{fin}', 'EtatComptabiliteController@listePointCaisseCloturesPeriode');
    Route::get('liste-point-caisse-clotures-periode-depot/{debut}/{fin}/{depot}', 'EtatComptabiliteController@listePointCaisseCloturesPeriodeDepot');
    
    Route::get('liste-all-ventes-marge', 'EtatComptabiliteController@listeAllVentesMarge')->name('liste-all-ventes-marge');
    Route::get('liste-all-ventes-marge-periode/{debut}/{fin}', 'EtatComptabiliteController@listeAllVentesMargePeriode');
    Route::get('liste-all-ventes-marge-periode-periode-depot/{debut}/{fin}/{depot}', 'EtatComptabiliteController@listeAllVentesMargePeriodeDepot');
    Route::get('liste-all-ventes-marge-depot/{depot}', 'EtatComptabiliteController@listeAllVentesMargeDepot');
    
    //Route parametrées 
    Route::get('liste-solde-client-by-client/{client}', 'EtatComptabiliteController@listeSoldeClientByClient');
    Route::get('liste-solde-fournisseur-by-fournisseur/{fournisseur}', 'EtatComptabiliteController@listeSoldeFournisseurByFournisseur');
            
    //Fichiers PDF 
        //Approvisionnement 
    Route::get('liste-approvisionnements-pdf', 'EtatController@listeApprovisionnementPdf');
    Route::get('liste-approvisionnements-by-periode-pdf/{debut}/{fin}', 'EtatController@listeApprovisionnementByPeriodePdf');
    Route::get('liste-approvisionnements-by-fournisseur-pdf/{fournisseur}', 'EtatController@listeApprovisionnementByFournisseurPdf');
    Route::get('liste-approvisionnements-by-periode-fournisseur-pdf/{debut}/{fin}/{fournisseur}', 'EtatController@listeApprovisionnementByPeriodeFournisseurPdf');
    
        //Vente
    Route::get('liste-ventes-pdf', 'EtatController@listeVentePdf');
    Route::get('liste-ventes-by-periode-pdf/{debut}/{fin}', 'EtatController@listeVenteByPeriodePdf');
    Route::get('liste-ventes-by-client-pdf/{client}', 'EtatController@listeVenteByClientPdf');
    Route::get('liste-ventes-by-periode-client-pdf/{debut}/{fin}/{client}', 'EtatController@listeVenteByPeriodeClientPdf');
    Route::get('print-recu-vente-pdf/{vente}', 'EtatController@printRecuVentePdf');
    Route::get('point-caisse-clotures-jour-pdf', 'EtatComptabiliteController@pointCaisseCloturesJourPdf');
    Route::get('point-caisse-clotures-periode-pdf/{debut}/{fin}', 'EtatComptabiliteController@pointCaisseCloturesPeriodePdf');
    Route::get('point-caisse-clotures-periode-depot-pdf/{debut}/{fin}/{depot}', 'EtatComptabiliteController@pointCaisseCloturesPeriodeDepotPdf');
    Route::get('point-caisse-clotures-depot-pdf/{depot}', 'EtatComptabiliteController@pointCaisseCloturesDepotPdf');
    Route::get('chiffre-affaire-client', 'EtatController@chiffreAffaireClientPdf')->name('chiffre-affaire-client');
    Route::get('liste-vente-by-quantite', 'EtatController@listeArticleVenteByQuantitePdf')->name('liste-vente-by-quantite');
     
    Route::get('timbre-fiscal-pdf', 'EtatComptabiliteController@timbreFiscalPdf');
    Route::get('declaration-tva-pdf', 'EtatComptabiliteController@declarationTvaPdf');
    Route::get('all-ventes-marge-periode-pdf/{debut}/{fin}', 'EtatComptabiliteController@allVenteMargePeriodePdf');
    Route::get('all-ventes-marge-pdf', 'EtatComptabiliteController@allVenteMargePdf');
    Route::get('all-ventes-marge-depot-pdf/{depot}', 'EtatComptabiliteController@allVenteMargeDepotPdf');
    Route::get('all-ventes-marge-periode-depot-pdf/{debut}/{fin}/{depot}', 'EtatComptabiliteController@allVenteMargePeriodeDepotPdf');
    
        //Articles 
    Route::get('liste-articles-pdf', 'EtatController@listeArticlePdf');
    Route::get('liste-articles-by-categorie-pdf/{categorie}', 'EtatController@listeArticleByCategoriePdf');
    Route::get('articles-retournes-pdf', 'EtatController@listeRetourArticlePdf');
    Route::get('articles-retournes-periode-pdf/{debut}/{fin}', 'EtatController@listeRetourArticlePeriodePdf');
    Route::get('articles-retournes-periode-depot-pdf/{debut}/{fin}/{depot}', 'EtatController@listeRetourArticlePeriodeDepotPdf');
    Route::get('articles-retournes-by-article-pdf/{article}', 'EtatController@listeRetourArticleByArticlePdf');
    Route::get('articles-retournes-by-depot-pdf/{depot}', 'EtatController@listeRetourArticleByDepotPdf');
    
        //Fournisseurs et clients 
    Route::get('liste-fournisseurs-pdf', 'EtatController@listeFournisseurPdf');
    Route::get('liste-fournisseurs-by-nation-pdf/{nation}', 'EtatController@listeFournisseurByNationPdf');
    Route::get('liste-clients-pdf', 'EtatController@listeClientPdf');
    Route::get('liste-clients-by-nation-pdf/{nation}', 'EtatController@listeClientByNationPdf');
    Route::get('solde-client-pdf', 'EtatComptabiliteController@soldeClientPdf')->name('solde-client-pdf');
    Route::get('solde-client-by-client-pdf/{client}', 'EtatComptabiliteController@soldeClientByClientPdf');
    Route::get('solde-fournisseur-pdf', 'EtatComptabiliteController@soldeFournisseurPdf');
    Route::get('solde-fournisseur-by-fournisseur-pdf/{fournisseur}', 'EtatComptabiliteController@soldeFournisseurByFournisseurPdf');
    
    //Dépôts et inventaires 
    Route::get('liste-depots-pdf', 'EtatController@listeDepotPdf');
    Route::get('liste-inventaires-pdf', 'EtatController@listeInventairePdf');
    Route::get('liste-inventaires-by-depot-pdf/{depot}', 'EtatController@listeInventaireByDepotPdf');
    Route::get('liste-inventaires-by-periode-pdf/{debut}/{fin}', 'EtatController@listeInventaireByPeriodePdf');
    Route::get('liste-inventaires-by-depot-periode-pdf/{depot}/{debut}/{fin}', 'EtatController@listeInventaireByDepotPeriodePdf');
    
    //Transfert stock et déstockage 
    Route::get('liste-transferts-stocks-pdf', 'EtatController@listeTransfertStockPdf');
    Route::get('liste-destockages-pdf', 'EtatController@listeDestockagePdf');
    Route::get('liste-transferts-stocks-by-article-pdf/{article}', 'EtatController@listeTransfertStockByArticlePdf');
    Route::get('liste-transferts-stocks-by-periode-pdf/{debut}/{fin}', 'EtatController@listeTransfertStockByPeriodePdf');
    Route::get('liste-destockages-by-periode-pdf/{debut}/{fin}', 'EtatController@listeDestockageByPeriodePdf');
    Route::get('liste-transferts-stocks-by-periode-article-pdf/{debut}/{fin}/{article}', 'EtatController@listeTransfertStockByPeriodeArticlePdf');
    
    //Règlement 
    Route::get('liste-reglements-pdf', 'EtatController@listeReglementPdf');
    Route::get('liste-reglements-by-periode-pdf/{debut}/{fin}', 'EtatController@listeReglementByPeriodePdf');
    Route::get('liste-reglements-by-fournisseur-pdf/{fournisseur}', 'EtatController@listeReglementByFournisseurPdf');
    Route::get('liste-reglements-by-client-pdf/{client}', 'EtatController@listeReglementByClientPdf');
    Route::get('liste-reglements-by-periode-fournisseur-pdf/{debut}/{fin}/{fournisseur}', 'EtatController@listeReglementByPeriodeFournisseurPdf');
    Route::get('liste-reglements-by-periode-client-pdf/{debut}/{fin}/{client}', 'EtatController@listeReglementByPeriodeClientPdf');
});

//les routes du module Canal
Route::namespace('Canal')->middleware('auth')->name('canal.')->prefix('canal')->group(function () {
    //Route resources 
   Route::resource('type-abonnements', 'TypeAbonnementController');
   Route::resource('options-canal', 'OptionCanalController');
   Route::resource('type-cautions', 'TypeCautionController');
   Route::resource('materiels', 'MaterielController');
   Route::resource('agences', 'AgenceController');
   Route::resource('localites', 'LocaliteController');
   Route::resource('demande-approv', 'DemandeApproviCanalController');
   Route::resource('rebis', 'RebiController');
   Route::resource('caution-agences', 'CautionAgenceController');
   Route::resource('type-pieces', 'TypePieceController');
   Route::resource('abonnes', 'AbonneController');
   Route::resource('abonnements', 'AbonnementController');
   Route::resource('reabonnements', 'ReabonnementController');
   Route::resource('vente-materiels', 'VenteMaterielController');
   Route::resource('materiels-vendues', 'MaterielVendueController');
    
    //Route pour les listes dans boostrap table 
    Route::get('liste-type-abonnements', 'TypeAbonnementController@listeTypeAbonnement')->name('liste-type-abonnements');
    Route::get('liste-options-canal', 'OptionCanalController@listeOptionCanal')->name('liste-options-canal');
    Route::get('liste-type-cautions', 'TypeCautionController@listeTypeCaution')->name('liste-type-cautions');
    Route::get('liste-materiels', 'MaterielController@listeMateriel')->name('liste-materiels');
    Route::get('liste-agences', 'AgenceController@listeAgence')->name('liste-agences'); 
    Route::get('liste-localites', 'LocaliteController@listeLocalite')->name('liste-localites'); 
    Route::get('liste-demande-approv', 'DemandeApproviCanalController@listeDemandeApprov')->name('liste-demande-approv'); 
    Route::get('liste-rebis', 'RebiController@listeRebi')->name('liste-rebis'); 
    Route::get('liste-caution-agences', 'CautionAgenceController@listeCautionAgence')->name('liste-caution-agences'); 
    Route::get('liste-type-pieces', 'TypePieceController@listeTypePiece')->name('liste-type-pieces'); 
    Route::get('liste-abonnes', 'AbonneController@listeAbonne')->name('liste-abonnes'); 
    Route::get('liste-abonnements', 'AbonnementController@listeAbonnement')->name('liste-abonnements'); 
    Route::get('liste-reabonnements', 'ReabonnementController@listeReabonnement')->name('liste-reabonnements'); 
    Route::get('liste-mouvement-ventes', 'RebiController@listeMouvementVente')->name('liste-mouvement-ventes'); 
    Route::get('liste-ventes-materiels', 'VenteMaterielController@listeVenteMateriel')->name('liste-ventes-materiels'); 
    
    //Route particulières 
    Route::post('update-materiel', 'MaterielController@updateMateriel')->name('update-materiel');
    Route::post('update-demande-approv', 'DemandeApproviCanalController@updateDemandeApprov')->name('update-demande-approv');
    Route::post('update-caution-agnece', 'CautionAgenceController@updateCautionAgence')->name('update-caution-agnece');
    Route::get('caution-agence', 'CautionAgenceController@cautionAgenceVueAgence')->name('caution-agence');
    Route::get('rebis-agence', 'RebiController@rebisVueAgence')->name('rebis-agence');
    Route::get('mouvement-vente', 'RebiController@vueMouvementVente')->name('mouvement-vente');
    Route::post('update-vente-materiel', 'VenteMaterielController@updateVenteMateriel')->name('update-vente-materiel');
    
    Route::post('demande-caution', 'CautionAgenceController@demandeCaution')->name('demande-caution');
    Route::post('update-demande-caution', 'CautionAgenceController@updateDemandeCaution')->name('update-demande-caution');
    Route::put('caution-agences-confirme/{id}', 'CautionAgenceController@confirmation');
    
    //Routes parametrées  caution-agences-confirme
    Route::get('liste-agences-by-localite/{localite}', 'AgenceController@listeAgenceByLocalite');
    Route::get('liste-demande-approv-by-type-caution/{type_caution}', 'DemandeApproviCanalController@listeDemandeApprovByTypeCaution');
    Route::get('liste-demande-approv-by-periode/{debut}/{fin}', 'DemandeApproviCanalController@listeDemandeApprovByPeriode');
    Route::get('liste-demande-approv-by-periode-type-caution/{debut}/{fin}/{type_caution}', 'DemandeApproviCanalController@listeDemandeApprovByPeriodeTypeCaution');
    Route::get('liste-caution-agence-by-periode/{debut}/{fin}', 'CautionAgenceController@listeCautionAgenceByPeriode');
    Route::get('liste-caution-agence-by-agence/{agence}', 'CautionAgenceController@listeCautionAgenceByAgence');
    Route::get('liste-caution-agence-by-agence-periode/{agence}/{debut}/{fin}', 'CautionAgenceController@listeCautionAgenceByAgencePeriode');
    Route::get('liste-rebis-by-periode/{debut}/{fin}', 'RebiController@listeRebiByPeriode');
    Route::get('liste-rebis-by-type-caution/{type}', 'RebiController@listeRebiByTypeCaution');
    Route::get('liste-rebis-by-agence/{agence}', 'RebiController@listeRebiByAgence');
    
    Route::get('get-infos-abonne/{id}', 'AbonneController@getInfosAbonne');
    Route::get('liste-abonnes-by-name/{name}', 'AbonneController@listeAbonneByName');
    Route::get('liste-abonnes-by-localite/{localite}', 'AbonneController@listeAbonneByLocalite');
    Route::get('get-infos-abonnement/{id}', 'AbonnementController@getInfosAbonnement');
    Route::get('liste-abonnements-by-numero/{numero}', 'AbonnementController@listeAbonnementByNumero');
    Route::get('liste-abonnements-by-agence/{agence}', 'AbonnementController@listeAbonnementByAgence');
    Route::get('liste-abonnements-by-periode/{debut}/{fin}', 'AbonnementController@listeAbonnementByPeriode');
    Route::get('liste-abonnements-by-periode-agence/{debut}/{fin}/{agence}', 'AbonnementController@listeAbonnementByPeriodeAgence');
    
    Route::get('liste-reabonnements-by-numero/{numero}', 'ReabonnementController@listeReabonnementByNumero');
    Route::get('liste-reabonnements-by-agence/{agence}', 'ReabonnementController@listeReabonnementByAgence');
    Route::get('liste-reabonnements-by-periode/{debut}/{fin}', 'ReabonnementController@listeReabonnementByPeriode');
    Route::get('liste-reabonnements-by-periode-agence/{debut}/{fin}/{agence}', 'ReabonnementController@listeReabonnementByPeriodeAgence');
    
    Route::get('liste-mouvement-ventes-by-periode/{debut}/{fin}', 'RebiController@listeMouvementVenteByPeriode'); 
    Route::get('liste-mouvement-ventes-by-agence/{agence}', 'RebiController@listeMouvementVenteByAgence'); 
    Route::get('liste-mouvement-ventes-by-agence-periode/{agence}/{debut}/{fin}', 'RebiController@listeMouvementVenteByAgencePeriode'); 
    
    Route::get('liste-materiel-vendus/{vente}', 'MaterielVendueController@listeMaterielVendu'); 
    Route::get('liste-vente-materiel-by-facture/{facture}', 'VenteMaterielController@listeVenteMaterielByFacture'); 
    Route::get('liste-vente-materiel-by-date/{date}', 'VenteMaterielController@listeVenteMaterielByDate'); 
    Route::get('liste-vente-materiel-by-agence/{agence}', 'VenteMaterielController@listeVenteMaterielByAgence'); 

    //Etat  
    //*** Agence **// 
    Route::get('liste-agences-pdf', 'AgenceController@listeAgencePdf');
    Route::get('liste-agences-by-localite-pdf/{localite}', 'AgenceController@listeAgenceByLocalitePdf');
    
    //*** Caution canal **// 
    Route::get('liste-caution-canal-pdf', 'DemandeApproviCanalController@listeCautionCanalPdf');
    Route::get('liste-caution-canal-by-type-pdf/{type}', 'DemandeApproviCanalController@listeCautionCanalByTypePdf');
    Route::get('liste-caution-canal-by-periode-pdf/{debut}/{fin}', 'DemandeApproviCanalController@listeCautionCanalByPeriodePdf');
    Route::get('liste-caution-canal-by-type-periode-pdf/{type}/{debut}/{fin}', 'DemandeApproviCanalController@listeCautionCanalByTypePeriodePdf');
    
    //*** Caution agence **// 
    Route::get('liste-caution-agence-pdf', 'CautionAgenceController@listeCautionAgencePdf');
    Route::get('liste-caution-agence-by-agence-pdf/{agence}', 'CautionAgenceController@listeCautionAgenceByAgencePdf');
    Route::get('liste-caution-agence-by-periode-pdf/{debut}/{fin}', 'CautionAgenceController@listeCautionAgenceByPeriodePdf');
    Route::get('liste-caution-agence-by-agenec-periode-pdf/{agence}/{debut}/{fin}', 'CautionAgenceController@listeCautionAgenceByAgencePeriodePdf');

    //*** Rebis ***//
    Route::get('liste-rebis-pdf', 'RebiController@listeRebisPdf');
    Route::get('liste-rebis-by-periode-pdf/{debut}/{fin}', 'RebiController@listeRebisByPeriodePdf');
    Route::get('liste-rebis-by-type-caution-pdf/{type}', 'RebiController@listeRebiByTypeCautionPdf');
    
    Route::get('liste-mouvement-ventes-pdf', 'RebiController@listeMouvementVentePdf'); 
    Route::get('liste-mouvement-ventes-by-periode-pdf/{debut}/{fin}', 'RebiController@listeMouvementVenteByPeriodePdf'); 
    Route::get('liste-mouvement-ventes-by-agence-pdf/{agence}', 'RebiController@listeMouvementVenteByAgencePdf'); 
    Route::get('liste-mouvement-ventes-by-agence-periode-pdf/{agence}/{debut}/{fin}', 'RebiController@listeMouvementVenteByAgencePeriodePdf'); 

    
    //*** Abonnés ***// 
    Route::get('liste-abonnes-pdf', 'AbonneController@listeAbonnePdf');
    Route::get('liste-abonnes-by-localite-pdf/{localite}', 'AbonneController@listeAbonneByLocalitePdf');
    
    //*** Abonnment ***// 
    Route::get('liste-abonnements-pdf', 'AbonnementController@listeAbonnementPdf');
    Route::get('liste-abonnements-by-agence-pdf/{agence}', 'AbonnementController@listeAbonnementByAgencePdf');
    Route::get('liste-abonnements-by-periode-pdf/{debut}/{fin}', 'AbonnementController@listeAbonnementByPeriodePdf');
    Route::get('liste-abonnements-by-periode-agence-pdf/{debut}/{fin}/{agence}', 'AbonnementController@listeAbonnementByPeriodeAgencePdf');
    Route::get('ticket-abonnements-pdf/{id}', 'AbonnementController@ticketAbonnementPdf');
       
    //*** Reabonnment ***//  
    Route::get('liste-reabonnements-pdf', 'ReabonnementController@listeReabonnementPdf');
    Route::get('liste-reabonnements-by-agence-pdf/{agence}', 'ReabonnementController@listeReabonnementByAgencePdf');
    Route::get('liste-reabonnements-by-periode-pdf/{debut}/{fin}', 'ReabonnementController@listeReabonnementByPeriodePdf');
    Route::get('liste-reabonnements-by-periode-agence-pdf/{debut}/{fin}/{agence}', 'ReabonnementController@listeReabonnementByPeriodeAgencePdf');     
    
    //*** Vente matériel ***//
    Route::get('all-vente-materiel-pdf', 'VenteMaterielController@allVenteMaterielPdf');
    Route::get('all-vente-materiel-by-agence-pdf/{agence}', 'VenteMaterielController@allVenteMaterielByAgencePdf');
    Route::get('all-vente-materiel-by-agence-date-pdf/{agence}/{date}', 'VenteMaterielController@allVenteMaterielByAgenceDatePdf');
    Route::get('facture-vente-materiel-pdf/{id}', 'VenteMaterielController@factureVenteMaterielPdf');
});

//les routes du module Auth 
Route::namespace('Auth')->middleware('auth')->name('auth.')->prefix('auth')->group(function () {
    //Route resources
    Route::resource('users', 'UserController');
    Route::resource('restaurages', 'RestaurageDataController');
    
    //Route pour les listes dans boostrap table
    Route::get('liste_users', 'UserController@listeUser')->name('liste_users');
    Route::get('liste_all_tables', 'RestaurageDataController@listeAllTable')->name('liste_all_tables');
    Route::get('liste-users-agences', 'UserController@listeUserAgence')->name('liste-users-agences');
    
    //Autres routes pour le profil 
    Route::get('profil-informations', 'UserController@profil')->name('profil-informations');
    Route::get('infos-profil-to-update', 'UserController@infosProfiTolUpdate')->name('infos-profil-to-update');
    Route::put('update-profil/{id}', 'UserController@updateProfil');
    Route::get('update-password-page', 'UserController@updatePasswordPage');
    Route::post('update-password', 'UserController@updatePasswordProfil')->name('update-password');

    //Réinitialisation du mot de passe manuellement par l'administrateur 
    Route::delete('/reset_password_manualy/{user}', 'UserController@resetPasswordManualy');
    
    //Routes particulières 
     Route::post('verification-access', 'UserController@verificationAccess')->name('verification-access');
     Route::get('users-agence-canal', 'UserController@userAgenceCanal')->name('users-agence-canal');
     Route::post('add-user-agence', 'UserController@addUserAgence')->name('add-user-agence');
     Route::put('update-user-agence/{id}', 'UserController@updateUserAgence')->name('update-user-agence');
     
     //Routes avec parametre 
    Route::get('one_table/{table}', 'RestaurageDataController@oneTable');
    Route::get('liste_content_one_table/{table}', 'RestaurageDataController@listeContentOneTable')->name('liste_content_one_table');
    Route::post('restaurage', 'RestaurageDataController@restaurage')->name('restaurage');
});
