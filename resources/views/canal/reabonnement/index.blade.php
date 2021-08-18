@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant' or Auth::user()->role == 'Agence')
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
<div class="col-md-4">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByNumero" placeholder="Rech. par N° Abon nom ou N° decodeur">
    </div>
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
@if(Auth::user()->role!='Agence')
<div class="col-md-3">
    <select class="form-control" id="searchByAgence">
        <option value="0" >-- Toutes les agences --</option>
        @foreach($agences as $agence)
        <option value="{{$agence->id}}"> {{$agence->libelle_agence}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-1">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>
@endif
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('canal',['action'=>'liste-reabonnements'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="abonnement.numero_abonnement">N° abonnement</th>
            <th data-field="abonnement.numero_decodeur">N° d&eacute;codeur</th>
            <th data-field="full_name_abonne">Abonn&eacute;</th>
            <th data-field="date_reabonnements">Date r&eacute;abonnement</th>
            <th data-field="type_abonnement.libelle_type_abonnement">Offre </th>
            <th data-field="option_canals" data-formatter="optionsFormatter">Options </th>
            <th data-field="date_debuts">Date d&eacute;but</th>
            <th data-field="duree">Dur&eacute;e</th>
            <th data-field="montant_reabonnement">Montant r&eacute;abonnement.</th>
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
                        <i class="fa fa-retweet fa-2x"></i>
                        Gestion des r&eacute;bonnements
                    </span>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" id="idReabonnementModifier" ng-hide="true" ng-model="reabonnement.id"/>
                    @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>N° Abonn&eacute;ment *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-edit"></i>
                                        </div>
                                        <select name="abonnement_id" id="abonnement_id" class="form-control" required>
                                            <option value="">-- Sectionner --</option>
                                            @foreach($abonnements as $abonnement)
                                            <option value="{{$abonnement->id}}"> {{$abonnement->numero_abonnement}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Abonn&eacute; </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-phone"></i>
                                        </div>
                                        <input type="text" class="form-control" id="nom_abonne" placeholder="Nom et prénom de l'abonné" readonly>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Contact de l'abonn&eacute;</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control" id="contact_abonne" placeholder="Contact de l'abonné" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date d'abonnement </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control" id="date_abonnement" placeholder="Date d'abonnement" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date de r&eacute;abonnement *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control" ng-model="reabonnement.date_reabonnements" id="date_reabonnement" name="date_reabonnement" value="<?=date('d-m-Y');?>" required>                                
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Offre *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-list"></i>
                                        </div>
                                        <select name="type_abonnement_id" id="type_abonnement_id" class="form-control" required>
                                            <option value="">-- Sectionner l'offre --</option>
                                            @foreach($type_abonnements as $type_abonnement)
                                            <option value="{{$type_abonnement->id}}"> {{$type_abonnement->libelle_type_abonnement}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date d&eacute;but *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control" ng-model="reabonnement.date_debuts" id="date_debut" name="date_debut" value="<?= date('d-m-Y'); ?>" required>                                
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Dure&eacute; *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-edit"></i>
                                        </div>
                                        <select name="duree" id="duree" ng-model="reabonnement.duree" ng-init="reabonnement.duree='1 mois'" class="form-control" required>
                                            @for($i=1;$i<= 24; $i++)
                                            <option value="{{$i}} mois">{{$i}} Mois</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Sectionner les options </label>
                                    <div class="form-group">
                                        <select name="options[]" id="options" class="form-control select2" multiple="multiple">
                                            @foreach($options as $option)
                                            <option value="{{$option->id}}"> {{$option->libelle_option}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Montant total *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-money"></i>
                                        </div>
                                        <input type="text" pattern="[0-9]*" class="form-control" ng-model="reabonnement.montant_reabonnement" id="montant_reabonnement" name="montant_reabonnement" placeholder="Montant total pour le réabonnement" required>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
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
                    <input type="text" class="hidden" id="idReabonnementSupprimer"  ng-model="reabonnement.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le r&eacute;abonnement de <br/><b>@{{reabonnement.abonnement.numero_abonnement}}</b></div>
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
<form>
    <input type="hidden" id="agence_auth" value="{{Auth::user()->agence_id}}"/>
</form>
<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];
    
    appSmarty.controller('formAjoutCtrl', function ($scope) { 
        $scope.populateForm = function (reabonnement) {
            $scope.reabonnement = reabonnement;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.reabonnement = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (reabonnement) {
            $scope.reabonnement = reabonnement;
        };
        $scope.initForm = function () {
            $scope.reabonnement = {};
        };
    });
    
    $(function () {
       $("#abonnement_id,#type_abonnement_id,#options").select2({width: '100%', allowClear: true});
      
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        
        $('#date_reabonnement, #dateDebut, #dateFin, #date_debut').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        }); 
        
        $("#searchByNumero").keyup(function (e) {
            var numero = $("#searchByNumero").val();
            $("#searchByAgence").val(0);
            $("#dateDebut").val("");
            $("#dateFin").val("");
            if(numero == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-reabonnements'])}}"});
            }else{
                $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnements-by-numero/' + numero});
            }
        });
        $("#dateDebut, #dateFin").change(function (e) {
            var agence_auth = $("#agence_auth").val();
            $("#searchByNumero").val("");
            var agence = $("#searchByAgence").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            
            if(agence_auth!=null && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-reabonnements'])}}"});
                return false;
            }
            
            if(agence == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-reabonnements'])}}"});
            }
            if(agence != 0 && dateDebut=="" && dateFin==""){
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnementsby-agence/' + agence});
            }
            if(agence == 0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnements-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(agence != 0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnements-by-periode-agence/' + dateDebut + '/' + dateFin + '/' + agence});
            }
        });
        
        $("#searchByAgence").change(function (e) {
           
            $("#searchByNumero").val("");
            var agence = $("#searchByAgence").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            if(agence == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-reabonnements'])}}"});
            }
            if(agence != 0 && dateDebut=="" && dateFin==""){
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnements-by-agence/' + agence});
            }
            if(agence == 0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnements-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(agence != 0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-reabonnements-by-periode-agence/' + dateDebut + '/' + dateFin + '/' + agence});
            }
        });
        
        $("#btnModalAjout").on("click", function () {
            $("#type_abonnement_id, #abonnement_id, #options").val('').trigger('change');
        });
        
        //Obtenir les informations de l'abonné 
        $('#abonnement_id').change(function(){
            var abonnement_id = $('#abonnement_id').val();
            $.getJSON("../canal/get-infos-abonnement/" + abonnement_id, function (reponse) {
                   $.each(reponse.rows, function (index, abonne) {
                        $('#nom_abonne').val(abonne.full_name_abonne);
                        $('#contact_abonne').val(abonne.contact1);
                        $('#date_abonnement').val(abonne.date_abonnements);
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
                var url = "{{route('canal.reabonnements.store')}}";
             }else{
                var id = $("#idReabonnementModifier").val();
                var methode = 'PUT';
                var url = 'reabonnements/' + id;
             }
            editerReabonnementAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idReabonnementSupprimer").val();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('reabonnements/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    
    function updateRow(idReabonnement) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var reabonnement =_.findWhere(rows, {id: idReabonnement});
         $scope.$apply(function () {
            $scope.populateForm(reabonnement);
        });
         var ids = reabonnement.option_canals.map(function(option){
            return option.id;
        });
        $("#options").select2("val", ids);
        $('#abonnement_id').select2("val", reabonnement.abonnement_id)
        $('#type_abonnement_id').select2("val", reabonnement.type_abonnement_id)
        
        $.getJSON("../canal/get-infos-abonnement/" + reabonnement.abonnement_id, function (reponse) {
                   $.each(reponse.rows, function (index, abonne) {
                        $('#nom_abonne').val(abonne.full_name_abonne);
                        $('#contact_abonne').val(abonne.contact1);
                        $('#date_abonnement').val(abonne.date_abonnements);
                    });
              })
        $(".bs-modal-ajout").modal("show");
    }
  
    function deleteRow(idReabonnement) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var reabonnement =_.findWhere(rows, {id: idReabonnement});
           $scope.$apply(function () {
              $scope.populateForm(reabonnement);
          });
       $(".bs-modal-suppression").modal("show");
    }

    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionsFormatter(options){
        var strOptions = '';
            $.each(options, function (index, option) {
                strOptions += '<span class="label label-success">' + option.libelle_option + '</span>' + ' ';
            });
            return strOptions;
    }
    function imprimePdf(){
        var agence = $("#searchByAgence").val();
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        if(agence==0 && dateDebut=="" && dateFin==""){
            window.open("liste-reabonnements-pdf/" ,'_blank');
        }
        if(agence!=0 && dateDebut=="" && dateFin==""){
            window.open("liste-reabonnements-by-agence-pdf/" + agence,'_blank');  
        }
        if(agence==0 && dateDebut!="" && dateFin!=""){
            window.open("liste-reabonnements-by-periode-pdf/" + dateDebut + "/" + dateFin,'_blank');  
        }
        if(agence!=0 && dateDebut!="" && dateFin!=""){
            window.open("liste-reabonnements-by-periode-agence-pdf/" + dateDebut + "/" + dateFin + "/" + agence,'_blank');  
        }
    }

    
    function editerReabonnementAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
                $("#type_abonnement_id, #abonnement_id, #options").val('').trigger('change');
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


