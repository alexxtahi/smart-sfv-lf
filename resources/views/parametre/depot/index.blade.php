@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">

<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <input type="text" id="idDepotModifier" ng-model="depot.id" ng-hide="true" class="hidden">
            @csrf
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$titleControlleur}}</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="libelle_depot">Libell&eacute; *</label>
                        <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" id="libelle_depot" name="libelle_depot" ng-model="depot.libelle_depot" class="form-control" placeholder="Libellé du dépôt" required>
                    </div>
                    <div class="form-group">
                        <label for="adresse_depot">Adresse du d&eacute;p&ocirc;t *</label>
                        <input type="text" onkeyup="this.value = this.value.charAt(0).toUpperCase() + this.value.substr(1);" id="libelle_depot" name="adresse_depot" ng-model="depot.adresse_depot" class="form-control" placeholder="Adresse du dépôt" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_depot">Contact</label>
                        <input type="text" class="form-control bfh-phone" ng-model="depot.contact_depot" id="contact_depot" name="contact_depot" data-format="dd dd-dd-dd-dd" pattern="[0-9]{2} [0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="Contact du dépôt">
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-right">
                    <span class="loader"></span>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Valider</button>
                </div>
                <!-- /.box-footer-->
                <div class="overlay loader-overlay">
                    <i class="fa fa-refresh fa-spin"></i>
                </div>
            </div>
            <!-- /.box -->
        </form>
    </div>
    <div class="col-lg-8 col-md-8 col-sm-8">
        <table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false"
               data-toggle="table"
               data-url="{{url('parametre',['action'=>'liste-depots'])}}"
               data-unique-id="id"
               data-show-toggle="false">
            <thead>
                <tr>
                    <th data-field="libelle_depot" data-searchable="true" data-sortable="true">Libell&eacute;  </th>
                    <th data-field="adresse_depot" data-searchable="true" data-sortable="true">Adresse du d&eacute;p&ocirc;t </th>
                    <th data-field="contact_depot">Contact</th>
                    <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                </tr>
            </thead>
        </table>
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
                    <input type="text" class="hidden" id="idDepotSupprimer"  ng-model="depot.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer le d&eacute;p&ocirc;t <br/><b>@{{depot.libelle_depot}}</b></div>
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
        $scope.populateForm = function (depot) {
            $scope.depot = depot;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.depot = {};
        };
    });
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (depot) {
            $scope.depot = depot;
        };
        $scope.initForm = function () {
            $scope.depot = {};
        };
    });
    
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
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
                var url = "{{route('parametre.depots.store')}}";
             }else{
                var id = $("#idDepotModifier").val();
                var methode = 'PUT';
                var url = 'depots/' + id;
             }
            editerAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $table, ajout);
        });

        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idDepotSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('depots/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });

    });
    
    function updateRow(idDepot) {
        ajout= false;
        var $scope = angular.element($("#formAjout")).scope();
        var depot =_.findWhere(rows, {id: idDepot});
         $scope.$apply(function () {
            $scope.populateForm(depot);
        });
    }

    function deleteRow(idDepot) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var depot =_.findWhere(rows, {id: idDepot});
           $scope.$apply(function () {
              $scope.populateForm(depot);
          });
       $(".bs-modal-suppression").modal("show");
    }
  
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


