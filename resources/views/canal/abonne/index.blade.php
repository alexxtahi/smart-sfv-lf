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
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByName" placeholder="Recherche par nom de l'abonné">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByNumero" placeholder="Recherche par N° de l'abonné">
    </div>
</div>
@if(Auth::user()->role!='Agence')
<div class="col-md-3">
    <select class="form-control" id="searchByLocalite">
        <option value="0">-- Toutes les localit&eacute;--</option>
        @foreach($localites as $localite)
        <option value="{{$localite->id}}"> {{$localite->libelle_localite}}</option>
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
               data-url="{{url('canal',['action'=>'liste-abonnes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="true">
    <thead>
        <tr>
            <th data-formatter="nameFormatter">Nom complet</th>
            <th data-field="date_naissance_abonnes" data-searchable="true">Date de naissance</th>
            <th data-field="contact1">Contact</th>
            <th data-field="contact2" data-visible="false">Contact 2 </th>
            <th data-field="contact_conjoint" data-visible="false">Contact conjoint</th>
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
            <th data-field="localite.libelle_localite">Localit&eacute;</th>
            @endif
            <th data-field="adresse_abonne">Adresse g&eacute;o.</th>
            <th data-field="type_piece.libelle_type_piece" data-visible="false">Type pi&egrave;ce</th>
            <th data-field="numero_piece">Num&eacute;ro pi&egrave;ce</th>
            <th data-field="nation.libelle_nation">Pays</th>
            <th data-field="code_postal" data-visible="false">Code postal</th>
            <th data-field="email_abonne">E-mail</th>
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
                        <i class="fa fa-users fa-2x"></i>
                        Gestion des abonn&eacute;s
                    </span>
                </div>
                <div class="modal-body">
                    <input type="text" class="hidden" id="idAbonneModifier" ng-hide="true" ng-model="abonne.id"/>
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Civilit&eacute; *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <select name="civilite" id="civilite" ng-model="abonne.civilite" ng-init="abonne.civilite='M'" class="form-control" required>
                                        <option value="M">Monsieur</option>
                                        <option value="Mme">Madame</option>
                                        <option value="Mlle">Mademoiselle</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nom de l'abonn&eacute; *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="abonne.full_name_abonne" id="full_name_abonne" name="full_name_abonne" placeholder="Nom & prénom de l'abonné" required>                                
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date de naissance *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="abonne.date_naissance_abonnes" id="date_naissance_abonne" name="date_naissance_abonne" placeholder="Date de naissance" required>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
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
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Situation g&eacute;ographique *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" class="form-control" ng-model="abonne.adresse_abonne" id="adresse_abonne" name="adresse_abonne" placeholder="Adresse géographique" required>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>E-mail </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" ng-model="abonne.email_abonne" id="email_abonne" name="email_abonne" placeholder="Adresse mail de l'abonne">                                
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact principal *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="abonne.contact1" id="contact1" name="contact1" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact de l'abonné" required>
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact secondaire</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="abonne.contact2" id="contact2" name="contact2" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact 2 de l'abonné">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Contact conjoint(e)</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <input type="text" class="form-control bfh-phone" ng-model="abonne.contact_conjoint" id="contact_conjoint" name="contact_conjoint" data-format="(dd) dd-dd-dd-dd" pattern="[(0-9)]{4} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact du conjoint(e)">
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Code postal</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-barcode"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="abonne.code_postal" id="code_postal" name="code_postal" placeholder="Code postal de l'abonné">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Type pi&egrave;ce </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-institution"></i>
                                    </div>
                                    <select name="type_piece_id" id="type_piece_id" class="form-control">
                                        <option value="">-- Type de pi&egrave;ce --</option>
                                        @foreach($type_pieces as $type_piece)
                                        <option value="{{$type_piece->id}}"> {{$type_piece->libelle_type_piece}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Num&eacute;ro pi&egrave;ce</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </div>
                                    <input type="text" class="form-control" ng-model="abonne.numero_piece" id="numero_piece" name="numero_piece" placeholder="Numéro pièce d'identité">                                
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pays </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-flag"></i>
                                    </div>
                                    <select name="nation_id" id="nation_id" class="form-control">
                                        <option value="">-- Sectionner le pays --</option>
                                        @foreach($nations as $nation)
                                        <option value="{{$nation->id}}"> {{$nation->libelle_nation}}</option>
                                        @endforeach
                                    </select>
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
                    <input type="text" class="hidden" id="idAbonneSupprimer"  ng-model="abonne.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'abonn&eacute; <br/><b>@{{abonne.civilite +'. ' + abonne.full_name_abonne}}</b></div>
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
        $scope.populateForm = function (abonne) {
            $scope.abonne = abonne;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.abonne = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (abonne) {
            $scope.abonne = abonne;
        };
        $scope.initForm = function () {
            $scope.abonne = {};
        };
    });
    
    $(function () {
       $("#searchByLocalite,#localite_id, #nation_id").select2({width: '100%', allowClear: true});
      
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        
        $('#date_naissance_abonne, #date_abonnement').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        }); 
        
        $("#searchByLocalite").change(function (e) {
            var localite = $("#searchByLocalite").val();
            $("#searchByName").val("");
            $("#searchByNumero").val("");
            if(localite == 0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-abonnes'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-abonnes-by-localite/' + localite});
            }
        });
        $("#searchByName").change(function (e) {
            var name = $("#searchByName").val();
            $("#searchByLocalite").val(0);
            if(name == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-abonnes'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-abonnes-by-name/' + name});
            }
        });
        $("#searchByNumero").change(function (e) {
            var numero = $("#searchByNumero").val();
            $("#searchByLocalite").val(0);
            if(numero == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-abonnes'])}}"});
            }else{
              $table.bootstrapTable('refreshOptions', {url: '../canal/liste-abonnes-by-numero/' + numero});
            }
        });
        
        $("#btnModalAjout").on("click", function () {
            $("#nation_id, #localite_id").val('').trigger('change');
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
                var url = "{{route('canal.abonnes.store')}}";
             }else{
                var id = $("#idAbonneModifier").val();
                var methode = 'PUT';
                var url = 'abonnes/' + id;
             }
            editerAbonneAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idAbonneSupprimer").val();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('abonnes/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
    });
    
    function updateRow(idAbonne) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var abonne =_.findWhere(rows, {id: idAbonne});
         $scope.$apply(function () {
            $scope.populateForm(abonne);
        });
        $('#localite_id').select2("val", abonne.localite_id)
        abonne.nation_id!=null ? $('#nation_id').select2("val", abonne.nation_id) : $('#nation_id').select2("val", "");
        abonne.type_piece_id!=null ? $('#type_piece_id').val(abonne.type_piece_id) : $('#type_piece_id').val("");
        $(".bs-modal-ajout").modal("show");
    }
  
    function deleteRow(idAbonne) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var abonne =_.findWhere(rows, {id: idAbonne});
           $scope.$apply(function () {
              $scope.populateForm(abonne);
          });
       $(".bs-modal-suppression").modal("show");
    }
    
    function nameFormatter(id, row){
        return row.civilite + '. ' + row.full_name_abonne;
    }
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function imprimePdf(){
        var localite = $("#searchByLocalite").val();
        if(localite == 0){
            window.open("liste-abonnes-pdf/" ,'_blank');
        }else{
            window.open("liste-abonnes-by-localite-pdf/" + localite,'_blank');  
        }
    }

    
    function editerAbonneAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
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
            $("#nation_id, #localite_id").val('').trigger('change');
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


