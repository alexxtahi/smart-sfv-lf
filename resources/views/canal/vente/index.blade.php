@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Agence' or Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByFacture" placeholder="Rechercher par N° facture">
    </div>
</div>
@if(Auth::user()->role!='Agence')
<div class="col-md-4">
    <select class="form-control" id="searchByAgence">
        <option value="0">-- Toutes les agences --</option>
        @foreach($agences as $agence)
        <option value="{{$agence->id}}"> {{$agence->libelle_agence}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-2">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>
@endif
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-url="{{url('canal',['action'=>'liste-ventes-materiels'])}}"
               data-toggle="table"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Agence')
            <th data-formatter="factureFormatter" data-align="center" data-width="60px"><i class="fa fa-print"></i></th>
            @endif
            <th data-field="date_ventes">Date </th>
            <th data-field="numero_facture">Facture </th>
            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant TTC</th>
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
            <th data-field="agence.libelle_agence">Agence</th>
            @endif
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Agence')
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
            @endif
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 70%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-tv fa-2x"></i>
                        Gestion des ventes de mat&eacute;riel
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idVenteModifier" name="idVente" ng-hide="true" ng-model="vente.id"/>
                    @csrf
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mat&eacute;riel *</label>
                                    <select id="materiel" class="form-control">
                                        <option value="">-- Selectionner le mat&eacute;riel --</option>
                                        @foreach($materiels as $materiel)
                                        <option data-libellemateriel="{{$materiel->libelle_materiel}}" value="{{$materiel->id}}"> {{$materiel->libelle_materiel}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; *</label>
                                    <input type="number" min="1" class="form-control" id="quantite" placeholder="Quantité">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix Unitaire</label>
                                    <input type="number" min="0" class="form-control" id="prixTTC" placeholder="Prix unitaire">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Montant TTC</label>
                                    <input type="text" class="form-control" id="montantTC" placeholder="Montant" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-sm  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-xs delete-row">Supprimer ligne</button><br/><br/>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tableAddRowArticle" class="table table-success table-striped box box-success"
                                       data-toggle="table"
                                       data-id-field="id"
                                       data-unique-id="id"
                                       data-click-to-select="true"
                                       data-show-footer="false">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th data-field="id">ID</th>
                                            <th data-field="libelle">Mat&eacute;riel</th>
                                            <th data-field="prix_ttc">Prix Unitaire</th>
                                            <th data-field="quantite">Qt&eacute;</th>
                                            <th data-field="montant_ttc">Montant TTC</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="div_update">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="btnModalAjoutArticle" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus">Ajouter un article</i></button>
                                </div>
                            </div> 
                        </div><br/>
                        <table id="tableArticle" class="table table-success table-striped box box-success"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="materiel.libelle_materiel">Mat&eacute;riel</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix Unitaire</th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant </th>
                                    <th data-field="id" data-formatter="optionAArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                        <div class="row"> 
                            <div class="col-md-6"><br/>
                                <ul class="nav nav-stacked" style="font-size: 15px;">
                                    <li><a class="text-bold" >Montant Total<span id="montantTTTC_add" class="pull-right text-bold  text-red"></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row" id="row_regle"> 
                        <div class="col-md-6"><br/>
                            <ul class="nav nav-stacked" style="font-size: 15px;">
                                <input type="text" class="hidden" name="montantTTC_input" id="montantTTC_input"/>
                                <li><a class="text-bold">Montant Total<span class="pull-right text-bold text-red montantTTC"></span></a></li>
                            </ul>
                        </div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="sendButton" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal add article -->
<div class="modal fade bs-modal-add-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
        <form id="formAjoutArticle" ng-controller="formAjoutArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Ajout d'un mat&eacute;riel
                </div>
                @csrf
                <div class="modal-body ">
                   <input type="text" class="hidden" id="idArticleModifier"  ng-model="article.id"/>
                   <input type="text" class="hidden" id="vente"  name="vente_id"/>
                   <div class="row">
                       <div class="col-md-4">
                           <div class="form-group">
                               <label>Libell&eacute; *</label>
                               <select id="materiel_add" name="materiel_id" ng-model="article.materiel_id" class="form-control">
                                   <option value="">-- Selectionner le mat&eacute;riel --</option>
                                    @foreach($materiels as $materiel)
                                        <option value="{{$materiel->id}}"> {{$materiel->libelle_materiel}}</option>
                                    @endforeach
                               </select>
                           </div>
                       </div>
                        <div class="col-md-2">
                           <div class="form-group">
                               <label>Qt&eacute; *</label>
                               <input type="number" min="0" name="quantite" ng-model="article.quantite" class="form-control" id="quantite_add" placeholder="Qté à vendre" required>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Prix unitaire</label>
                               <input type="number" min="0" class="form-control" name="prix" ng-model="article.prix" id="prixTTC_add" placeholder="Prix TTC" required>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="form-group">
                               <label>Montant</label>
                               <input type="text" class="form-control" id="montantTC_add" placeholder="Montant" readonly>
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

<!-- Modal suppresion article-->
<div class="modal fade bs-modal-supprimer-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimerArticle" ng-controller="formSupprimerArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idArticleSupprimer"  ng-model="article.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le mat&eacute;riel <br/><b>@{{article.materiel.libelle_materiel}}</b></div>
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

<!-- Modal suppresion-->
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
                    <input type="text" class="hidden" id="idVenteSupprimer"  ng-model="vente.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer cette vente <br/><b>@{{vente.numero_facture}}</b></div>
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

<!-- Modal panier -->
<div class="modal fade bs-modal-panier" id="panierArticle" ng-controller="panierArticleCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                     Panier de la facture N° <b>@{{vente.numero_facture}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="tablePanierArticle" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="materiel.libelle_materiel">Mat&eacute;riel</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix unitaire</th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant</th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<input type="hidden" id="agence_auth" value="{{Auth::user()->agence_id}}"/>

<script type="text/javascript">
    var ajout = false; 
    var ajoutArticle = false;
    var montantTTC = 0;
    var $table = jQuery("#table"), rows = [],$tablePanierArticle = jQuery("#tablePanierArticle"),$tableListeArticle = jQuery("#tableListeArticle"), $tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle");
    var monPanier = [];
    var idTablle =  0;
    
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (vente) {
        $scope.vente = vente;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.vente = {};
        };
    }); 
    
    appSmarty.controller('formAjoutArticleCtrl', function ($scope) {
        $scope.populateArticleForm = function (article) {
        $scope.article = article;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.article = {};
        };
    }); 
    
    appSmarty.controller('formSupprimerArticleCtrl', function ($scope) {
        $scope.populateSupArticleForm = function (article) {
        $scope.article = article;
        };
        $scope.initForm = function () {
        $scope.article = {};
        };
    });
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (vente) {
        $scope.vente = vente;
        };
        $scope.initForm = function () {
        $scope.vente = {};
        };
    });
    
    appSmarty.controller('formReglementClientAnonymeCtrl', function ($scope) {
        $scope.populateClientAnonymeForm = function (vente) {
            $scope.vente = vente;
        };
        $scope.initForm = function () {
            $scope.vente = {};
        };
    }); 
    
    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
        $scope.vente = vente;
        };
    });

    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        
        $tableArticle.on('load-success.bs.table', function (e, data) {
             rowsArticle = data.rows;
            $("#montantTTTC_add").html($.number(data.montantTTTC_add));
        });
        
        $('#searchByDate, #date_vente').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        
        $("#div_enregistrement").show();
        $("#div_update").hide();
        $("#row_regle").hide();
        $(".delete-row").hide();
 
        $("#searchByDate").change(function (e) {
            var agence_auth = $("#agence_auth").val();
            
            if(agence_auth!=null){
                $("#searchByAgence").val(0);
            }
            $("#searchByFacture").val("");
            var date = $("#searchByDate").val();
            if(date == ""){
                 $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-ventes-materiels'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../canal/liste-vente-materiel-by-date/' + date});
            }
         });
         
        $("#searchByFacture").keyup(function (e) {
            $("#searchByAgence").val(0);
            $("#searchByDate").val("");
            var numero_facture = $("#searchByFacture").val();
            if(numero_facture == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-ventes-materiels'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-vente-materiel-by-facture/' + numero_facture});
            }
        });
        
        $("#searchByAgence").change(function (e) {
            $("#searchByFacture").val("");
             if(agence_auth!=null){
               $("#searchByDate").val("");  
             }
            
            var agence = $("#searchByAgence").val();
            if(agence == 0){
                 $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-ventes-materiels'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../canal/liste-vente-materiel-by-agence/' + agence});
            }
         });
        
        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            var vente = $("#idVenteModifier").val();
            document.forms["formAjoutArticle"].reset();
            $("#vente").val(vente);
            $(".bs-modal-add-article").modal("show");
        });
        $("#btnModalAjout").on("click", function () {
            ajout = true;
            $("#row_regle").hide();
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $(".delete-row").hide();
            $tableAddRowArticle.bootstrapTable('removeAll');
            monPanier = [];
            idTablle =  0;
            montantTTC = 0;
            $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
        });

        $("#quantite").change(function (e) { 
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
            if(quantite!=null && prix!=null){
                $("#montantTC").val(quantite*prix);
            }
        });
        $("#quantite").keyup(function (e) { 
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
          if(quantite!=null && prix!=null){
                $("#montantTC").val(quantite*prix);
            }
        });
        $("#quantite_add").change(function (e) { 
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          if(quantite!=null && prix!=null){
             $("#montantTC_add").val(quantite*prix); 
          }
        });
        $("#quantite_add").keyup(function (e) { 
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          if(quantite!=null && prix!=null){
             $("#montantTC_add").val(quantite*prix); 
          }
        });
        $("#prixTTC").change(function (e) { 
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
            if(quantite!=null && prix!=null){
                $("#montantTC").val(quantite*prix);
            }
        });
        $("#prixTTC").keyup(function (e) { 
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
            if(quantite!=null && prix!=null){
                $("#montantTC").val(quantite*prix);
            }
        });
        $("#prixTTC_add").change(function (e) { 
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          if(quantite!=null && prix!=null){
             $("#montantTC_add").val(quantite*prix); 
          }
        });
        $("#prixTTC_add").keyup(function (e) { 
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          if(quantite!=null && prix!=null){
             $("#montantTC_add").val(quantite*prix); 
          }
        });
        
        //Add row on table
        $(".add-row").click(function () {
            if($("#materiel").val() != '' && $("#quantite").val() != '' && $("#prixTTC").val() != '' && $("#quantite").val()!=0){
               
                var materiel = $("#materiel").children(":selected").data("libellemateriel");
                var materielId = $("#materiel").val();
                var quantite = $("#quantite").val();
                var prixTTC = $("#prixTTC").val();
              
                    //Vérification Si la ligne existe déja dans le tableau
                    var materielTrouver = _.findWhere(monPanier, {materiels: materielId})
                    if(materielTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = materielTrouver.quantites;
                        idElementLigne = materielTrouver.id;
                         var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);
                            //MAJ de la ligne
                            montantTTC = oldQte*prixTTC;
                            
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite : sommeDeuxQtes,
                                    montant_ttc: $.number(prixTTC*sommeDeuxQtes),
                                }
                            });
                            materielTrouver.quantites = sommeDeuxQtes;
                            montantTTC = parseInt(montantTTC) + parseInt(sommeDeuxQtes*prixTTC);
                            
                            $("#quantite").val("");
                            $("#prixTTC").val("");
                            $("#montantTC").val("");
                            $("#materiel").val("");
                            $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                            $("#montantTTC_input").val(montantTTC);
                            return;
                    }
                    idTablle++; 
                    $tableAddRowArticle.bootstrapTable('insertRow',{
                        index: idTablle,
                        row: {
                          id: idTablle,
                          libelle: materiel,
                          prix_ttc: $.number(prixTTC),
                          quantite: quantite,
                          materiels: materielId,
                          montant_ttc: $.number(quantite*prixTTC),
                        }
                    })
                    montantTTC = montantTTC + parseInt(quantite*prixTTC);
                    $("#montantTTC_input").val(montantTTC);
                    //Creation de l'article dans le tableau virtuel (panier)
                    var DataMateriel = {'id':idTablle, 'materiels':materielId, 'quantites':quantite,'prix':prixTTC};
                    monPanier.push(DataMateriel);
                    $("#quantite").val("");
                    $("#prixTTC").val("");
                    $("#montantTC").val("");
                    $("#materiel").val("");
                    $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                    if(idTablle>0)
                    {
                        $("#row_regle").show();
                        $(".delete-row").show();
                    }else{
                        $("#row_regle").hide();
                        $(".delete-row").hide();
                    }
                
            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs matériel, quantité et prix unitaire ne doivent pas être vides et la quantité minimum à vendre doit être 1.",
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                return;
            }
        })
        // Find and remove selected table rows  
        $(".delete-row").click(function () {
           var selecteds = $tableAddRowArticle.bootstrapTable('getSelections');
           var ids = $.map($tableAddRowArticle.bootstrapTable('getSelections'), function (row) {
                        return row.id
                    })
                $tableAddRowArticle.bootstrapTable('remove', {
                    field: 'id',
                    values: ids 
                })
              
                $.each(selecteds, function (index, value) { 
                    var materielTrouver = _.findWhere(monPanier, {id: value.id})
                    montantTTC = parseInt(montantTTC) - parseInt(materielTrouver.quantites*materielTrouver.prix);
                    monPanier = _.reject(monPanier, function (article) {
                        return article.id == value.id;
                    });
                });
              
                $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                $("#montantTTC_input").val(montantTTC);
                if(monPanier.length==0){
                    $("#row_regle").hide();
                    $(".delete-row").hide();
                    montantTTC = 0;
                    $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                    idTablle = 0;
                }
        });
        
        // Submit the add form
        $("#sendButton").click(function(){  
            $("#formAjout").submit(); 
            $("#sendButton").prop("disabled", true);
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
                var url = "{{route('canal.vente-materiels.store')}}";
                var formData = new FormData($(this)[0]);
                createFormData(formData, 'monPanier', monPanier);
            }else{
               var methode = 'POST';
                var url = "{{route('canal.update-vente-materiel')}}";
                 var formData = new FormData($(this)[0]);
            }
            editerVenteAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });
        $("#formAjoutArticle").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutArticle .loader-overlay");

            if (ajoutArticle==true) {
                var methode = 'POST';
                var url = "{{route('canal.materiels-vendues.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'materiels-vendues/' + id;
             }
            editerVentesArticlesAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle,$table, ajoutArticle);
        });
        $("#formAjoutClient").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutClient .loader-overlay");
            var methode = 'POST';
            var url = "{{route('parametre.clients.store')}}";
            editerClient(methode, url, $(this), $(this).serialize(), $ajaxLoader);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idVenteSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('vente-materiels/' + id, $(this).serialize(), $question, $ajaxLoader,$table);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('materiels-vendues/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle, $table);
        });
    });
    function createFormData(formData, key, data) {
        if (data === Object(data) || Array.isArray(data)) {
            for (var i in data) {
                createFormData(formData, key + '[' + i + ']', data[i]);
            }
        } else {
            formData.append(key, data);
        }
    }
    function updateRow(idVente) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
      
        $("#idVenteModifier").val(vente.id);
        $tableArticle.bootstrapTable('refreshOptions', {url: "../canal/liste-materiel-vendus/" + idVente})
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $(".bs-modal-ajout").modal("show");
    }
    function deleteRow(idVente){
        var $scope = angular.element($("#formSupprimer")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $(".bs-modal-suppression").modal("show");
    }
    function updateArticleRow(idArticle){
        ajoutArticle = false;
        var $scope = angular.element($("#formAjoutArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateArticleForm(article);
        });
        var vente = $("#idVenteModifier").val();
        $("#vente").val(vente);
         $("#prixTTC_add").val(article.prix);
        $("#montantTC_add").val(article.prix*article.quantite);
        $("#quantite_add").val(article.quantite);
        $(".bs-modal-add-article").modal("show");
    }
    function deleteArticleRow(idArticle){
        var $scope = angular.element($("#formSupprimerArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateSupArticleForm(article);
        });
        $(".bs-modal-supprimer-article").modal("show");
    }
    function listeArticleRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: "../canal/liste-materiel-vendus/" + idVente});
        $(".bs-modal-panier").modal("show");
    }
    
    function facturePrintRow(idVente){
        window.open("facture-vente-materiel-pdf/" + idVente ,'_blank')
    }
    
    function imprimePdf(){
        var agence = $("#searchByAgence").val();
        var date = $("#searchByDate").val();
        if(agence==0 && date==""){
            window.open("all-vente-materiel-pdf/",'_blank')
        }
        if(agence!=0 && date==""){
            window.open("all-vente-materiel-by-agence-pdf/" + agence,'_blank')
        }
        if(agence!=0 && date!=""){
            window.open("all-vente-materiel-by-agence-date-pdf/" + agence + "/" + date,'_blank')
        }
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function optionFormatter(id, row) { 
        if(row.acompte_facture>0){
            return '<button type="button" class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Liste des règlements" onClick="javascript:reglementRow(' + row.id + ');"><i class="fa fa-money"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
        }else{
            return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>\n\
                    <button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
        }
    }
    
    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    
    function optionAArticleFormatter(id, row) { 
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function imageFormatter(id, row) { 
          return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir la facture</a>" : "";
    }

    function factureFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Facture" onClick="javascript:facturePrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }
    
    function editerVenteAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $("#row_regle").hide();
                    $(".remise_add_row").show();
                    $("#div_enregistrement").show();
                    $("#div_update").hide();
                    $(".delete-row").hide();
                    $tableAddRowArticle.bootstrapTable('removeAll');
                    monPanier = [];
                    idTablle =  0;
                    montantTTC = 0;
                    $(".montantTTC").html("<b>" + $.number(montantTTC) +"</b>");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $("#row_regle").hide();
//                if(reponse.data.attente!=1){
//                  window.open("ticket-vente-pdf/" + reponse.data.id ,'_blank')  
//                }
                location.reload();
                $formObject.trigger('eventAjouter', [reponse.data]);
                $("#sendButton").prop("disabled", false);
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
            $("#sendButton").prop("disabled", false);
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
    function editerVentesArticlesAction(methode, url, $formObject, formData, $ajoutLoader, $table,$tableVente, ajoutArticle = true) {
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
                if (ajoutArticle) { //creation
                    $table.bootstrapTable('refresh');
                    $tableVente.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $tableVente.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
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
  
    function editerClient(methode, url, $formObject, formData, $ajoutLoader) {
        jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                $.getJSON("../parametre/last-client/", function (reponse) {
                    $('#client_id').html("<option value=''>-- Selectionner client --</option>");
                    var doit_client = 0;
                    $.each(reponse.rows, function (index, client) { 
                       $('#client_id').append("<option value=" + client.id + ">" + client.full_name_client + "</option>")
                        $("#client_id").select2("val",client.id)
                        $('#contact_client').val(client.contact_client)
                        $('#plafond_client').val(client.plafond_client)
                        $("#plafond_client_aff").html('Plafond ' + $.number(client.plafond_client)+' F CFA');
                        
                        $.getJSON("../parametre/get-all-doit-client/" + client.id, function (reponse) {
                            if(reponse.total>0){
                                $.each(reponse.rows, function (index, client_doi) { 
                                    doit_client = doit_client + client_doi.sommeTotale - client_doi.acompte_facture;
                                    $('#doit_client').val(doit_client)
                                    $("#client_doit_aff").html('Doit ' + $.number(doit_client)+' F CFA');
                                });
                            }else{
                                $('#doit_client').val(0)
                                $("#client_doit_aff").html('Doit ' + $.number(0)+' F CFA');
                            }
                        });
                    });
              });
            $(".bs-modal-ajout-client").modal("hide");
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
            if(res.message == "The given data was invalid."){
               // messageErreur = "Cet enregistrement existe dèjà";
                messageErreur = "Erreur survenue lors de l'enregistrement.";
            }
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
    }

    //Supprimer un article 
    function supprimerArticleAction(url, formData, $question, $ajaxLoader, $table, $tableVente) {
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
                $tableVente.bootstrapTable('refresh');
                $(".bs-modal-supprimer-article").modal("hide");
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


