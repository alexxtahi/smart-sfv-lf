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
        <select class="form-control" id="searchByClient">
            <option value="0">-- Tous les clients --</option>
            @foreach($clients as $client)
            <option value="{{$client->id}}"> {{$client->full_name_client}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control" id="searchByFournisseur">
            <option value="0">-- Tous les fournisseurs --</option>
            @foreach($fournisseurs as $fournisseur)
            <option value="{{$fournisseur->id}}"> {{$fournisseur->full_name_fournisseur}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
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
             <th data-field="date_reglements">Date  </th>
            <th data-field="moyen_reglement.libelle_moyen_reglement">Moyen de payement </th>
            <th data-field="montant_reglement" data-formatter="montantFormatter">Montant</th>
            <th data-formatter="concerneFormatter">Concern&eacute;</th>
            <th data-formatter="objetFormatter">Objet</th>
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
        
        $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByFournisseur").val(0);
            $("#searchByClient").val(0);
            if(dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-periode/' + dateDebut + '/' + dateFin});
            }else{
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }
        });
        $("#searchByClient").change(function (e) { 
            var client= $("#searchByClient").val();
            $("#searchByFournisseur").val(0);
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
           
            if(client==0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }
            if(client!=0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-client/' + client});
            }
            if(client!=0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-periode-client/'+ dateDebut + '/' + dateFin + '/' + client});
            }
            if(client==0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-periode/'+ dateDebut + '/' + dateFin});
            }
        });
        
        $("#searchByFournisseur").change(function (e) { 
            $("#searchByClient").val(0);
            var fournisseur = $("#searchByFournisseur").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
           
            if(fournisseur==0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-reglements'])}}"});
            }
            if(fournisseur!=0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-fournisseur/' + fournisseur});
            }
            if(fournisseur!=0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-periode-fournisseur/'+ dateDebut + '/' + dateFin + '/' + fournisseur});
            }
            if(fournisseur==0 && dateDebut!="" && dateFin!=""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-reglements-by-periode/'+ dateDebut + '/' + dateFin});
            }
        });
    });
    
    function imprimePdf(){
        var client= $("#searchByClient").val();
        var fournisseur = $("#searchByFournisseur").val();
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
       
        if(client==0 && fournisseur==0 && (dateDebut=="" || dateFin=="")){
            window.open("liste-reglements-pdf/",'_blank');  
        }
        if(client==0 && fournisseur==0 && dateDebut!="" && dateFin!=""){
            window.open("liste-reglements-by-periode-pdf/" + dateDebut + "/" + dateFin,'_blank');  
        }
        if(client!=0 && fournisseur==0 && dateDebut=="" && dateFin==""){
            window.open("liste-reglements-by-client-pdf/" + client,'_blank');  
        }
        if(client==0 && fournisseur!=0 && dateDebut=="" && dateFin==""){
            window.open("liste-reglements-by-fournisseur-pdf/" + fournisseur,'_blank');  
        }
        if(client!=0 && fournisseur==0 && dateDebut!="" && dateFin!=""){
            window.open("liste-reglements-by-periode-client-pdf/" + dateDebut + "/" + dateFin + "/" + client,'_blank');  
        }
        if(client==0 && fournisseur!=0 && dateDebut!="" && dateFin!=""){
            window.open("liste-reglements-by-periode-fournisseur-pdf/" + dateDebut + "/" + dateFin + "/" + fournisseur,'_blank');  
        }
    }

     function concerneFormatter(id, row){
        if(row.id_client){
            return '<span class="text-bold text-green"> Client ' + row.full_name_client + '</span>';
        }
        if(row.commande_id){
            return '<span class="text-bold text-red"> Fournisseur ' + row.full_name_fournisseur + '</span>';
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
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection