@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
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
    <div class="col-md-3">
        <select class="form-control" id="searchByArticle">
            <option value="0">-- Tous les articles --</option>
            @foreach($articles as $article)
            <option value="{{$article->id}}"> {{$article->description_article}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-transferts-stocks'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_transferts">Date</th>
            <th data-field="article.description_article">Article </th>
            <th data-field="unite.libelle_unite">Lot </th>
            <th data-field="depot_depart.libelle_depot">D&eacute;p&ocirc;t du d&eacute;part</th>
            <th data-field="depot_arrivee.libelle_depot">D&eacute;p&ocirc;t d'arriv&eacute;e</th>
            <th data-field="quantite">Qt&eacute;</th>
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
        $("#searchByArticle").change(function (e) { 
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var article = $("#searchByArticle").val();
            if(article == 0 && dateDebut=="" && dateFin == ""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-transferts-stocks'])}}"});
            }
            if(article != 0 && dateDebut=="" && dateFin == ""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-transferts-stocks-by-article/' + article});
            }
            if(article != 0 && dateDebut!="" && dateFin != ""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-transferts-stocks-by-periode-article/' + dateDebut + '/' + dateFin + '/' + article});
            }
        });
        $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByArticle").val(0);
            if(dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-transferts-stocks-by-periode/' + dateDebut + '/' + dateFin});
            }else{
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-transferts-stocks'])}}"});
            }
        });
    });
    
    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var article = $("#searchByArticle").val();
        if(article == 0 && dateDebut=="" && dateFin == ""){
            window.open("liste-transferts-stocks-pdf/" ,'_blank');
        }
        if(dateDebut=='' && dateFin=='' && article!=0){
            window.open("liste-transferts-stocks-by-article-pdf/" + article,'_blank');
        }
        if(dateDebut!='' && dateFin!='' && article==0){
            window.open("liste-transferts-stocks-by-periode-pdf/" + dateDebut + '/' + dateFin ,'_blank');  
        }
        if(article != 0 && dateDebut!="" && dateFin != ""){
            window.open("liste-transferts-stocks-by-periode-article-pdf/" + dateDebut + '/' + dateFin + '/' + article ,'_blank');
        }
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection
