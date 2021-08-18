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
    <div class="form-group">
       <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date">
    </div>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByFournisseur">
        <option value="0">-- Tous les fournisseurs --</option>
        @foreach($fournisseurs as $fournisseur)
        <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByClient">
        <option value="tous">-- Tous les clients --</option>
        @foreach($clients as $client)
        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-3">
    <select class="form-control" id="searchByModes">
        <option value="0">-- Tous les moyens payement --</option>
        @foreach($moyenReglements as $moyenReglement)
        <option value="{{$moyenReglement->id}}"> {{$moyenReglement->libelle_moyen_reglement}}</option>
        @endforeach
    </select>
</div>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-reglements'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="recuFormatter">Re&ccedil;u</th>
            <th data-field="date_reglements">Date</th>
            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de payement </th>
            <th data-field="montant_reglement" data-formatter="montantFormatter">Montant</th>
            <th data-formatter="concerneFormatter">Concern&eacute;</th>
            <th data-formatter="objetFormatter">Objet</th>
            <th data-field="numero_cheque_virement">N° virement ou ch&egrave;que</th>
            <th data-formatter="imageFormatter" data-visible="true" data-align="center">Ch&egrave;que</th>
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
                        <i class="fa fa-paypal fa-2x"></i>
                        Gestion des r&egrave;glements
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" name="idReglement" ng-hide="true" ng-model="reglement.id"/>
                    @csrf
                    <div class="row" id="row_premier">
                        <div class="col-md-2"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <input type="radio" onclick="concerner(this.value)" name="concerne" checked="checked" value="client">&nbsp;Client
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <input type="radio" onclick="concerner(this.value)" name="concerne" value="fournisseur">&nbsp;Fournisseur
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="row_client">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_id">Client *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <select id="client_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le client --</option>
                                        @foreach($clients as $client)
                                        <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text"  class="form-control" id="contact_client" placeholder="Contact du client" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Adresse client </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text"  class="form-control" id="adresse_client" placeholder="Adresse du client" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="row_fournisseur">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fournisseur_id">Fournisseur *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-institution"></i>
                                    </div>
                                    <select id="fournisseur_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner le fournisseur --</option>
                                        @foreach($fournisseurs as $fournisseur)
                                        <option value="{{$fournisseur->id}}">{{$fournisseur->full_name_fournisseur}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact fournisseur</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text"  class="form-control" id="contact_fournisseur" placeholder="Contact du fournisseur" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Adresse g&eacute;ographique </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text"  class="form-control" id="adresse_fournisseur" placeholder="Adresse du fournisseur" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row"id="row_second"> 
                        <div class="col-md-4" id="facture_vente">
                            <div class="form-group">
                                <label for="vente_id">Facture(s) *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select id="vente_id" name="vente_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner la facture --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="facture_fournisseur">
                            <div class="form-group">
                                <label for="commande_id">Commande(s) *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <select id="commande_id" name="commande_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner la commande --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Montant TTC </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" id="montant_ttc" placeholder="Montant TTC" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reste &agrave; payer </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" class="form-control" id="montant_restant" placeholder="Montant restant" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="moyen_reglement_id">Moyen de payement *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-cog"></i>
                                    </div>
                                    <select name="moyen_reglement_id" id="moyen_reglement_id" ng-model="reglement.moyen_reglement_id" class="form-control" required>
                                        <option value="" ng-show="false">-- Selectionner --</option>
                                        @foreach($moyenReglements as $moyenReglement)
                                        <option value="{{$moyenReglement->id}}">{{$moyenReglement->libelle_moyen_reglement}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Montant *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <input type="text" pattern="[0-9]*" class="form-control" ng-model="reglement.montant_reglement" id="montant_reglement" name="montant_reglement" placeholder="Montant" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date de payement *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="reglement.date_reglements" id="date_reglement" name="date_reglement" value="{{date('d-m-Y')}}" required>
                                </div>
                            </div>
                        </div> 
                    </div> 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ajouter le fichier si le payement a &eacute;t&eacute; fait par ch&egrave;que </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-file"></i>
                                    </div>
                                    <input type="file" class="form-control" name="scan_cheque">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Num&eacute;ro du ch&egrave;que ou du virement </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="reglement.numero_cheque_virement" id="numero_cheque_virement" name="numero_cheque_virement"  placeholder="Numéro du chèque ou du virement">
                                </div>
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
                    <input type="text" class="hidden" id="idReglementSupprimer"  ng-model="reglement.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le r&egrave;glement du <br/><b>@{{reglement.commande_id ? ' Fournisseur ' + reglement.full_name_fournisseur + ' au ' + reglement.date_reglements : ' Client ' + reglement.full_name_client + ' au ' +  reglement.date_reglements}}</b></div>
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
    var $table = jQuery("#table"), rows = [];
    
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (reglement) {
            $scope.reglement = reglement;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.reglement = {};
        };
    });
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (reglement) {
            $scope.reglement = reglement;
        };
        $scope.initForm = function () {
            $scope.reglement = {};
        };
    });
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        $('#date_reglement, #searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            if(date == ''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-date/' + date});
            }
        }); 
        $("#searchByFournisseur").change(function (e) {
            var fournisseur = $("#searchByFournisseur").val();
            if(fournisseur == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-fournisseur/' + fournisseur});
            }
        });
        $("#searchByClient").change(function (e) {
            var client = $("#searchByClient").val();
            if(client == "tous"){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-client/' + client});
            }
        });
        $("#searchByModes").change(function (e) {
            var moyen = $("#searchByModes").val();
            if(moyen == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-moyen-reglement/' + moyen});
            }
        });
        $('#row_client').show();
        $('#row_fournisseur').hide();
        $('#row_premier').show();
        $('#facture_vente').show();
        $('#facture_fournisseur').hide();
        $('#vente_id').attr('disabled', false)
        $('#date_reglement').attr('disabled', false)
        $('#montant_reglement').attr('disabled', false)
        $('#commande_id').attr('disabled', true)
        $('#client_id').attr('required', true)
        $('#fournisseur_id').attr('required', false)
        $('#row_second').show();
        $('#btnModalAjout').click(function(){
            $('#date_reglement').attr('disabled', false)
            $('#montant_reglement').attr('disabled', false)
            $("#clientAnonyme").val("");
            $('#row_premier').show();
            $('#row_second').show();
            $('#row_client').show();
            $('#row_fournisseur').hide();
            $('#facture_vente').show();
            $('#facture_fournisseur').hide();
            $('#vente_id').attr('disabled', false)
            $('#commande_id').attr('disabled', true)
            $('#client_id').attr('required', true)
            $('#fournisseur_id').attr('required', false)
            $('#contact_client').val("");    
            $('#contact_fournisseur').val("");    
            $('#adresse_client').val("");    
            $('#adresse_fournisseur').val("");    
            $('#montant_ttc').val("");    
            $('#montant_restant').val(""); 
            $('#vente_id').html("<option>-- Selectionner la facture --</option>");
            $('#commande_id').html("<option>-- Selectionner la commande --</option>");
        });
        
        $('#client_id').change(function(){
            var client_id = $('#client_id').val();
            $.getJSON("../boutique/get-all-facture-client/" + client_id, function (reponse) {
                $('#vente_id').html("<option>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function (index, client) { 
                    $('#contact_client').val(client.client.contact_client)
                    $('#adresse_client').val(client.client.adresse_client)
                    if((client.sommeTotale-client.acompte_facture>0) && client.proformat==0){
                       $('#vente_id').append('<option value=' + client.id + '> Facture N° ' + client.numero_facture + '</option>')
                    }
                });
            })
        });
        $('#fournisseur_id').change(function(){
            var fournisseur_id = $('#fournisseur_id').val();
            $.getJSON("../boutique/liste-reception-commande-by-fournisseur/" + fournisseur_id, function (reponse) {
                $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function (index, fournisseur) { 
                    $('#contact_fournisseur').val(fournisseur.fournisseur.contact_fournisseur)
                    $('#adresse_fournisseur').val(fournisseur.fournisseur.adresse_fournisseur)
                    if((fournisseur.montantCommande-fournisseur.accompteFournisseur)>0){
                       $('#commande_id').append('<option data-numeorbon= "' + fournisseur.numero_bon + '" value=' + fournisseur.commmande_id + '>' + fournisseur.numero_bon + '</option>')
                    }
                });
            })
        });
        $('#vente_id').change(function(){
            var vente_id = $('#vente_id').val();
            $.getJSON("../boutique/find-vente-by-id/" + vente_id, function (reponse) {
                $.each(reponse.rows, function (index, vente) { 
                    $('#montant_ttc').val(vente.sommeTotale);
                    $('#montant_restant').val(vente.sommeTotale-vente.acompte_facture)
                });
            })
        });
        $('#commande_id').change(function(){
            var numero_bon = $("#commande_id").children(":selected").data("numeorbon");
            $.getJSON("../boutique/liste-reception-commande-by-numero_bon/" + numero_bon, function (reponse) {
                $.each(reponse.rows, function (index, commande) { 
                    var montant_restant = commande.montantCommande - commande.accompteFournisseur;
                    $('#montant_ttc').val(commande.montantCommande);
                    $('#montant_restant').val(montant_restant)
                });
            })
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
                var url = "{{route('boutique.reglements.store')}}";
            }else{
                var methode = 'POST';
                var url = "{{route('boutique.update-reglement')}}";
            }
            var formData = new FormData($(this)[0]);
            editerReglement(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idReglementSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('reglements/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    function updateRow(idReglement) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var reglement =_.findWhere(rows, {id: idReglement});
        $scope.$apply(function () {
            $scope.populateForm(reglement);
        });
        if(reglement.vente_id!=null){
            $.getJSON("../parametre/find-client-by-vente/" + reglement.vente_id, function (reponse) {
                $('#vente_id').html("<option>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function (index, client) { 
                    $("#client_id").val(client.client.id);
                    $('#contact_client').val(client.client.contact_client)
                    $('#adresse_client').val(client.client.adresse_client)
                    $('#vente_id').append('<option selected value=' + client.id + '> Facture N° ' + client.numero_facture + '</option>')
                });
            })
            $.getJSON("../boutique/find-vente-by-id/" + reglement.vente_id, function (reponse) {
                $.each(reponse.rows, function (index, vente) { 
                    $('#montant_ttc').val(vente.sommeTotale);
                    $('#montant_restant').val(vente.sommeTotale-vente.acompte_facture)
                });
            })
            $('input:radio[name=concerne]').val(['client']); 
            $('#fournisseur_id').val("");
            $('#adresse_fournisseur').val("");
            $('#contact_fournisseur').val("");
            $('#row_fournisseur').hide();
            $('#facture_fournisseur').hide();
            $('#facture_vente').show();
            $('#row_client').show();
            $('#row_second').show();
            $('#row_premier').show();
            $('#date_reglement').attr('disabled', false)
            $('#montant_reglement').attr('disabled', false)
            $('#vente_id').attr('disabled', false)
            $('#commande_id').attr('disabled', true)
            $('#client_id').attr('required', true)
            $('#fournisseur_id').attr('required', false)
            $('#commande_id').html("<option>-- Selectionner la facture --</option>");
        }
        if(reglement.commande_id!=null){
            $.getJSON("../parametre/find-fournisseur-by-commande/" + reglement.commande_id, function (reponse) {
                $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                $.each(reponse.rows, function (index, fournisseur) { 
                    $("#fournisseur_id").val(fournisseur.fournisseur.id);
                    $('#contact_fournisseur').val(fournisseur.fournisseur.contact_fournisseur)
                    $('#adresse_fournisseur').val(fournisseur.fournisseur.adresse_fournisseur)
                    $('#commande_id').append('<option selected data-numeorbon= "' + fournisseur.numero_bon + '" value=' + fournisseur.id + '>' + fournisseur.numero_bon + '</option>')
                });
            })
            $("#commande_id").val(reglement.commande_id);
            $.getJSON("../boutique/find-reception-commande-by-id/" + reglement.commande_id, function (reponse) {
                $.each(reponse.rows, function (index, commande) { 
                    var montant_restant = commande.montantCommande - commande.accompteFournisseur;
                    $('#montant_ttc').val(commande.montantCommande);
                    $('#montant_restant').val(montant_restant)
                });
            })
            $('input:radio[name=concerne]').val(['fournisseur']);
            $('#client_id').val("");
            $('#adresse_client').val("");
            $('#contact_client').val("");
            $('#row_client').hide();
            $('#facture_vente').hide();
            $('#facture_fournisseur').show();
            $('#row_fournisseur').show();
            $('#vente_id').attr('disabled', true)
            $('#row_second').show();
            $('#row_premier').show();
            $('#date_reglement').attr('disabled', false)
            $('#montant_reglement').attr('disabled', false)
            $('#commande_id').attr('disabled', false)
            $('#client_id').attr('required', false)
            $('#fournisseur_id').attr('required', true)
            $('#vente_id').html("<option>-- Selectionner la facture --</option>");
        }
        reglement.commande_id != null ? $("#commande_id").val(reglement.commande_id) : $("#vente_id").val(reglement.vente_id);
        $(".bs-modal-ajout").modal("show");
    }
    function recuPrintRow(idReglement){
        window.open("recu-reglement-pdf/" + idReglement ,'_blank')
    }
    function deleteRow(idReglement) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var reglement =_.findWhere(rows, {id: idReglement});
           $scope.$apply(function () {
              $scope.populateForm(reglement);
          });
       $(".bs-modal-suppression").modal("show");
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function concerneFormatter(id, row){
        if(row.id_client){
            return '<span class="text-bold text-green"> Client ' + row.full_name_client + '</span>';
        }
        if(row.commande_id){
            return '<span class="text-bold text-red"> Fournisseur ' + row.full_name_fournisseur + '</span>';
        }
    }
    
    function recuFormatter(id, row){
        if(row.commande_id!=null){
          return '---';
        }else{
          return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Imprimer le reçu" onClick="javascript:recuPrintRow(' + row.id + ');"><i class="fa fa-print"></i></button>'
        }
    }
    function objetFormatter(id, row){
        if(row.vente_id){
            return '<span class="text-bold"> Facture N° ' + row.numero_facture + '</span>';
        }
        if(row.commande_id){
            return '<span class="text-bold"> Commande N° ' + row.numero_bon + '</span>';
        }
    }
    
    function concerner(concerneValue) {
        switch(concerneValue) {
            case 'client':
            if($('#client_id').val()==''){
                $('#fournisseur_id').val("");
                $('#adresse_fournisseur').val("");
                $('#contact_fournisseur').val("");
                $('#montant_reglement').val("");
                $('#moyen_reglement_id').val("");
                $('#montant_ttc').val("");
                $('#montant_restant').val("");
                $('#row_fournisseur').hide();
                $('#facture_fournisseur').hide();
                $('#facture_vente').show();
                $('#row_client').show();
                $('#vente_id').attr('disabled', false)
                $('#commande_id').attr('disabled', true)
                $('#client_id').attr('required', true)
                $('#fournisseur_id').attr('required', false)
                $('#contact_client').val("");
                $('#adresse_client').val("");
                $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                $('#vente_id').html("<option>-- Selectionner la facture --</option>");
            }else{
                $('#fournisseur_id').val("");
                $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                $('#adresse_fournisseur').val("");
                $('#contact_fournisseur').val("");
                $('#commande_id').attr('disabled', true)
                $('#fournisseur_id').attr('required', false)
                $('#client_id').attr('required', true)
                $('#row_fournisseur').hide();
                $('#facture_fournisseur').hide();
            }
            break;
            case 'fournisseur':
            if($('#fournisseur_id').val()==''){
                $('#row_client').hide();
                $('#facture_vente').hide();
                $('#facture_fournisseur').show();
                $('#row_fournisseur').show();
                $('#vente_id').attr('disabled', true)
                $('#commande_id').attr('disabled', false)
                $('#client_id').attr('required', false)
                $('#fournisseur_id').attr('required', true)
                $('#contact_fournisseur').val("");
                $('#adresse_fournisseur').val("");
                $('#contact_client').val("");    
                $('#client_id').val("");    
                $('#adresse_client').val("");    
                $('#montant_ttc').val("");    
                $('#montant_restant').val(""); 
                $('#montant_reglement').val("");
                $('#moyen_reglement_id').val("");
                $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                $('#vente_id').html("<option>-- Selectionner la facture --</option>");
            }else{
                $('#client_id').val("");
                $('#vente_id').html("<option>-- Selectionner la facture --</option>");
                $('#adresse_client').val("");
                $('#contact_client').val("");
                $('#vente_id').attr('disabled', true)
                $('#client_id').attr('required', false)
                $('#fournisseur_id').attr('required', true)
                $('#row_client').hide();
                $('#facture_vente').hide();
            }
            break;
            default:
        }
    }
    function imageFormatter(id, row) { 
          return row.scan_cheque ? "<a target='_blank' href='" + basePath + '/' + row.scan_cheque + "'>Voir le chèque</a>" : "";
    }
    
     function editerReglement(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                    $('#row_client').show();
                    $('#row_fournisseur').hide();
                    $('#row_premier').show();
                    $('#row_second').show();
                    $('#facture_vente').show();
                    $('#facture_fournisseur').hide();
                    $('#vente_id').attr('disabled', false)
                    $('#commande_id').attr('disabled', true)
                    $('#fournisseur_id').attr('required', false)
                    $('#client_id').attr('required', true)
                    $('#contact_client').val("");    
                    $('#contact_fournisseur').val("");    
                    $('#adresse_client').val("");    
                    $('#adresse_fournisseur').val("");  
                    $("#clientAnonyme").val("");
                    $('#montant_ttc').val("");    
                    $('#montant_restant').val(""); 
                    $('#date_reglement').attr('disabled', false)
                    $('#montant_reglement').attr('disabled', false)
                    $('#vente_id').html("<option>-- Selectionner la facture --</option>");
                    $('#commande_id').html("<option>-- Selectionner la facture --</option>");
                     $('input:radio[name=concerne]').val(['client']);
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

