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
    <div class="col-md-6">
        <label>Voir la liste sur une p&eacute;riode</label>
    </div>
</div>
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
<!--    <div class="col-md-4">
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Toutes les d&eacute;p&ocirc;ts --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>-->
    <div class="col-md-6">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-raports-caisses'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="tiketFormatter" data-width="100px" data-align="center">Ticket</th>
            <th data-field="date_ventes">Date </th>
            <th data-field="numero_ticket">Ticket </th>
            <!--<th data-formatter="montantFormatter">Montant HT</th>-->
            <!--<th data-formatter="tvaFormatter">TVA</th>-->
            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant TTC</th>
            <th data-field="sommeRemise" data-formatter="montantFormatter">R&eacute;mise</th>
            <th data-formatter="netPayerFormatter">Net &agrave; payer</th>
            <!--<th data-field="depot.libelle_depot">D&eacute;p&ocirc;t </th>-->
       </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
       
        $('#dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr'
        });
        $("#searchByDepot").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var depot = $("#searchByDepot").val();
            if(depot == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-raports-caisses'])}}"});
            }
            if(depot != 0 && dateDebut=="" && dateFin==""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-raports-caisses-by-depot/' + depot});
            }
            if(depot == 0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-raports-caisses-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(depot != 0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-raports-caisses-by-depot-periode/' + dateDebut + '/' + dateFin + '/' + depot});
            }
        });
        $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByDepot").val(0);
          
            if(dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-raports-caisses-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('../boutique', ['action' => 'liste-raports-caisses'])}}"});
            }
        });
    });
    
    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
       
        if(dateDebut=='' && dateFin==''){
            window.open("../boutique/raports-caisses-pdf/",'_blank');
        }
        if(dateDebut!='' && dateFin!=''){
            window.open("../boutique/raports-caisses-by-periode-pdf/" + dateDebut + '/' + dateFin ,'_blank');  
        }
    }
    function ticketPrintRow(idVente){
        window.open("../boutique/ticket-vente-pdf/" + idVente ,'_blank')
    }
    function tiketFormatter(id, row){
       return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Ticket" onClick="javascript:ticketPrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
    }
 
    function netPayerFormatter(id, row){
       var netApayer = row.sommeTotale - row.sommeRemise;
       return '<span class="text-bold">' + $.number(netApayer)+ '</span>';
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection