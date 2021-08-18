@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Comptable')
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="row">
    <div class="col-md-12">
        <div class="box box-widget widget-user-2">
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle" src="{{asset('images/profil.png')}}" alt="User Avatar">
                </div>
                <h3 class="widget-user-username"><span class="text-bold">{{$infoClient->full_name_client}}</span></h3>
                <h5 class="widget-user-desc">{{$infoClient->contact_client}}</h5>
            </div>
        </div>
    </div>
</div>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#achat_info" data-toggle="tab" aria-expanded="true">Liste des achats</a>
        </li>
        <li class="">
            <a href="#reglement_info" data-toggle="tab" aria-expanded="true">Liste des r&egrave;glements</a>
        </li>
        <li class="">
            <a href="#article_achat_info" data-toggle="tab" aria-expanded="true">Articles les plus achet&eacute;s</a>
        </li>
    </ul> 
    <div class="tab-content">  
        <div class="tab-pane active" id="achat_info">
          
                    <div class="box-header">
                        <div class="col-md-2">
                            <h3 class="box-title pull-left">Liste des achats</h3>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" id="dateDebut" placeholder="Date du début">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" id="dateFin" placeholder="Date de fin">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchByFacture" placeholder="Rechercher par N° facture">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="table" class="table table-warning table-striped"
                               data-pagination="true"
                               data-search="false" 
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false"
                               data-show-columns="false">
                            <thead>
                                <tr>
                                    <th data-formatter="factureFormatter" data-align="center" data-width="60px"><i class="fa fa-print"></i></th>
                                    <th data-field="date_ventes">Date </th>
                                    <th data-formatter="typeFactureFormatter">Facture </th>
                                    <th data-formatter="depotFormatter">D&eacute;p&ocirc;t </th>
                                    <th data-field="sommeTotale" data-formatter="montantFormatter">Montant TTC</th>
                                    <th data-field="sommeRemise" data-formatter="montantFormatter">Remise</th>
                                    <th data-field="acompte_facture" data-formatter="montantFormatter">Acompte</th>
                                    <th data-formatter="resteFormatter">Reste</th>
                                    <th data-field="id" data-formatter="optionFormatter" data-width="60px" data-align="center"><i class="fa fa-wrench"></i></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header">
                                        <span class="text-bold text-green" id="total_achat">0</span>
                                    </h5>
                                    <span class="description-text">TOTAL ACHAT</span>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header">
                                        <span class="text-bold text-warning" id="total_remise">0</span>
                                    </h5>
                                    <span class="description-text">TOTAL REMISE</span>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block border-right">
                                    <h5 class="description-header">
                                        <span class="text-bold" id="total_acompte">0</span>
                                    </h5>
                                    <span class="description-text">TOTAL ACOMPTE</span>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block">
                                    <h5 class="description-header">
                                        <span class="text-bold text-red" id="total_reste">0</span>
                                    </h5>
                                    <span class="description-text">TOTAL RESTE</span>
                                </div>
                            </div>
                        </div>
                    </div>
              
        </div>
        <div class="tab-pane" id="reglement_info">
         
                    <div class="box-header">
                        <div class="col-md-3">
                            <h3 class="box-title pull-left">Liste des r&egrave;glements</h3>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" id="dateDebutRgl" placeholder="Date du début">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" id="dateFinRgl" placeholder="Date de fin">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-success pull-right" onclick="imprimeRglPdf()">Imprimer</a><br/>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="tableReglement" class="table table-warning table-striped"
                               data-pagination="true"
                               data-search="false" 
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false"
                               data-show-columns="false">
                            <thead>
                                <tr>
                                    <th data-formatter="recuFormatter">Re&ccedil;u</th>
                                    <th data-field="date_reglements">Date</th>
                                    <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de payement </th>
                                    <th data-field="montant_reglement" data-formatter="montantFormatter">Montant</th>
                                    <th data-formatter="objetFormatter">Objet</th>
                                    <th data-field="numero_cheque_virement">N° virement ou ch&egrave;que</th>
                                    <th data-formatter="imageFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
             
        </div>
        <div class="tab-pane" id="article_achat_info">
            <div class="box-header">
                <div class="col-md-8">
                    <h3 class="box-title pull-left">Liste des articles les plus achet&eacute;s</h3>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-success pull-right" onclick="imprimeAchgtPdf()">Imprimer</a><br/>
                </div>
            </div>
            <div class="box-body">
                <table id="tableArticleAchetes" class="table table-warning table-striped"
                       data-pagination="true"
                       data-search="false" 
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false"
                       data-show-columns="false">
                    <thead>
                        <tr>
                            <th data-field="description_article">Article</th>
                            <th data-field="qteTotale" data-formatter="montantFormatter">Quantit&eacute;</th>
                            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
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
                                    <th data-field="article.description_article">Article</th>
                                    <th data-field="unite.libelle_unite">Colis</th>
                                    <th data-formatter="montantHTFormatter">Prix HT</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix TTC</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant TTC </th>
                                    <th data-field="remise_sur_ligne" data-formatter="montantFormatter">Remise </th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<!-- Modal panier divers-->
<div class="modal fade bs-modal-panier-article-divers" id="panierArticleDivers" ng-controller="panierArticleDiversCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                     Panier de la facture N° <b>@{{vente.numero_facture}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="tablePanierArticleDivers" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="divers.libelle_divers">Libelle&eacute;</th>
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
<form>
    <input type="text" class="hidden" id="client" value="{{$infoClient->id}}"/>
</form>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [], $tableReglement = jQuery("#tableReglement"),$tablePanierArticle = jQuery("#tablePanierArticle"),$tableArticleAchetes = jQuery("#tableArticleAchetes"), $tablePanierArticleDivers = jQuery("#tablePanierArticleDivers");
    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
        $scope.vente = vente;
        };
    });
    appSmarty.controller('panierArticleDiversCtrl', function ($scope) {
        $scope.populateFormPanierDivers = function (vente) {
        $scope.vente = vente;
        };
    });
    $(function () {
        var client = $("#client").val(); 
        $table.bootstrapTable('refreshOptions', {url: '../liste-achats-client/' + client});
        $tableReglement.bootstrapTable('refreshOptions', {url: '../liste-reglements-client/' + client});
        $tableArticleAchetes.bootstrapTable('refreshOptions', {url: '../liste-articles-plus-achetes/' + client});
        
        
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
            $("#total_achat").html($.number(data.totalAchat));
            $("#total_acompte").html($.number(data.totalAcompte));
            $("#total_remise").html($.number(data.totalRemise));
            $("#total_reste").html($.number(data.totalAchat-data.totalAcompte));
        });
        
        $('#dateDebut,#dateFin,#dateDebutRgl,#dateFinRgl').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        
        $("#dateDebut,#dateFin").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByFacture").val("");
         
            if(dateDebut == "" && dateFin == ""){
                $table.bootstrapTable('refreshOptions', {url: '../liste-achats-client/' + client});
            }
            if(dateDebut != "" && dateFin != ""){
               $table.bootstrapTable('refreshOptions', {url: '../liste-achats-client-by-periode/' + dateDebut + "/" + dateFin + "/" + client});
            }
        });
        
        $("#searchByFacture").keyup(function (e) { 
            $("#dateDebut").val("");
            $("#dateFin").val("");
            var facture = $("#searchByFacture").val();
            if(facture == ""){
                $table.bootstrapTable('refreshOptions', {url: '../liste-achats-client/' + client});
            }
            if(facture != ""){
               $table.bootstrapTable('refreshOptions', {url: '../liste-achats-client-by-facture/' + facture + "/" + client});
            }
        });
        
        $("#dateDebutRgl,#dateFinRgl").change(function (e) { 
            var dateDebutRgl = $("#dateDebutRgl").val();
            var dateFinRgl = $("#dateFinRgl").val();
         
            if(dateDebutRgl == "" && dateFinRgl == ""){
                $tableReglement.bootstrapTable('refreshOptions', {url: '../liste-reglements-client/' + client});
            }
            if(dateDebutRgl != "" && dateFinRgl != ""){
               $tableReglement.bootstrapTable('refreshOptions', {url: '../liste-reglements-client-by-periode/' + dateDebutRgl + "/" + dateFinRgl + "/" + client});
            }
        });
        
    });
    function listeArticleRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: basePath + "/boutique/liste-articles-vente/" + idVente});
        $(".bs-modal-panier").modal("show");
    }
    
    function listeArticleDiversRow(idVente){
        var $scope = angular.element($("#panierArticleDivers")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanierDivers(vente);
        });
        $tablePanierArticleDivers.bootstrapTable('refreshOptions', {url: basePath + "/boutique/liste-articles-vente-divers/" + idVente});
        $(".bs-modal-panier-article-divers").modal("show");
    }
    
    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var client = $("#client").val();
        $("#searchByFacture").val("");
        if(dateDebut=='' && dateFin==''){
            window.open("../liste-achats-client-pdf/" + client,'_blank');
        }
        if(dateDebut!='' && dateFin!=''){
            window.open("../liste-achats-client-by-periode-pdf/" + dateDebut + '/' + dateFin + '/' + client,'_blank');  
        }
    }
    
    function imprimeRglPdf(){
        var dateDebutRgl = $("#dateDebutRgl").val();
        var dateFinRgl = $("#dateFinRgl").val();
        var client = $("#client").val();
        
        if(dateDebutRgl=='' && dateFinRgl==''){
            window.open("../liste-reglements-client-pdf/" + client,'_blank');
        }
        if(dateDebutRgl!='' && dateFinRgl!=''){
            window.open("../liste-reglements-client-by-periode-pdf/" + dateDebutRgl + '/' + dateFinRgl + '/' + client,'_blank');  
        }
    }
    
    function imprimeAchgtPdf(){
        var client = $("#client").val();
        window.open("../liste-articles-plus-achetes-pdf/" + client,'_blank');
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }

    function recuFormatter(id, row){
        return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Imprimer le reçu" onClick="javascript:recuPrintRow(' + row.id + ');"><i class="fa fa-print"></i></button>'
    }
    function objetFormatter(id, row){
        return '<span class="text-bold"> Facture N° ' + row.numero_facture + '</span>';
    }
    function recuPrintRow(idReglement){
        window.open(basePath + "/boutique/recu-reglement-pdf/" + idReglement ,'_blank')
    }
    function facturePrintRow(idVente){
        window.open(basePath + "/boutique/facture-vente-pdf/" + idVente ,'_blank');
    }
    function factureVenteDiversPrintRow(idVente){
        window.open(basePath + "/boutique/facture-vente-divers-pdf/" + idVente ,'_blank');
    }
    function typeFactureFormatter(id, row){
        return row.proformat==0 ? row.numero_facture : "Proforma";
    }
    function resteFormatter(id, row){
        var montant = row.sommeTotale - row.acompte_facture;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function montantHTFormatter(id, row){
        var prix_ttc = row.prix;
        if(row.article.param_tva_id!=null){
            var tva = row.montant_tva;
            var prix_ht_article = (prix_ttc/(tva + 1));
            var prix = Math.round(prix_ht_article);
            return '<span class="text-bold">' + $.number(prix)+ '</span>';
        }else{
           return '<span class="text-bold">' + $.number(prix_ttc)+ '</span>';
        }
    }
    function depotFormatter(id, row){
        if(row.depot_id!=null){
            return row.depot.libelle_depot;
        }else{
            return "<span class='text-bold'>Achat divers </span>";
        }
    }
    function factureFormatter(id, row){
        if(row.depot_id!=null){
            return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Facture" onClick="javascript:facturePrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
        }else{
            return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Facture" onClick="javascript:factureVenteDiversPrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
        }
    }
    function optionFormatter(id, row) { 
        if(row.depot_id!=null){
            return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
        }else{
            return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleDiversRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
        }
    }
    function imageFormatter(id, row) { 
          return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


