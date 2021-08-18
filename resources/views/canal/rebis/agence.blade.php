@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Agence')
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
<div class="col-md-4">
    <select class="form-control" id="searchByTypeCaution">
        <option value="0">-- Tous les type de caution --</option>
        @foreach($type_cautions as $caution)
        <option value="{{$caution->id}}"> {{$caution->libelle_type_caution}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-2">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>
</div>
<div class="col-md-4">
    <p class="text-bold h3"> Total rechargement : <span class="text-bold text-yellow" id="total_rechargement">0</span></p>
</div>
<div class="col-md-4">
    <p class="text-bold h3"> Total distribu&eacute; : <span class="text-bold text-warning" id="total_distribue">0</span></p>
</div>
<div class="col-md-4">
    <p class="text-bold h3"> Total disponible : <span class="text-bold text-green" id="total_disponible">0</span></p>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('canal',['action'=>'liste-rebis'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_operations">Date</th>
            <th data-formatter="concerneFormatter">Concerne</th>
            <th data-formatter="montantFormatter">Montant</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];
    
    appSmarty.controller('formAjoutCtrl', function ($scope) { 
        $scope.populateForm = function (cautionAgence) {
            $scope.cautionAgence = cautionAgence;
        };
        $scope.initForm = function () {
            ajout = true;
            $scope.cautionAgence = {};
        };
    });

    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (cautionAgence) {
            $scope.cautionAgence = cautionAgence;
        };
        $scope.initForm = function () {
            $scope.cautionAgence = {};
        };
    });
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
            $("#total_rechargement").html($.number(data.total_rechargement));
            $("#total_distribue").html($.number(data.total_distribue));
            $("#total_disponible").html($.number(data.total_rechargement-data.total_distribue));
        });
        
        $('#dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        }); 
        
        $("#searchByTypeCaution").change(function (e) {
            var typeCaution = $("#searchByTypeCaution").val();
            $("#dateDebut").val("");
            $("#dateFin").val("");
            if(typeCaution == 0){
               $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-rebis'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-rebis-by-type-caution/'+ typeCaution});
            }
        });
        $("#dateDebut, #dateFin").change(function (e) {
            $("#searchByTypeCaution").val(0);
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            if(dateDebut=="" && dateFin==""){
               $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-rebis'])}}"});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-rebis-by-periode/' + dateDebut + '/' + dateFin});
            }
        });
    });

    function montantFormatter(id, rows){
        var montant = 0;
        if(rows.demande_approvi_canal_id!=null){
            montant = rows.montant_recharge;
        }
        if(rows.caution_agence_id!=null){
            montant = rows.montant_recharge_agence;
        }
        return montant ? '<span class="text-bold">' + $.number(montant)+ '</span>' : "--";
    }
    
    function concerneFormatter(id, rows){
        if(rows.concerne=='Agence'){
           return "<span class='text-bold text-green'>Rechargement de l'agence " + rows.numero_identifiant_agence + " pour "+rows.libelle_caution_agence+"</span>";
        }
        if(rows.concerne=='Canal'){
            return "<span class='text-bold text-red'>Rechargement chez Canal pour "+rows.libelle_caution_canal+"</span>";
        }
    }

    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var typeCaution = $("#searchByTypeCaution").val();
        if(dateDebut=="" && dateFin=="" && typeCaution==0){
            window.open("liste-rebis-pdf/" ,'_blank');
        }
        if(dateDebut!="" && dateFin!="" && typeCaution==0){
            window.open("liste-rebis-by-periode-pdf/" +dateDebut+"/"+ dateFin,'_blank');  
        }
        if(dateDebut=="" && dateFin=="" && typeCaution!=0){
            window.open("liste-rebis-by-type-caution-pdf/" + typeCaution,'_blank');  
        }
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


