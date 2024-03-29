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
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <input type="text" class="form-control" id="dateDebut" placeholder="Date du début">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <input type="text" class="form-control" id="dateFin" placeholder="Date de fin">
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Tous les d&eacute;pots --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-4">
    <p class="text-bold h4"> Total HT : <span class="text-bold text-green" id="totalHT">0</span></p>
</div>
<div class="col-md-3">
    <p class="text-bold h4"> Total TVA : <span class="text-bold text-red" id="totalTVA">0</span></p>
</div>
<div class="col-md-4">
    <p class="text-bold h4"> Total TTC : <span class="text-bold text-green" id="totalTTC">0</span></p>
</div>
<br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-declarations'])}}"
               data-page-list="[10,25,50,100]"
               data-page-size="25"
               data-unique-id="id"
               data-id-field="id"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="id" data-formatter="otherOptionFormatter" data-width="60px" data-align="center"><i class="fa fa-opera"></i></th>
            <th data-field="date_declarations">Date</th>
            <th data-formatter="montantFormatter" data-field="montantHT">Total HT</th>
            <th data-formatter="montantFormatter" data-field="montantTTC">Total TTC</th>
            <th data-formatter="tvaFormatter" data-field="montantTva">Total TVA</th>
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
        </tr>
    </thead>
</table>

<!-- Modal liste tickets déclarés -->
<div class="modal fade bs-modal-liste" id="formListe" ng-controller="formListeCtrl" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <span style="font-size: 16px;">
                    <i class="fa fa-list fa-2x"></i>
                    Liste des tickets de la d&eacute;claration TVA du <b>@{{declaration.date_declarations}}</b></b>
                </span>
            </div>
            <div class="modal-body ">
                <table id="tableListe" class="table table-success table-striped box box-success"
                       data-pagination="true"
                       data-search="false"
                       data-toggle="table"
                       data-unique-id="id">
                    <thead>
                        <tr>
                            <th data-field="numero_ticket">N° Ticket</th>
                            <th data-field="description_article">Article</th>
                            <th data-field="libelle_unite">Unit&eacute;</th>
                            <th data-field="quantite">Qt&eacute;</th>
                            <th data-field="prix_ht" data-formatter="montantFormatter">Prix HT</th>
                            <th data-field="prix_vente_ttc" data-formatter="montantFormatter">Prix TTC</th>
                            <th data-formatter="montantHT">Montant HT</th>
                            <th data-formatter="montantTTC">Montant TTC </th>
                            <th data-formatter="montantTvaFormatter">Montant TVA </th>
                            <th data-formatter="tvaListeFormatter">TVA </th>
                            <th data-field="montant_remise" data-formatter="montantFormatter">Remise</th>
                            <th data-formatter="netFormatter">Montant net</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
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
                    <input type="text" class="hidden" id="idDeclarationSupprimer"  ng-model="declaration.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer cette declaration TVA du <br/><b>@{{declaration.date_declarations}}</b></div>
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
    var $table = jQuery("#table"), rows = [], $tableListe = jQuery("#tableListe");
    
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (declaration) {
            $scope.declaration = declaration;
        };
        $scope.initForm = function () {
            $scope.declaration = {};
        };
    });
    
     appSmarty.controller('formListeCtrl', function ($scope) {
        $scope.populateFormListe = function (declaration) {
            $scope.declaration = declaration;
        };
        $scope.initForm = function () {
            $scope.declaration = {};
        };
    });
    
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
            $("#totalHT").html($.number(data.totalHT));
            $("#totalTTC").html($.number(data.totalTTC));
            $("#totalTVA").html($.number(data.totalTTC-data.totalHT));
        });
        
        $('#dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr'
        });

        $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();
            
            if(dateDebut=='' && dateFin=='' && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-declarations'])}}"});
            }
            if(dateDebut!='' && dateFin!='' && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-declaration-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(dateDebut=='' && dateFin=='' && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-declaration-depot/' + depot});
            }
            if(dateDebut!='' && dateFin!='' && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-declaration-depot-periode/' + dateDebut + '/' + dateFin + '/' +  depot});
            }
        });
        
        $("#searchByDepot").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();
            
            if(dateDebut=='' && dateFin=='' && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-declarations'])}}"});
            }
            if(dateDebut!='' && dateFin!='' && depot==0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-declaration-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(dateDebut=='' && dateFin=='' && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-declaration-depot/' + depot});
            }
            if(dateDebut!='' && dateFin!='' && depot!=0){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-declaration-depot-periode/' + dateDebut + '/' + dateFin + '/' +  depot});
            }
        });
        
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idDeclarationSupprimer").val();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('declarations/' + id, $(this).serialize(), $question, $ajaxLoader, $table);
        });
 
    });
    
    function deleteRow(idDeclaration) {
          var $scope = angular.element($("#formSupprimer")).scope();
          var declaration =_.findWhere(rows, {id: idDeclaration});
           $scope.$apply(function () {
              $scope.populateForm(declaration);
          });
       $(".bs-modal-suppression").modal("show");
    }
    
    function listeRow(idDeclaration) {
          var $scope = angular.element($("#formListe")).scope();
          var declaration =_.findWhere(rows, {id: idDeclaration});
           $scope.$apply(function () {
              $scope.populateFormListe(declaration);
          });
          
        $tableListe.bootstrapTable('refreshOptions', {url: "../boutique/liste-tickets-declares/" + idDeclaration});
        
        $(".bs-modal-liste").modal("show");
    }
    
    function printRow(idDeclaration){ 
        window.open("../boutique/ticket-declares-pdf/" + idDeclaration,'_blank');
    }

    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    
    function tvaFormatter(id, row){
        var montant = row.montantTTC - row.montantHT;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    
     function montantHT(id, row){
        return '<span class="text-bold">' + $.number(row.prix_ht*row.quantite)+ '</span>';
    }
    function montantTTC(id, row){
        return '<span class="text-bold">' + $.number(row.prix_vente_ttc*row.quantite)+ '</span>';
    }
    
    function montantTvaFormatter(id, row){
        var montant = row.prix_ht*row.montant_tva*row.quantite;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function tvaListeFormatter(id, row){
        return '<span class="text-bold">' + row.montant_tva * 100 + '%</span>';
    }
    function netFormatter(id,row){
        var montant = row.prix_vente_ttc-row.montant_remise;
         return '<span class="text-bold">' + $.number(montant*row.quantite)+ '</span>';
    }

    
    function optionFormatter(id, row) {
        return '<button class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Voir la liste" onClick="javascript:listeRow(' + id + ');"><i class="fa fa-list"></i></button>\n\
                <button class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
    function otherOptionFormatter(id, row){
        return '<button class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Imprimer" onClick="javascript:printRow(' + id + ');"><i class="fa fa-print"></i></button>';
    }
   //<!--<button class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Exporter" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-file-excel-o"></i></button>-->


</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection