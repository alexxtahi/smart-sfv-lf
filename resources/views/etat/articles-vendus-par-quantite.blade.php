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
    <div class="col-md-4">
        <select class="form-control" id="searchByArticle">
            <option value="0">-- Toutes les articles --</option>
            @foreach($articles as $article)
            <option value="{{$article->id}}"> {{$article->description_article}}</option>
            @endforeach
        </select>
    </div>
     <div class="col-md-3">
        <select class="form-control" id="searchByDepot">
            <option value="0">-- Toutes les d&eacute;p&ocirc;ts --</option>
            @foreach($depots as $depot)
            <option value="{{$depot->id}}"> {{$depot->libelle_depot}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <a class="btn btn-success pull-right" onclick="imprimePdf()">Imprimer</a><br/>
    </div>
</div><br/>
<table id="table" class="table table-warning table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-url="{{url('boutique',['action'=>'liste-articles-vendus-by-quantite'])}}"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
           <th data-field="article.code_barre">Code barre</th>
            <th data-field="article.description_article">Article</th>
            <th data-field="unite.libelle_unite">Colis</th>
            <th data-field="prix">Prix U.</th>
            <th data-field="quantite" data-align="center">Quantit&eacute; </th>
            <th data-formatter="montantFormatter">Valeur </th>
       </tr>
    </thead>
</table>
<script type="text/javascript">
    var $table = jQuery("#table"), rows = [];
    $(function () {
       $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
        });
        $("#searchByArticle").select2({width: '100%'});
         
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
            var depot = $("#searchByDepot").val();
            if(depot==0 && article == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-articles-vendus-by-quantite'])}}"});
            }
            if(article!= 0 && depot==0 && dateDebut=="" && dateFin==""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-article/' + article});
            }
            if(article == 0 && depot==0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-periode/' + dateDebut + '/' + dateFin});
            }
            if(article != 0 && dateDebut!="" && dateFin!="" && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-periode-article/' + dateDebut + '/' + dateFin + '/' + article});
            }
            if(article == 0 && dateDebut=="" && dateFin==""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot/' + depot});
            }
            if(article != 0 && dateDebut=="" && dateFin==""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot-article/' + depot + '/' + article});
            }
            if(article == 0 && dateDebut!="" && dateFin!=""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot-periode/'+ dateDebut + '/' + dateFin + '/' +depot});
            }
            if(article != 0 && dateDebut!="" && dateFin!=""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot-article-periode/'+ dateDebut + '/' + dateFin + '/' +depot + '/' + article});
            }
        });
        $("#searchByDepot").change(function (e) { 
            
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            var article = $("#searchByArticle").val();
            var depot = $("#searchByDepot").val();
            if(depot==0 && article == 0 && dateDebut=="" && dateFin==""){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-articles-vendus-by-quantite'])}}"});
            }
            if(article!= 0 && depot==0 && dateDebut=="" && dateFin==""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-article/' + article});
            }
            if(article == 0 && depot==0 && dateDebut!="" && dateFin!=""){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-periode/' + dateDebut + '/' + dateFin});
            }
            if(article != 0 && dateDebut!="" && dateFin!="" && depot==0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-periode-article/' + dateDebut + '/' + dateFin + '/' + article});
            }
            if(article == 0 && dateDebut=="" && dateFin==""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot/' + depot});
            }
            if(article != 0 && dateDebut=="" && dateFin==""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot-article/' + depot + '/' + article});
            }
            if(article == 0 && dateDebut!="" && dateFin!=""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot-periode/'+ depot + '/' + dateDebut + '/' + dateFin});
            }
            if(article != 0 && dateDebut!="" && dateFin!=""  && depot!=0){
              $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-depot-article-periode/'+ dateDebut + '/' + dateFin + '/' +depot + '/' + article});
            }
        });
        
        $("#dateDebut, #dateFin").change(function (e) {
            var dateDebut = $("#dateDebut").val();
            var dateFin = $("#dateFin").val();
            $("#searchByArticle").select2("val",0);
            $("#searchByDepot").val(0);
            if(dateDebut!='' && dateFin!=''){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-articles-vendus-by-quantite-periode/' + dateDebut + '/' + dateFin});
            }
            if(dateDebut=='' && dateFin==''){
                $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-articles-vendus-by-quantite'])}}"});
            }
        });
       
    });
    
    function imprimePdf(){
        var dateDebut = $("#dateDebut").val();
        var dateFin = $("#dateFin").val();
        var article = $("#searchByArticle").val();
        var depot = $("#searchByDepot").val();
        if(depot ==0 && article == 0 && dateDebut=="" && dateFin==""){
            window.open("../boutique/articles-vendus-by-quantite-pdf/",'_blank');
        }
        if(depot==0 && article!= 0 && dateDebut=="" && dateFin==""){
            window.open("../boutique/articles-vendus-by-quantite-article-pdf/" + article ,'_blank');  
        }
        if(depot==0 && article == 0 && dateDebut!="" && dateFin!=""){
            window.open("../boutique/articles-vendus-by-quantite-periode-pdf/" + dateDebut + '/' + dateFin ,'_blank');  
        }
        if(depot== 0 && article != 0 && dateDebut!="" && dateFin!=""){
            window.open("../boutique/articles-vendus-by-quantite-periode-article-pdf/" + dateDebut + '/' + dateFin  + '/' + article,'_blank');  
        }
        if(article == 0 && dateDebut=="" && dateFin==""  && depot!=0){
            window.open("../boutique/articles-vendus-by-quantite-depot-pdf/" + depot,'_blank');  
        }
        if(article != 0 && dateDebut=="" && dateFin==""  && depot!=0){
            window.open("../boutique/articles-vendus-by-quantite-depot-article-pdf/" + depot + '/' +article,'_blank');  
        }
        if(article == 0 && dateDebut!="" && dateFin!=""  && depot!=0){
            window.open("../boutique/articles-vendus-by-quantite-depot-periode-pdf/" + depot + '/' + dateDebut + '/' + dateFin ,'_blank');  
        }
        if(article != 0 && dateDebut!="" && dateFin!=""  && depot!=0){
            window.open("../boutique/articles-vendus-by-quantite-depot-article-periode-pdf/"+ dateDebut + '/' + dateFin + '/' + depot + '/' +article,'_blank');  
        }
    }

    function montantFormatter(id, row){
        var montants = row.prix*row.quantite;
        return '<span class="text-bold">' + $.number(montants)+ '</span>';
    }

</script>
@else
@include('layouts.partials.look_page')
@endif
@endsection