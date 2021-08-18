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

<div class="col-md-4">
    <select class="form-control" id="searchByAgence">
        <option value="0">-- Toutes les agences --</option>
        @foreach($agences as $agence)
        <option value="{{$agence->id}}"> {{$agence->libelle_agence}}</option>
        @endforeach
    </select>
</div>
<div class="col-md-2">
    <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
</div>
</div>
<div class="col-md-6">
    <p class="text-bold h3"> Total ventes : <span class="text-bold text-yellow" id="total_rechargement">0</span></p>
</div>
<div class="col-md-6">
    <p class="text-bold h3"> Montant timbres : <span class="text-bold text-red" id="total_timbre">0</span></p>
</div>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('canal',['action'=>'liste-mouvement-ventes'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_operations">Date</th>
            <th data-formatter="concerneFormatter">Concerne</th>
            <th data-formatter="montantFormatter">Montant</th>
            <th data-formatter="timbreFormatter" data-width="120px">Timbre</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
            $("#total_rechargement").html($.number(data.total_rechargement));
            $("#total_timbre").html($.number(data.total_timbre));
        });
        
        $('#dateDebut, #dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        }); 
        
        $("#searchByAgence").change(function (e) {
            var agence = $("#searchByAgence").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            
            if(dateDebut == "" && dateFin == "" && agence == 0){
               $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-mouvement-ventes'])}}"});
            }
            if(dateDebut == "" && dateFin == "" && agence != 0){
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-mouvement-ventes-by-agence/'+ agence});
            }
            if(dateDebut != "" && dateFin != "" && agence == 0){
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-mouvement-ventes-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(dateDebut != "" && dateFin != "" && agence != 0){
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-mouvement-ventes-by-agence-periode/' + agence + '/' + dateDebut + '/' + dateFin});
            }
        });
        $("#dateDebut, #dateFin").change(function (e) {
            var agence = $("#searchByAgence").val();
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            
            if(dateDebut == "" && dateFin == "" && agence == 0){
               $table.bootstrapTable('refreshOptions', {url: "{{url('canal', ['action' => 'liste-mouvement-ventes'])}}"});
            }
            if(dateDebut == "" && dateFin == "" && agence != 0){
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-mouvement-ventes-by-agence/'+ agence});
            }
            if(dateDebut != "" && dateFin != "" && agence == 0){
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-mouvement-ventes-by-periode/' + dateDebut + '/' + dateFin});
            }
            if(dateDebut != "" && dateFin != "" && agence != 0){
               $table.bootstrapTable('refreshOptions', {url: '../canal/liste-mouvement-ventes-by-agence-periode/' + agence + '/' + dateDebut + '/' + dateFin});
            }
        });
    });

    function montantFormatter(id, rows){
        var montant = rows.montant_recharge_client;
        return '<span class="text-bold">' + $.number(montant) + '</span>';
    }
    function timbreFormatter(id, rows){
       
        return rows.montant_recharge_client > 5000 ? '<span class="text-bold">' + 100 + '</span>' : "---";
    }
    
    function concerneFormatter(id, rows){
        if(rows.idReabonnement){
           return "<span class='text-bold'> Réabonnement effectuer par " + rows.libelle_agence_reabonnement + "</span>";
        }
        if(rows.idAbonnement){
            return "<span class='text-bold'> Abonnement effectuer par " + rows.libelle_agence_abonnement + "</span>";;
        }
        if(rows.idMateriel){
            return "<span class='text-bold'> Vente de matériel effectuer par " + rows.libelle_agence_materiel + "</span>";;
        }
    }

    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var agence = $("#searchByAgence").val();
       
        if(dateDebut=="" && dateFin=="" && agence==0){
            window.open("liste-mouvement-ventes-pdf/" ,'_blank');
        }
        if(dateDebut=="" && dateFin=="" && agence!=0){
            window.open("liste-mouvement-ventes-by-agence-pdf/" + agence,'_blank');
        }
        if(dateDebut!="" && dateFin!="" && agence==0){
            window.open("liste-mouvement-ventes-by-periode-pdf/" + dateDebut + "/"+ dateFin,'_blank');  
        }
         if(dateDebut!="" && dateFin!="" && agence!=0){
            window.open("liste-mouvement-ventes-by-agence-periode-pdf/" + agence + "/" + dateDebut + "/"+ dateFin,'_blank');  
        }
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection


