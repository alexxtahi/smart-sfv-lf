@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date d'inventaire">
    </div>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByDepot">
        <option value="0">-- Tous les d&eacute;p&ocirc;ts --</option>
        @foreach($depots as $depot)
        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
        @endforeach
    </select>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-inventaires'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="ficheInventaireFormatter" data-width="60px">Fiche</th>
            <th data-field="date_inventaires">Date</th>
            <th data-field="libelle_inventaire">P&eacute;riode</th>
            <th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>
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
                        <i class="fa fa-calendar-plus-o fa-2x"></i>
                        Gestion des inventaires
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idInventaireModifier" ng-hide="true" ng-model="inventaire.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>P&eacute;riode de l'inventaire *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="inventaire.libelle_inventaire" id="libelle_inventaire" name="libelle_inventaire" placeholder="Inventaire du 01-05-2020 au 25-06-2020" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>D&eacute;p&ocirc;t *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-bank"></i>
                                    </div>
                                    <select name="depot_id" id="depot_id" ng-model="inventaire.depot_id" ng-init="inventaire.depot_id=''" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le d&eacute;p&ocirc;t --</option>
                                        @foreach($depots as $depot)
                                        <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Code barre</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" id="code_barre" class="form-control" placeholder="Recherche">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-2">
                            <div class="form-group">
                                <label>Date de l'inventaire *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"  class="form-control" ng-model="inventaire.date_inventaires" id="date_inventaire" name="date_inventaire" value="<?= date('d-m-Y'); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cat&eacute;gorie article</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select name="categorie_id" ng-model="inventaire.categorie_id" ng-init="inventaire.categorie_id=''" id="categorie_id" class="form-control">
                                        <option value="">-- Aucune --</option>
                                         @foreach($categories as $categorie)
                                        <option value="{{$categorie->id}}"> {{$categorie->libelle_categorie}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sous cat&eacute;gorie article</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select name="sous_categorie_id" id="sous_categorie_id" class="form-control">
                                        <option value="">-- Sous cat&eacute;gorie --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Article</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select id="article_id" name="article_id" class="form-control">
                                        <option value="">-- Article --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="id_row_table">
                        <br/><div class="col-md-12">
                            <table class="table table-primary table-striped box box-primary">
                                <thead>
                                    <th>Code barre</th>
                                    <th>Article</th>
                                    <th>Colis</th>
                                    <th>Quantit&eacute; en stock</th>
                                    <th>Quantit&eacute; d&eacute;nombr&eacute;e</th>
                                </thead>
                                <tbody id="liste_articles">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="sendButton" class="btn btn-primary btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste details inventaire -->
<div class="modal fade bs-modal-liste-detail-inventaire" id="listeDetailInventaire" ng-controller="listeDetailInventaireCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Inventaire <b>@{{inventaire.libelle_inventaire}}</b> du d&eacute;p&ocirc;t <b>@{{inventaire.depot.libelle_depot}}</b> effectu&eacute; le <b>@{{inventaire.date_inventaires}}</b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListeDetailInventaire" class="table table-success table-striped box box-success"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id"
                       data-show-toggle="false">
                    <thead>
                        <tr>
                            <th data-field="article.code_barre">Code barre  </th>
                            <th data-field="article.description_article">Article  </th>
                            <th data-field="unite.libelle_unite">Colis  </th>
                            <th data-field="quantite_en_stocke">Quantit&eacute; en stock </th>
                            <th data-field="quantite_denombree">Qt&eacute; d&eacute;nombr&eacute;e</th>
                            <th data-formatter="ecartFormatter">Ecart</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [], $tableListeDetailInventaire = jQuery("#tableListeDetailInventaire");
    
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (inventaire) {
            $scope.inventaire = inventaire;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.inventaire = {};
        };
    });
    appSmarty.controller('listeDetailInventaireCtrl', function ($scope) {
        $scope.populateDetailInventaireForm = function (inventaire) {
            $scope.inventaire = inventaire;
        };
    });
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        $("#depot_id, #article_id").select2({width: '100%', allowClear: true});
        $('#searchByDate,#date_inventaire').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $('#btnModalAjout').click(function(){
            $('#id_row_table').show();
            $('#liste_articles').html('');
            $('#depot_id').attr('disabled', false)
             $("#depot_id").select2("val", "");
             $("#article_id").select2("val","");
        });
                    
        $("#searchByDate").change(function (e) {
            $("#searchByDepot").val(0);
            var date = $("#searchByDate").val();
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-inventaires'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-inventaires-by-date/' + date});
            }
        });
        $("#searchByDepot").change(function (e) {
            $("#searchByDate").val("");
            var depot = $("#searchByDepot").val();
            if(depot == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-inventaires'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-inventaires-by-depot/' + depot});
            }
        });
        
        $('#depot_id').change(function(){
            var depot = $("#depot_id").val();
            if(depot!=""){
                $('#categorie_id').val('');
                $('#sous_categorie_id').html('<option value="">-- Sous catégorie --</option>');
                $("#code_barre").val("");
                $.getJSON("../boutique/liste-article-by-unite-in-depot/" + depot , function (reponse) {
                        $('#article_id').html('<option value="">-- Article --</option>');
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#article_id').append('<option value=' + depot_article.id_article + '>' + depot_article.description_article + '</option>')
                        });
                });
                $.getJSON("../parametre/inventaire-liste-articles-by-depot/" + depot , function (reponse) {
                    $('#liste_articles').html('');
                    if(reponse.total==0){
                        $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe dans ce dépôt</td></tr>');
                    }else{
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#liste_articles').append("<tr><td>"+depot_article.article.code_barre+"</td><td><input type='hidden' name='articles[]' value='" + depot_article.article.id + "'>" + depot_article.article.description_article + "</td><td><input type='hidden' name='unites[]' value='" + depot_article.unite.id + "'>" + depot_article.unite.libelle_unite + "</td><td align='center' width='150'><input type='hidden' name='quantite_en_stocks[]' value='" + depot_article.quantite_disponible + "'>" + depot_article.quantite_disponible + "</td><td><input type='number' min='0' name='quantite_denombres[]'></td></tr>"); 
                        });
                    }
                });
            }else{
                $('#categorie_id').val("");
                $('#sous_categorie_id').val("");
                $('#article_id').val("");
                $('#liste_articles').html('');
            }
        });
        $('#categorie_id').change(function(){
            if( $("#depot_id").val()==""){
                alert("Selectionner un dépôt svp");
                return;
            }
            var categorie = $("#categorie_id").val();
            var depot = $("#depot_id").val();
            if(categorie!=""){
                $.getJSON("../parametre/sous-categorie-articles-by-depot/" + categorie + "/" + depot , function (reponse) {
                        $('#sous_categorie_id').html('<option value="">-- Sous catégorie --</option>');
                        $.each(reponse.rows, function (index, sous_categorie) { 
                            $('#sous_categorie_id').append('<option value=' + sous_categorie.id_sous_categorie + '>' + sous_categorie.libelle_sous_categorie + '</option>')
                        });
                });
                $.getJSON("../parametre/liste-article-by-depot-categorie/" + depot + "/" + categorie, function (reponse) {
                        $('#article_id').html('<option value="">-- Article --</option>');
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#article_id').append('<option value=' + depot_article.id + '>' + depot_article.description_article + '</option>')
                        });
                });
                $.getJSON("../parametre/inventaire-liste-articles-categorie-in-depot/" + categorie + "/" + depot , function (reponse) {
                    $('#liste_articles').html('');
                    if(reponse.total==0){
                        $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe dans ce dépôt</td></tr>');
                    }else{
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#liste_articles').append("<tr><td>"+depot_article.article.code_barre+"</td><td><input type='hidden' name='articles[]' value='" + depot_article.article.id + "'>" + depot_article.article.description_article + "</td><td><input type='hidden' name='unites[]' value='" + depot_article.unite.id + "'>" + depot_article.unite.libelle_unite + "</td><td align='center' width='150'><input type='hidden' name='quantite_en_stocks[]' value='" + depot_article.quantite_disponible + "'>" + depot_article.quantite_disponible + "</td><td><input type='number' min='0' name='quantite_denombres[]'></td></tr>"); 
                        });
                    }
                });
            }else{
                $('#liste_articles').html('');
            }
        });
        $('#sous_categorie_id').change(function(){
            if( $("#depot_id").val()==""){
                alert("Selectionner un dépôt svp");
                return;
            }
            var sous_categorie = $("#sous_categorie_id").val();
            var depot = $("#depot_id").val();
            if(sous_categorie!=""){
                $("#categorie_id").val("");
                $("#code_barre").val("");
                $.getJSON("../parametre/liste-articles-grouper-by-sous-categorie-in-depot/" + sous_categorie + "/" + depot , function (reponse) {
                        $('#article_id').html('<option value="">-- Article --</option>');
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#article_id').append('<option value=' + depot_article.article.id + '>' + depot_article.article.description_article + ' ' + depot_article.unite.libelle_unite + '</option>')
                        });
                });
                $.getJSON("../parametre/inventaire-liste-articles-sous-categorie-in-depot/" + sous_categorie + "/" + depot , function (reponse) {
                    $('#liste_articles').html('');
                    if(reponse.total==0){
                        $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe dans ce dépôt</td></tr>');
                    }else{
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#liste_articles').append("<tr><td>"+depot_article.article.code_barre+"</td><td><input type='hidden' name='articles[]' value='" + depot_article.article.id + "'>" + depot_article.article.description_article + "</td><td><input type='hidden' name='unites[]' value='" + depot_article.unite.id + "'>" + depot_article.unite.libelle_unite + "</td><td align='center' width='150'><input type='hidden' name='quantite_en_stocks[]' value='" + depot_article.quantite_disponible + "'>" + depot_article.quantite_disponible + "</td><td><input type='number' min='0' name='quantite_denombres[]'></td></tr>"); 
                        });
                    }
                });
            }else{
                $('#liste_articles').html('');
            }
        });
        $('#code_barre').keyup(function(e){
            if($("#depot_id").val()==""){
                alert("Selectionner un dépôt svp");
                $("#code_barre").val("");
                return;
            }
            var code_barre = $("#code_barre").val();
            var depot = $("#depot_id").val();
            if(code_barre!=""){
                $("#categorie_id").val("");
                $("#sous_categorie_id").val("");
                if(e.which == '10' || e.which == '13') {
                $.getJSON("../parametre/liste-articles-grouper-code_barre-in-depot/" + code_barre + "/" + depot , function (reponse) {
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#article_id').append('<option selected value=' + depot_article.article.id + '>' + depot_article.article.description_article + '</option>')
                        });
                });
                $.getJSON("../parametre/inventaire-liste-articles-code_barre-in-depot/" + code_barre + "/" + depot , function (reponse) {
                    $('#liste_articles').html('');
                    if(reponse.total==0){
                        $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe dans ce dépôt</td></tr>');
                    }else{
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#liste_articles').append("<tr><td>"+depot_article.article.code_barre+"</td><td><input type='hidden' name='articles[]' value='" + depot_article.article.id + "'>" + depot_article.article.description_article + "</td><td><input type='hidden' name='unites[]' value='" + depot_article.unite.id + "'>" + depot_article.unite.libelle_unite + "</td><td align='center' width='150'><input type='hidden' name='quantite_en_stocks[]' value='" + depot_article.quantite_disponible + "'>" + depot_article.quantite_disponible + "</td><td><input type='number' min='0' name='quantite_denombres[]'></td></tr>"); 
                        });
                    }
                });
                e.preventDefault();
                e.stopPropagation();
                } 
            }else{
                $('#liste_articles').html('');
                $("#article_id").val("");
            }
        });
        $('#article_id').change(function(){
//            if($("#depot_id").val()==""){
//                alert("Selectionner un dépôt svp");
//                return;
//            }
            var article = $("#article_id").val();
            var depot = $("#depot_id").val();
            if(article!=""){
                $("#code_barre").val("");
                $("#categorie_id").val("");
                $("#sous_categorie_id").val("");
                $.getJSON("../parametre/find-article/" + article , function (reponse) {
                   $.each(reponse.rows, function (index, articles_trouver) { 
                       $("#code_barre").val(articles_trouver.code_barre);
                   });
                })
                $.getJSON("../parametre/liste-articles-grouper-id-in-depot/" + article + "/" + depot , function (reponse) {
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#article_id').append('<option selected value=' + depot_article.article.id + '>' + depot_article.article.description_article + '</option>')
                        });
                });
                $.getJSON("../parametre/inventaire-liste-articles-id-in-depot/" + article + "/" + depot , function (reponse) {
                    $('#liste_articles').html('');
                    if(reponse.total==0){
                        $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe dans ce dépôt</td></tr>');
                    }else{
                        $.each(reponse.rows, function (index, depot_article) { 
                            $('#liste_articles').append("<tr><td>"+depot_article.article.code_barre+"</td><td><input type='hidden' name='articles[]' value='" + depot_article.article.id + "'>" + depot_article.article.description_article + "</td><td><input type='hidden' name='unites[]' value='" + depot_article.unite.id + "'>" + depot_article.unite.libelle_unite + "</td><td align='center' width='150'><input type='hidden' name='quantite_en_stocks[]' value='" + depot_article.quantite_disponible + "'>" + depot_article.quantite_disponible + "</td><td><input type='number' min='0' name='quantite_denombres[]'></td></tr>"); 
                        });
                    }
                });
            }else{
                $('#liste_articles').html('');
            }
        });
         // Submit the add form  
        $("#sendButton").click(function(){  
            $("#formAjout").submit(); 
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
                var url = "{{route('boutique.inventaires.store')}}";
             }else{
                var id = $("#idInventaireModifier").val();
                var methode = 'PUT';
                var url = 'inventaires/' + id;
             }
            editerInventaireAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });
    });
    function updateRow(idInventaire){
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var inventaire =_.findWhere(rows, {id: idInventaire});
        $scope.$apply(function () {
            $scope.populateForm(inventaire);
        });
        $('#code_barre').val("");
        $("#depot_id").select2("val",inventaire.depot_id);
        $.getJSON("../boutique/liste-article-by-unite-in-depot/" + inventaire.depot_id , function (reponse) {
            $('#article_id').html('<option value="">-- Article --</option>');
            $.each(reponse.rows, function (index, depot_article) { 
                $('#article_id').append('<option value=' + depot_article.id_article + '>' + depot_article.description_article + '</option>')
            });
            if(inventaire.article_id!=null){
           
            $("#article_id").select2("val",inventaire.article_id);
            $.getJSON("../parametre/find-article/" + inventaire.article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) { 
                       $("#code_barre").val(articles_trouver.code_barre);
                });
            })
        }else{
            $('#article_id').val("");
        }
        });
        inventaire.categorie_id !=null ? $('#categorie_id').val(inventaire.categorie_id) : $('#categorie_id').val("");
        if(inventaire.sous_categorie_id !=null){
            $.getJSON("../parametre/sous-categorie-id-articles-by-depot/" + inventaire.sous_categorie_id + "/" + inventaire.depot_id , function (reponse) {
                $('#sous_categorie_id').html('<option value="">-- Sous catégorie --</option>');
                $.each(reponse.rows, function (index, sous_categorie) { 
                    $('#sous_categorie_id').append('<option selected value=' + sous_categorie.id_sous_categorie + '>' + sous_categorie.libelle_sous_categorie + '</option>')
                });
            });
        }else{
            
        }
        
        $.getJSON(basePath + "/boutique/liste-details-inventaire/" + inventaire.id , function (reponse) {
            $('#liste_articles').html('');
            if(reponse.total==0){
                   $('#liste_articles').html('<tr><td align="center">Aucun article disponilbe dans ce dépôt</td></tr>');
            }else{
                $.each(reponse.rows, function (index, details_inventaire) { 
                    $('#liste_articles').append("<tr><td>"+details_inventaire.article.code_barre+"</td><td><input type='hidden' name='articles[]' value='" + details_inventaire.article.id + "'>" + details_inventaire.article.description_article + "</td><td><input type='hidden' name='unites[]' value='" + details_inventaire.unite.id + "'>" + details_inventaire.unite.libelle_unite + "</td><td align='center' width='150'><input type='hidden' name='quantite_en_stocks[]' value='" + details_inventaire.quantite_en_stocke + "'>" + details_inventaire.quantite_en_stocke + "</td><td><input type='number' min='0' name='quantite_denombres[]' value='" + details_inventaire.quantite_denombree + "'></td></tr>"); 
                });
            }
        });
        $(".bs-modal-ajout").modal("show");
    }
    
    function detailInventaireRow(idInventaire){
        var $scope = angular.element($("#listeDetailInventaire")).scope();
        var inventaire =_.findWhere(rows, {id: idInventaire});
        $scope.$apply(function () {
            $scope.populateDetailInventaireForm(inventaire);
        });
        $tableListeDetailInventaire.bootstrapTable('refreshOptions', {url: "../boutique/liste-details-inventaire/" + idInventaire});
        $(".bs-modal-liste-detail-inventaire").modal("show");
    }
    
    function printRow(idInventaire){
        window.open("../boutique/fiche-inventaire-pdf/" + idInventaire,'_blank');
    }
    
    function ecartFormatter(id,row){
         var ecart = row.quantite_en_stocke-row.quantite_denombree;
        return '<span class="text-bold">' + $.number(ecart)+ '</span>';
    }
    function ficheInventaireFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + row.id + ');"><i class="fa fa-print"></i></button>';
    }
    function optionFormatter(id, row) { 
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Détails inventaire" onClick="javascript:detailInventaireRow(' + id + ');"><i class="fa fa-list"></i></button>';
    }
    
    function editerInventaireAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $('#liste_articles').html('');
                    $('#depot_id').attr('disabled', false);
                     $("#depot_id").select2("val","");
                     $("#article_id").select2("val","");
                    $('#id_row_table').show();
                    $("#code_barre").val("");
                    $("#article_id").val("");
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
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection