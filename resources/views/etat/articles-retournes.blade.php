@extends('layouts.app')
@section('content')
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
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
        <select class="form-control" id="searchByArticle">
            <option value="0">-- Tous les articles --</option>
            @foreach($articles as $article)
            <option value="{{$article->id}}"> {{$article->description_article}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Toues les &eacute;p&ocirc;t --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
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
               data-url="{{url('boutique',['action'=>'liste-retour-articles'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-field="date_retours">Date</th>
            <th data-formatter="ticketFormatter">N° Ticket</th>
            <th data-field="libelle_depot">D&eacute;p&ocirc;t </th>
            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant total </th>
            <!--<th data-field="id" data-formatter="optionFormatter" data-width="60px" data-align="center"><i class="fa fa-wrench"></i></th>-->
        </tr>
    </thead>
</table>

<script type="text/javascript">
    var ajout = true;
    var $table = jQuery("#table"), rows = [];
 
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });

        $("#searchByArticle").select2({width: '100%', allowClear: true});
        
        $('#dateDebut,#dateFin').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr',
            maxDate : new Date()
        });
        
        $("#dateDebut,#dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var date_fin = $("#dateFin").val();
            $("#searchByArticle").select2("val", 0);
            $("#searchByDepot").val(0);
            if(dateDebut =="" && date_fin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-retour-articles'])}}"});
            }
            if(dateDebut !="" && date_fin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode/' + dateDebut + '/' + date_fin});
            }
        }); 
        
        $("#searchByDepot").change(function (e) {
            var depot = $("#searchByDepot").val();
            var article = $("#searchByArticle").val();
            var dateDebut = $("#dateDebut").val();
            var date_fin = $("#dateFin").val();
            $("#searchByArticle").select2("val", 0);
            if(dateDebut =="" && date_fin=="" && article==0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-retour-articles'])}}"});
            }
            if(dateDebut !="" && date_fin!="" && article==0 && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode/' + dateDebut + '/' + date_fin});
            }
            if(dateDebut =="" && date_fin=="" && article==0 && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-depot/' + depot});
            }
            if(dateDebut =="" && date_fin=="" && article!=0 && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-article/' + article});
            }
            if(dateDebut!="" && date_fin!="" && article==0 && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode-depot/' + dateDebut + '/' + date_fin + '/' + depot});
            }
            if(dateDebut!="" && date_fin!="" && article!=0 && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode-article/' + dateDebut + '/' + date_fin + '/' + article});
            }
        });
        $("#searchByArticle").change(function (e) {
            var depot = $("#searchByDepot").val();
            var article = $("#searchByArticle").val();
            var dateDebut = $("#dateDebut").val();
            var date_fin = $("#dateFin").val();
            
            if(dateDebut =="" && date_fin=="" && article==0 && depot==0){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-retour-articles'])}}"});
            }
            if(dateDebut !="" && date_fin!="" && article==0 && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode/' + dateDebut + '/' + date_fin});
            }
            if(dateDebut =="" && date_fin=="" && article==0 && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-depot/' + depot});
            }
            if(dateDebut =="" && date_fin=="" && article!=0 && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-article/' + article});
            }
            if(dateDebut!="" && date_fin!="" && article==0 && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode-depot/' + dateDebut + '/' + date_fin + '/' + depot});
            }
            if(dateDebut!="" && date_fin!="" && article!=0 && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-retour-article-by-periode-article/' + dateDebut + '/' + date_fin + '/' + article});
            }
        });
    });
    function imprimePdf(){
        var depot = $("#searchByDepot").val();
        var article = $("#searchByArticle").val();
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        if(dateDebut =="" && dateFin=="" && article==0 && depot==0){
            window.open("articles-retournes-pdf/" ,'_blank');
        }
        if(dateDebut!="" && dateFin!="" && article==0 && depot==0){
            window.open("articles-retournes-periode-pdf/" + dateDebut + "/" + dateFin,'_blank');
        }
        if(dateDebut!="" && dateFin!="" && article==0 && depot!=0){
            window.open("articles-retournes-periode-depot-pdf/" + dateDebut + "/" + dateFin + "/" + depot,'_blank');
        }
        if(dateDebut!="" && dateFin!="" && article!=0 && depot==0){
            window.open("articles-retournes-periode-article-pdf/" + dateDebut + "/" + dateFin + "/" + article,'_blank');
        }
        if(dateDebut=="" && dateFin=="" && article!=0 && depot==0){
            window.open("articles-retournes-by-article-pdf/" + article,'_blank');
        }
        if(dateDebut=="" && dateFin=="" && article==0 && depot!=0){
            window.open("articles-retournes-by-depot-pdf/" + depot,'_blank');
        }
    }
    function printRow(idRetourArticle){
        window.open("../boutique/fiche-retour-article-pdf/" + idRetourArticle,'_blank');
    }
    
    function ticketFormatter(id,row){
        return row.numero_facture!=null ? '<span class="text-bold"> FACT' + row.numero_facture+ '</span>' : '<span class="text-bold">' + row.numero_ticket+ '</span>';
    }
    function montantRetourFormatter(id, row){
        return '<span class="text-bold">' + $.number(row.quantite*row.prix_unitaire)+'</span>';
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+'</span>';
    }
    function optionFormatter(id, row) { 
            return '<button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Fiche" onClick="javascript:printRow(' + row.id_ligne + ');"><i class="fa fa-print"></i></button>';
    }
</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection