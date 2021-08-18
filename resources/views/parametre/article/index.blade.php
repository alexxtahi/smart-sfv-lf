@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="col-md-3">
    <select class="form-control" id="searchByCategorie">
        <option value="0">-- Toutes les cat&eacute;gories --</option>
        @foreach($categories as $categorie)
        <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByLibbele" placeholder="Rechercher par nom de l'article">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByCode" placeholder="Rechercher par code barre">
    </div>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('parametre',['action'=>'liste-articles'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="true">
    <thead>
        <tr>
            <th data-field="code_barre">Code barre</th>
            <th data-field="description_article" data-searchable="true" data-sortable="true">Article</th>
            <th data-field="categorie.libelle_categorie">Cat&eacute;gorie </th>
            <th data-field="sous_categorie.libelle_sous_categorie" data-visible="false">Sous cat&eacute;gorie </th>
            <th data-align="center" data-formatter="alertFormatter">En stock </th>
            <th data-field="prix_achat_ttc" data-formatter="montantFormatter">Prix Achat TTC</th>
            <th data-formatter="prixAchatHtFormatter">Prix Achat HT</th>
            <th data-field="prix_vente_ttc_base" data-formatter="montantFormatter">Prix vente TTC</th>
            <th data-formatter="prixVenteHtFormatter">Prix vente HT</th>
            <th data-field="fournisseurs" data-formatter="fournisseursFormatter">Fournisseur(s)</th>
            <th data-formatter="tvaFormatter">TVA </th>
            <th data-field="rayon.libelle_rayon" data-visible="false">Rayon </th>
            <th data-field="rangee.libelle_rangee" data-visible="false">Rangee&eacute;e </th>
            <th data-field="poids_net" data-visible="false">Poids net </th>
            <th data-field="poids_brut" data-visible="false">Poids brut </th>
            <th data-visible="false" data-formatter="tauAirsiAchatFormatter">AIRSI acaht </th>
            <th data-visible="false" data-formatter="tauAirsiVenteFormatter">AIRSI vente </th>
            <th data-field="prix_vente_en_gros_base" data-formatter="montantFormatter" data-visible="false">PV en gros </th>
            <th data-field="prix_vente_demi_gros_base" data-formatter="montantFormatter" data-visible="false">PV demi gros </th>
            <th data-field="reference_interne" data-visible="false">R&eacute;ference </th>
            <th data-align="center" data-formatter="stockMinFormatter">Stock Min.</th>
            <th data-align="center" data-formatter="stockMaxFormatter" data-visible="false">Stock Max. </th>
            <th data-field="image_article" data-align="center" data-formatter="imageFormatter" data-visible="false">Image </th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 75%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-cubes fa-2x"></i>
                        Gestion des articles
                    </span>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" id="idArticleModifier" name="idArticle" ng-hide="true" ng-model="article.id"/>
                    @csrf
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#article_info" data-toggle="tab" aria-expanded="true">D&eacute;tails de l'article</a>
                            </li>
                            <li class="">
                                <a href="#info_supl" data-toggle="tab" aria-expanded="true">Infos suplementaires</a>
                            </li>
                        </ul> 
                        <div class="tab-content">  
                            <div class="tab-pane active" id="article_info">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Code barre</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-barcode"></i>
                                                </div>
                                                <input type="text" class="form-control" ng-model="article.code_barre" id="code_barre" name="code_barre" placeholder="Code barre de l'article">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>D&eacute;signation de l'article *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-edit"></i>
                                                </div>
                                                <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="article.description_article" id="description_article" name="description_article" placeholder="Désignation de l'article" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label>Selectionner les fournisseurs de cet article </label>
                                        <div class="form-group">
                                            <select name="fournisseurs[]" id="fournisseur_id" class="form-control select2" multiple="multiple">
                                                @foreach($fournisseurs as $fournisseur)
                                                <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cat&eacute;gorie *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <select name="categorie_id" id="categorie_id" ng-model="article.categorie_id" class="form-control" required>
                                                    <option value="" ng-show="false"> Selectionner la cat&eacute;gorie </option>
                                                    @foreach($categories as $categorie)
                                                    <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Sous cat&eacute;gorie </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <select name="sous_categorie_id" id="sous_categorie_id" class="form-control">
                                                    <option value=""> Sous cat&eacute;gorie </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Stock minimum </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-battery-quarter"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.stock_mini" id="stock_mini" name="stock_mini" placeholder="Stock minimum">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>TVA *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-text-height"></i>
                                                </div>
                                                <select name="param_tva_id" id="param_tva_id" ng-model="article.param_tva_id" ng-init="article.param_tva_id='0'" class="form-control">
                                                    @foreach($param_tvas as $tva)
                                                    <option selected data-tva="{{$tva->montant_tva}}" value="{{$tva->id}}"> {{$tva->montant_tva*100}}%</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix d'achat TTC *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" pattern="[0-9]*" class="form-control" ng-model="article.prix_achat_ttc" id="prix_achat_ttc" name="prix_achat_ttc" placeholder="Prix d'achat ttc" required>                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix d'achat HT </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" class="form-control"  id="prix_achat_ht" placeholder="Prix d'achat ht" readonly>                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Taux de marge </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle"></i>
                                                </div>
                                                <input type="text" class="form-control"  id="taux_marge" placeholder="Taux de marge" readonly>                                
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Image</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-photo"></i>
                                                </div>
                                                <input type="file" class="form-control" name="image_article">                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix de vente TTC *</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" pattern="[0-9]*" class="form-control" ng-model="article.prix_vente_ttc_base" id="prix_vente_ttc_base" name="prix_vente_ttc_base" placeholder="Prix de vente ttc" required>                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix de vente HT </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" class="form-control" id="prix_vente_ht" placeholder="Prix de vente ht" readonly>                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Taux de marque </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-circle"></i>
                                                </div>
                                                <input type="text" class="form-control" id="taux_marque" placeholder="Taux de marque" readonly>                                
                                            </div>
                                        </div>
                                    </div>
                                   <div class="col-md-3">
                                       <h5 class="text-bold text-red">
                                            <label><br/>
                                                <input type="checkbox" id="stockable" name="stockable" ng-model="article.stockable" ng-checked="article.stockable==0">&nbsp; Article non stockable
                                            </label>
                                         </h5>
                                    </div>
                                </div>
                            </div>    
                            <div class="tab-pane" id="info_supl">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix de vente en gros</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" pattern="[0-9]*" class="form-control" ng-model="article.prix_vente_en_gros_base" id="prix_vente_en_gros_base" name="prix_vente_en_gros_base" placeholder="Prix de vente en gros">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix de vente demi gros</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" pattern="[0-9]*" class="form-control" ng-model="article.prix_vente_demi_gros_base" id="prix_vente_demi_gros_base" name="prix_vente_demi_gros_base" placeholder="Prix de vente en demi gros">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Poids net</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-balance-scale"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.poids_net" id="poids_net" name="poids_net" placeholder="Poids net">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Poids brut</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-balance-scale"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.poids_brut" id="poids_brut" name="poids_brut" placeholder="Poids brut">                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                         <div class="form-group">
                                            <label>AIRSI achat</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-text-height"></i>
                                                </div>
                                                <select name="taux_airsi_achat" id="taux_airsi_achat" ng-model="article.taux_airsi_achat" class="form-control">
                                                    @foreach($param_tvas as $taux_airsi_achat)
                                                    <option selected data-tva="{{$tva->montant_tva}}" value="{{$taux_airsi_achat->id}}"> {{$taux_airsi_achat->montant_tva*100}}%</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Rayon</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-arrows-h"></i>
                                                </div>
                                                <select name="rayon_id" id="rayon_id" ng-model="article.rayon_id" class="form-control">
                                                    <option value="" ng-show="false">Selectionner le rayon </option>
                                                    @foreach($rayons as $rayon)
                                                    <option value="{{$rayon->id}}"> {{$rayon->libelle_rayon}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Rang&eacute;e</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-bars"></i>
                                                </div>
                                                <select name="rangee_id" id="rangee_id" ng-model="article.rangee_id" class="form-control">
                                                    <option value="" ng-show="false">Selectionner la rang&eacute;e </option>
                                                    @foreach($rangees as $rangee)
                                                    <option value="{{$rangee->id}}"> {{$rangee->libelle_rangee}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Volume</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-wifi"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.volume" id="volume" name="volume" placeholder="Volume">                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                         <div class="form-group">
                                            <label>AIRSI vente</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-text-height"></i>
                                                </div>
                                                <select name="taux_airsi_vente" id="taux_airsi_vente" ng-model="article.taux_airsi_vente" class="form-control">
                                                    @foreach($param_tvas as $taux_airsi_vente)
                                                    <option selected data-tva="{{$tva->montant_tva}}" value="{{$taux_airsi_vente->id}}"> {{$taux_airsi_vente->montant_tva*100}}%</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Stock maximum</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-battery-full"></i>
                                                </div>
                                                <input type="number" min="0" class="form-control" ng-model="article.stock_max" id="stock_max" name="stock_max" placeholder="Stock maximum">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>R&eacute;ference interne</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-keyboard-o"></i>
                                                </div>
                                                <input type="text" class="form-control" ng-model="article.reference_interne" id="reference_interne" name="reference_interne" placeholder="Code réference interne">                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix pond TTC</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" pattern="[0-9]*" class="form-control" ng-model="article.prix_pond_ttc" id="prix_pond_ttc" name="prix_pond_ttc" placeholder="Prix pond ttc">                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Prix pond HT </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-money"></i>
                                                </div>
                                                <input type="text" class="form-control"  id="prix_pond_ht" placeholder="Prix pond HT" readonly>                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Taille</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-text-height"></i>
                                                </div>
                                                <select name="taille_id" id="taille_id" ng-model="article.taille_id" class="form-control">
                                                    <option value=""> Selectionner la taille</option>
                                                    @foreach($tailles as $taille)
                                                    <option value="{{$taille->id}}"> {{$taille->libelle_taille}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-12"> 
                            <h5 class="text-bold text-green">
                                <label>
                                    Liste des d&eacute;pots de cet article
                                </label>
                            </h5>
                        </div>
                    </div>
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>D&eacute;p&ocirc;t *</label>
                                    <select id="depot" class="form-control">
                                        <option value="">Choisir un d&eacute;p&ocirc;t </option>
                                        @foreach($depots as $depot)
                                        <option data-libelle="{{$depot->libelle_depot}}" value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Colis *</label>
                                    <select class="form-control" id="unite">
                                    <option value="" ng-show="false">-- Colis--</option>
                                        @foreach($unites as $unite)
                                        <option data-libelleunite="{{$unite->libelle_unite}}" value="{{$unite->id}}"> {{$unite->libelle_unite}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Prix de vente *</label>
                                    <input type="number" min="0" class="form-control" id="prix_vente" placeholder="prix de vente">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-xs  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div><br/>
                        <table class="table table-info table-striped box box-success">
                            <thead>
                                <tr>
                                    <th>Cochez</th>
                                    <th>D&eacute;p&ocirc;t</th>
                                    <th>Colis</th>
                                    <th>Prix vente</th>
                                </tr>
                            </thead>
                            <tbody class="articles-depot-info">

                            </tbody>
                        </table>
                        <button type="button" class="delete-row">Supprimer ligne</button>
                    </div>
                     <div id="div_update">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="btnModalAjoutArticleDepot" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus">Ajouter un enregistrement</i></button>
                                </div>
                            </div> 
                        </div><br/>
                        <table id="tableArticleDepot" class="table table-success table-striped box box-success"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t</th>
                                    <th data-field="unite.libelle_unite">Unit&eacute; </th>
                                    <th data-field="prix_vente" data-formatter="montantFormatter">Prix de vente </th>
                                    <th data-field="id" data-formatter="optionArticleDepotFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal add depot-article -->
<div class="modal fade bs-modal-add-article-depot" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:60%">
        <form id="formAjoutArticleDepot" ng-controller="formAjoutArticleDepotCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Ajout d'un enregistrement
                </div>
                @csrf
                <div class="modal-body ">
                   <input type="text" class="hidden" id="idDepotArticleModifier"  ng-model="depotArticle.id"/>
                   <input type="text" class="hidden" id="article_id"  name="article_id"/>
                   <div class="row">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>D&eacute;p&ocirc;t *</label>
                               <select name="depot_id" id="depot_id_add" ng-model="depotArticle.depot_id" ng-init="deporArticle.depot_id=''" class="form-control" required>
                                   <option value="" ng-show="false">Choisir un d&eacute;p&ocirc;t </option>
                                   @foreach($depots as $depot)
                                   <option data-libelle="{{$depot->libelle_depot}}" value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                                    <label>Colis *</label>
                                    <select name="unite_id" id="unite_add" ng-model="depotArticle.unite_id" ng-init="deporArticle.unite_id=''" class="form-control" required>
                                    <option value="" ng-show="false">-- Colis--</option>
                                        @foreach($unites as $unite)
                                        <option data-libelleunite="{{$unite->libelle_unite}}" value="{{$unite->id}}"> {{$unite->libelle_unite}}</option>
                                        @endforeach
                                    </select>
                                </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Prix de vente *</label>
                               <input type="number" class="form-control" min="0" id="prix_vente_add" name="prix_vente" ng-model="depotArticle.prix_vente" placeholder="prix de vente" required>
                           </div>
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion -->
<div class="modal fade bs-modal-suppression" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimer" ng-controller="formSupprimerCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idArticleSupprimer"  ng-model="article.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'article <br/><b>@{{article.description_article}}</b></div>
                        <div class="text-center vertical processing">Suppression en cours</div>
                        <div class="pull-right">
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Non</button>
                            <button type="submit" class="btn btn-danger btn-sm ">Oui</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion depot-article-->
<div class="modal fade bs-modal-supprimer-depot-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimerDepotArticle" ng-controller="formSupprimerDepotArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idDepotArticleSupprimer"  ng-model="depotArticle.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le d&eacute;p&ocirc;t <br/><b>@{{depotArticle.depot.libelle_depot}}</b></div>
                        <div class="text-center vertical processing">Suppression en cours</div>
                        <div class="pull-right">
                            <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Non</button>
                            <button type="submit" class="btn btn-danger btn-sm ">Oui</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    var ajout = true;
    var ajoutArticleDepot = true;
    var $table = jQuery("#table"), rows = [], $tableArticleDepot = jQuery("#tableArticleDepot"), rowsArticleDepot = [];
    
    appSmarty.controller('formAjoutCtrl', function ($scope) { 
        $scope.populateForm = function (article) {
            $scope.article = article;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.article = {};
        };
    });
    appSmarty.controller('formAjoutArticleDepotCtrl', function ($scope) { 
        $scope.populateFormModif = function (depotArticle) {
            $scope.depotArticle = depotArticle;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.depotArticle = {};
        };
    });
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (article) {
            $scope.article = article;
        };
        $scope.initForm = function () {
            $scope.article = {};
        };
    });
     appSmarty.controller('formSupprimerDepotArticleCtrl', function ($scope) {
        $scope.populateFormSup = function (depotArticle) {
            $scope.depotArticle = depotArticle;
        };
        $scope.initForm = function () {
            $scope.depotArticle = {};
        };
    });
    
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        $tableArticleDepot.on('load-success.bs.table', function (e, data) {
            rowsArticleDepot = data.rows; 
        });
        $("#fournisseur_id").select2({width: '100%'});
        $("#div_enregistrement").show();
        $("#div_update").hide();
        $("#prix_achat_ttc").keyup(function (e) { 
            var prix_achat_ttc = $("#prix_achat_ttc").val();
            var tva = $("#param_tva_id").children(":selected").data("tva");
            var prix_achat_ht = (prix_achat_ttc/(tva + 1));
            var prix = Math.round(prix_achat_ht);
            $("#prix_achat_ht").val(prix);
            //Calcule de marge et de marque
            if($("#prix_vente_ht").val()!=""){
                var prix_vente = $("#prix_vente_ht").val();
                var prix_achat = $("#prix_achat_ht").val();
                var marge_commercial = parseInt(prix_vente) - parseInt(prix_achat);
                var taux_marge = (marge_commercial/prix_achat)*100;
                var taux_marque = (marge_commercial/prix_vente)*100;
                var taux_marg = Math.round(taux_marge);
                var taux_marq = Math.round(taux_marque);
                $("#taux_marge").val(taux_marg);
                $("#taux_marque").val(taux_marq);
            }
        });
        $("#prix_vente_ttc_base").keyup(function (e) { 
            var prix_vente_ttc_base = $("#prix_vente_ttc_base").val();
            var tva = $("#param_tva_id").children(":selected").data("tva");
            var prix_vente_ht = (prix_vente_ttc_base/(tva + 1));
            var prix = Math.round(prix_vente_ht);
            $("#prix_vente_ht").val(prix);
            //Calcule de marge et de marque
            if($("#prix_achat_ht").val()!=""){
                var prix_vente = $("#prix_vente_ht").val();
                var prix_achat = $("#prix_achat_ht").val();
                var marge_commercial = parseInt(prix_vente) - parseInt(prix_achat);
                var taux_marge = (marge_commercial/prix_achat)*100;
                var taux_marque = (marge_commercial/prix_vente)*100;
                var taux_marg = Math.round(taux_marge);
                var taux_marq = Math.round(taux_marque);
                $("#taux_marge").val(taux_marg);
                $("#taux_marque").val(taux_marq);
            }
        });
        $("#prix_pond_ttc").keyup(function (e) { 
            var prix_pond_ttc = $("#prix_pond_ttc").val();
            var tva = $("#param_tva_id").children(":selected").data("tva");
            var prix_pond_ht = (prix_pond_ttc/(tva + 1));
            var prix = Math.round(prix_pond_ht);
            $("#prix_pond_ht").val(prix);
        });
        $("#param_tva_id").change(function (e) {
            var tva = $("#param_tva_id").children(":selected").data("tva");
            if($("#prix_achat_ttc").val()!=""){
                var prix_achat_ttc = $("#prix_achat_ttc").val();
                var tva = $("#param_tva_id").children(":selected").data("tva");
                var prix_achat_ht = (prix_achat_ttc/(tva + 1));
                var prix = Math.round(prix_achat_ht);
                $("#prix_achat_ht").val(prix);
                //Calcule de marge et de marque
                if($("#prix_vente_ht").val()!=""){
                    var prix_vente = $("#prix_vente_ht").val();
                    var prix_achat = $("#prix_achat_ht").val();
                    var marge_commercial = parseInt(prix_vente) - parseInt(prix_achat);
                    var taux_marge = (marge_commercial/prix_achat)*100;
                    var taux_marque = (marge_commercial/prix_vente)*100;
                    var taux_marg = Math.round(taux_marge);
                    var taux_marq = Math.round(taux_marque);
                    $("#taux_marge").val(taux_marg);
                    $("#taux_marque").val(taux_marq);
                }
            }
            if($("#prix_vente_ttc_base").val()!=""){
                var prix_vente_ttc_base = $("#prix_vente_ttc_base").val();
                var tva = $("#param_tva_id").children(":selected").data("tva");
                var prix_vente_ht = (prix_vente_ttc_base/(tva + 1));
                var prix = Math.round(prix_vente_ht);
                $("#prix_vente_ht").val(prix);
                //Calcule de marge et de marque
                if($("#prix_achat_ht").val()!=""){
                    var prix_vente = $("#prix_vente_ht").val();
                    var prix_achat = $("#prix_achat_ht").val();
                    var marge_commercial = parseInt(prix_vente) - parseInt(prix_achat);
                    var taux_marge = (marge_commercial/prix_achat)*100;
                    var taux_marque = (marge_commercial/prix_vente)*100;
                    var taux_marg = Math.round(taux_marge);
                    var taux_marq = Math.round(taux_marque);
                    $("#taux_marge").val(taux_marg);
                    $("#taux_marque").val(taux_marq);
                }
            }
            if($("#prix_pond_ttc").val()!=""){
                var prix_pond_ttc = $("#prix_pond_ttc").val();
                var tva = $("#param_tva_id").children(":selected").data("tva");
                var prix_pond_ht = (prix_pond_ttc/(tva + 1));
                var prix = Math.round(prix_pond_ht);
                $("#prix_pond_ht").val(prix);
            }
        });
        
        $("#searchByCategorie").change(function (e) {
            var categorie = $("#searchByCategorie").val();
            if(categorie == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('parametre', ['action' => 'liste-articles'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../parametre/liste-articles-by-categorie/' + categorie});
            }
        });
        $("#searchByLibbele").keyup(function (e) {
            var libelle = $("#searchByLibbele").val();
            if(libelle == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('parametre', ['action' => 'liste-articles'])}}"});
            } else{
              $table.bootstrapTable('refreshOptions', {url: '../parametre/article-by-name/' + libelle});
            }
        });
        $("#searchByCode").keyup(function (e) {
            var code = $("#searchByCode").val();
            if(code == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('parametre', ['action' => 'liste-articles'])}}"});
            } else{
              $table.bootstrapTable('refreshOptions', {url: '../parametre/article-by-code/' + code});
            }
        });
       
        $("#categorie_id").change(function (e) {
            var categorie_id = $("#categorie_id").val();
            $.getJSON("../parametre/liste-sous-famille-by-categorie/" + categorie_id, function (reponse) {
                $('#sous_categorie_id').html("<option value=''>-- Sous catégorie --</option>");
                $.each(reponse.rows, function (index, sous_categorie) { 
                    $('#sous_categorie_id').append('<option value=' + sous_categorie.id + '>' + sous_categorie.libelle_sous_categorie + '</option>')
                });
            });
        });
        
        $("#btnModalAjoutArticleDepot").on("click", function () {
            ajoutArticleDepot = true;
            var article = $("#idArticleModifier").val();
            document.forms["formAjoutArticleDepot"].reset();
            $("#article_id").val(article);
            $(".bs-modal-add-article-depot").modal("show");
        });
        
        $("#btnModalAjout").on("click", function () {
            $("#fournisseur_id").select2("val", "");
            $("#prix_achat_ht").val("");
            $("#param_tva_id").val(0);
            $("#prix_vente_ht").val("");
            $("#prix_pond_ht").val("");
            $("#taux_marge").val("");
            $("#taux_marque").val("");
            $("#div_enregistrement").show();
            $("#div_update").hide();
        });
        
        $(".add-row").click(function (e) {
            if($("#depot").val() != '' && $("#prix_vente").val() != '' && $("#unite").val()!= '') {
                var libelle_depot = $("#depot").children(":selected").data("libelle");
                var libelle_unite = $("#unite").children(":selected").data("libelleunite");
                var depot = $("#depot").val();
                var prix_vente = $("#prix_vente").val();
                var unite = $("#unite").val();

                var markup = "<tr><td><input type='checkbox' name='record'></td><td><input type='hidden' name='depots[]' value='" + depot + "'>" + libelle_depot + "</td><td><input type='hidden' name='unites[]' value='" + unite + "'>" + libelle_unite + "</td><td><input type='hidden' name='prix_ventes[]' value='" + prix_vente + "'>" + prix_vente + "</td></tr>";
                $(".articles-depot-info").append(markup);
                $("#depot").val("");
                $("#unite").val("");
                $("#prix_vente").val("");
            }else{
                alert("Les champs dépôt, colis et prix de vente ne doivent pas être vide!");
            }
        });
        
         // Find and remove selected table rows 
        $(".delete-row").click(function () {
            $(".articles-depot-info").find('input[name="record"]').each(function () {
                if ($(this).is(":checked")) {
                    $(this).parents("tr").remove();
                }else{
                   alert("Cochez la ligne que vous souhaitez supprimer !"); 
                }
            });
        });
        
        $("#formAjout").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjout .loader-overlay");

             if (ajout==true) {
                var methode = 'POST';
                var url = "{{route('parametre.articles.store')}}";
             }else{
                var methode = 'POST';
                var url = "{{route('parametre.update-article')}}";
             }
             var formData = new FormData($(this)[0]);
            editerArticleAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });
        
        $("#formAjoutArticleDepot").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutArticleDepot .loader-overlay");

             if (ajoutArticleDepot==true) {
                var methode = 'POST';
                var url = "{{route('boutique.depot-articles.store')}}";
             }else{
                var id = $("#idDepotArticleModifier").val();
                var methode = 'PUT';
                var url = '../boutique/depot-articles/' + id;
             }
            editerArticleDepotAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticleDepot, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('articles/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
        
         $("#formSupprimerDepotArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idDepotArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerDepotArticle .question");
            var $ajaxLoader = $("#formSupprimerDepotArticle .processing");
            supprimerDepotArticleAction('../boutique/depot-articles/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticleDepot);
        });
    });
    
    function updateRow(idArticle) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var article =_.findWhere(rows, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateForm(article);
        });
        var ids = article.fournisseurs.map(function(fournisseur){
            return fournisseur.id;
        });
        $("#fournisseur_id").select2("val", ids);
        $tableArticleDepot.bootstrapTable('refreshOptions', {url: "../boutique/liste-depot-by-article/" + idArticle});
        $("#div_enregistrement").hide();
        $("#div_update").show();
        if(article.sous_categorie_id!=null){
            $.getJSON("../parametre/liste-sous-famille-by-categorie/" + article.categorie_id, function (reponse) {
                $('#sous_categorie_id').html("<option value=''>-- Sous catégorie --</option>");
                $.each(reponse.rows, function (index, sous_categorie) { 
                    $('#sous_categorie_id').append('<option value=' + sous_categorie.id + '>' + sous_categorie.libelle_sous_categorie + '</option>')
                });
                 $('#sous_categorie_id').val(article.sous_categorie_id);
            });
        }
        //TVA
        article.param_tva_id !=null ? tva = article.param_tva.montant_tva: tva = 0;
        article.param_tva_id !=null ? tvaId = article.param_tva_id : tvaId = 0;
        $("#param_tva_id").val(tvaId);
        //AIRSI chat et vente
        article.taux_airsi_achat !=null ? taux_airsi_achat_id = article.taux_airsi_achat : taux_airsi_achat_id = 0;
        article.taux_airsi_vente !=null ? taux_airsi_vente_id = article.taux_airsi_vente : taux_airsi_vente_id = 0;
        $("#taux_airsi_achat").val(taux_airsi_achat_id);
        $("#taux_airsi_vente").val(taux_airsi_vente_id);
        //Achat HT
        var prix_achat_ttc = article.prix_achat_ttc;
        var prix_achat_ht = (prix_achat_ttc/(tva + 1));
        var prixA = Math.round(prix_achat_ht);
        $("#prix_achat_ht").val(prixA);
        
        //Vente HT
        var prix_vente_ttc_base = article.prix_vente_ttc_base;
        var prix_vente_ht = (prix_vente_ttc_base/(tva + 1));
        var prixV = Math.round(prix_vente_ht);
        $("#prix_vente_ht").val(prixV);
        
        //Marge commerciale
        var marge_commercial = parseInt(prixV) - parseInt(prixA);
        var taux_marge = (marge_commercial/prixA)*100;
        var taux_marque = (marge_commercial/prixV)*100;
        var taux_marg = Math.round(taux_marge);
        var taux_marq = Math.round(taux_marque);
        $("#taux_marge").val(taux_marg);
        $("#taux_marque").val(taux_marq);
        
        var prix_pond_ttc = article.prix_pond_ttc;
        var prix_pond_ht = (prix_pond_ttc/(tva + 1));
        var prixP = Math.round(prix_pond_ht);
        $("#prix_pond_ht").val(prixP);
     
        $(".bs-modal-ajout").modal("show");
    }
    function updateDepoArticleRow(idArticleDepot) {
        ajoutArticleDepot = false;
        var $scope = angular.element($("#formAjoutArticleDepot")).scope();
        var depotArticle =_.findWhere(rowsArticleDepot, {id: idArticleDepot});
         $scope.$apply(function () {
            $scope.populateFormModif(depotArticle);
        });
         $(".bs-modal-add-article-depot").modal("show");
    }
    function deleteRow(idArticle) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var article =_.findWhere(rows, {id: idArticle});
           $scope.$apply(function () {
              $scope.populateForm(article);
          });
       $(".bs-modal-suppression").modal("show");
    }
    function deleteDepoArticleRow(idArticleDepot) {
          var $scope = angular.element($("#formSupprimerDepotArticle")).scope();
          var depotArticle =_.findWhere(rowsArticleDepot, {id: idArticleDepot});
           $scope.$apply(function () {
              $scope.populateFormSup(depotArticle);
          });
       $(".bs-modal-supprimer-depot-article").modal("show");
    }
    function fournisseursFormatter(fournisseurs){
        var strFournisseurs = '';
            $.each(fournisseurs, function (index, fournisseur) {
                strFournisseurs += '<span class="label label-success">' + fournisseur.full_name_fournisseur + '</span>' + ' ';
            });
            return strFournisseurs;
    }
    
    function tvaFormatter(id, row){
        if(row.param_tva_id==null){
            return "Pas de TVA";
        }else{
            var tva = row.param_tva.montant_tva*100;
            return '<span class="text-bold">' + tva.toFixed(2) + ' %' + '</span>';
        }
    }
    function prixVenteHtFormatter(id, row){
        var prix_vente_ttc = row.prix_vente_ttc_base;
        row.param_tva_id !=null ? tva = row.param_tva.montant_tva : tva = 0;
        var prix_vente_ht = (prix_vente_ttc/(tva + 1));
        var prix = Math.round(prix_vente_ht);
        return '<span class="text-bold">' +prix+ '</span>';
    }
    function prixAchatHtFormatter(id, row){
        var prix_achat = row.prix_achat_ttc;
        row.param_tva_id !=null ? tva = row.param_tva.montant_tva : tva = 0;
        var prix_achat_ht = (prix_achat/(tva + 1));
        var prix = Math.round(prix_achat_ht);
        return '<span class="text-bold">' +prix+ '</span>';
    }
    function tauAirsiAchatFormatter(id,row){
        if(row.taux_airsi_achat==null){
            return "-";
        }else{
            var taux = row.airsi_achat.montant_tva*100;
            return '<span class="text-bold">' + taux.toFixed(2) + ' %' + '</span>';
        }
    }
    function tauAirsiVenteFormatter(id,row){
        if(row.taux_airsi_vente==null){
            return "-";
        }else{
            var taux = row.airsi_vente.montant_tva*100;
            return '<span class="text-bold">' + taux.toFixed(2) + ' %' + '</span>';
        }
    }
    
    function alertFormatter(id, row){
       if(row.stockable==0){
           return '<span class="text-bold">Non stockable</span>';
       }else{
         if(row.totalStock<=row.stock_mini){
            return '<span class="label label-danger">' + $.number(row.totalStock) + '</span>';
        }else{
            return '<span class="label label-success">' +  $.number(row.totalStock) + '</span>';
        }  
       }
    }
    function imageFormatter(image) { 
          return image ? "<a target='_blank' href='" + basePath + '/' + image + "'>Voir le l'image</a>" : "";
    }
    function montantFormatter(montant){
        return montant ? '<span class="text-bold">' + $.number(montant)+ '</span>' : "--";
    }
    
    function stockMinFormatter(id, row){
        return row.stockable ? '<span class="text-bold">' + $.number(row.stock_mini)+ '</span>' : '<span class="text-bold"> Non stockable </span>';
    }
    function stockMaxFormatter(id, row){
        return row.stockable ? '<span class="text-bold">' + $.number(row.stock_max)+ '</span>' : '<span class="text-bold"> Non stockable </span>';
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function optionArticleDepotFormatter(id, row) {
        return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateDepoArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteDepoArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function editerArticleAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                    $("#fournisseur_id").select2("val", "");
                    $("#prix_achat_ht").val("");
                    $("#prix_vente_ht").val("");
                    $("#prix_pond_ht").val("");
                    $("#taux_marge").val("");
                    $("#taux_marque").val("");
                    $("#param_tva_id").val(0);
                    $(".articles-depot-info").find('input[name="record"]').each(function () {
                        $(this).parents("tr").remove();
                    });
                    
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
         },
          error: function (err) {
            var res = eval('('+err.responseText+')');
            var messageErreur = res.message;
            
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: messageErreur,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
         beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        },
    });
};
    
    function editerArticleDepotAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajoutArt = true) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-add-article-depot").modal("hide");
                    ajout = false;
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-add-article-depot").modal("hide");
                    ajout = false;
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
         },
          error: function (err) {
            var res = eval('('+err.responseText+')');
            var messageErreur = res.message;
            
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: messageErreur,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
            $formObject.removeAttr("disabled");
            $ajoutLoader.hide();
        },
         beforeSend: function () {
            $formObject.attr("disabled", true);
            $ajoutLoader.show();
        },
        complete: function () {
            $ajoutLoader.hide();
        },
    });
};

 //Supprimer un dépôt
    function supprimerDepotArticleAction(url, formData, $question, $ajaxLoader, $table) {
    jQuery.ajax({
        type: 'DELETE',
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                 $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [reponse.data.id]
                });
                $table.bootstrapTable('refresh');
                $(".bs-modal-supprimer-depot-article").modal("hide");
                ajout = false;
            }
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: reponse.msg,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png",
            });
        },
        error: function (err) {
            var res = eval('('+err.responseText+')');
            //alert(res.message);
            //alert(Object.getOwnPropertyNames(res));
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: res.message,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png"
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


