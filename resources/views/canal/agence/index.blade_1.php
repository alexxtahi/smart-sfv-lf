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
<div class="col-md-4">
    <select class="form-control" id="searchByLocalite">
        <option value="0">-- Toutes les localit&eacute;--</option>
        @foreach($localites as $localite)
        <option value="{{$localite->id}}"> {{$localite->libelle_localite}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-4">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="true" 
               data-toggle="table"
               data-url="{{url('canal',['action'=>'liste-agences'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="numero_identifiant_agence" data-searchable="true">N° identifiant</th>
            <th data-field="libelle_agence" data-searchable="true">Nom de l'agence</th>
            <th data-field="contact_agence">Contact</th>
            <th data-field="localite.libelle_localite">Localit&eacute;</th>
            <th data-field="adresse_agence">Adresse g&eacute;o.</th>
            <th data-field="nom_responsable">Responsable</th>
            <th data-field="contact_responsable">Contact respon.</th>
            <th data-field="email_agence">E-mail</th>
            <th data-field="numero_cc">N° compte contr.</th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
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
                        <i class="fa fa-hospital-o fa-2x"></i>
                        Gestion des agences
                    </span>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" id="idAgenceModifier" ng-hide="true" ng-model="agence.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>N° identifiant *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="agence.numero_identifiant_agence" id="numero_identifiant_agence" name="numero_identifiant_agence" placeholder="N° identifiant de l'agence" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Nom de l'agence *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-hospital-o"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="agence.libelle_agence" id="libelle_agence" name="libelle_agence" placeholder="Nom de l'agence" required>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Localit&eacute; *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-institution"></i>
                                    </div>
                                    <select name="localite_id" id="localite_id" class="form-control" required>
                                        <option value="">-- Sectionner la localit&eacute; --</option>
                                        @foreach($localites as $localite)
                                        <option value="{{$localite->id}}"> {{$localite->libelle_localite}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Situation g&eacute;ographique *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="agence.adresse_agence" id="adresse_agence" name="adresse_agence" placeholder="Adresse géographique" required>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Contact de l'agence *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="agence.contact_agence" id="contact_agence" name="contact_agence" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact de l'agence" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>E-mail </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" ng-model="agence.email_agence" id="email_agence" name="email_agence" placeholder="Adresse mail de l'agence">                                
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>N° du compte contribuable *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-list"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="agence.numero_cc" id="numero_cc" name="numero_cc" placeholder="N° du compte contribuable" required>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Responsable *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="agence.nom_responsable" id="nom_responsable" name="nom_responsable" placeholder="Nom du responsable" required>                                
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact du responsable *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="agence.contact_responsable" id="contact_responsable" name="contact_responsable" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact du responsable" required>
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
                    <input type="text" class="hidden" id="idAgenceSupprimer"  ng-model="agence.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'agence <br/><b>@{{agence.libelle_agence +' N° ident : ' + agence.numero_identifiant_agence}}</b></div>
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
        $scope.populateForm = function (agence) {
            $scope.agence = agence;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.agence = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (agence) {
            $scope.agence = agence;
        };
        $scope.initForm = function () {
            $scope.agence = {};
        };
    });
    
    $(function () {
       $("#localite_id, #searchByLocalite").select2({width: '100%', allowClear: true});
      
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        
        $("#searchByLocalite").change(function (e) {
            var localite = $("#searchByLocalite").val();
            if(localite == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-agences'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-agences-by-localite/' + localite});
            }
        });
        
        $("#btnModalAjout").on("click", function () {
            $("#localite_id").val('').trigger('change');
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
                var url = "{{route('canal.agences.store')}}";
             }else{
                var id = $("#idAgenceModifier").val();
                var methode = 'PUT';
                var url = 'agences/' + id;
             }
            editerAgenceAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idAgenceSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('agences/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    
    function updateRow(idAgence) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var agence =_.findWhere(rows, {id: idAgence});
         $scope.$apply(function () {
            $scope.populateForm(agence);
        });
        $('#localite_id').select2("val", agence.localite_id)
        $(".bs-modal-ajout").modal("show");
    }
  
    function deleteRow(idAgence) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var agence =_.findWhere(rows, {id: idAgence});
           $scope.$apply(function () {
              $scope.populateForm(agence);
          });
       $(".bs-modal-suppression").modal("show");
    }
    
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function imprimePdf(){
        var localite = $("#searchByLocalite").val();
        if(localite == 0){
            window.open("liste-agences-pdf/" ,'_blank');
        }else{
            window.open("liste-agences-by-localite-pdf/" + localite,'_blank');  
        }
    }

    
    function editerAgenceAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
            }
            $("#localite_id").val('').trigger('change');
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


