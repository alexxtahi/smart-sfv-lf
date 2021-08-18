@extends('layouts.app')
@section('content')
<script src="{{asset('assets/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/underscore-min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
<script src="{{asset('assets/js/fonction_crude.js')}}"></script>
<script src="{{asset('assets/js/jquery.number.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.datetimepicker.full.min.js')}}"></script>
<script src="{{asset('assets/plugins/Bootstrap-form-helpers/js/bootstrap-formhelpers-phone.js')}}"></script>
<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
<link href="{{asset('assets/css/bootstrap-table.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/jquery.datetimepicker.min.css')}}" rel="stylesheet">
@if(Auth::user()->role == 'Caissier')
<div class="col-md-6">
   @if($caisse_ouverte!=null) 
   <a class="btn btn-sm btn-danger pull-left" id="btnFermerCaisse"><i class="fa fa-lock"></i> Fermer la caisse</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       <a class="btn btn-sm btn-success">{{$caisse!=null ? $caisse->libelle_caisse: ""}} ouverte</a>
    @else
        <a class="btn btn-sm btn-success pull-left" id="btnOuvrirCaisse"><i class="fa fa-unlock"></i> Ouvrir la caisse</a> 
    @endif
    <br/><br/>
</div>
@if($caisse_ouverte!=null)
<div class="col-md-6">
    <p class="text-bold h3"> Total vente caisse : <span class="text-bold text-green" id="total_caisse_caissier">0</span></p>
</div>
@endif
@endif
@if(Auth::user()->role != 'Caissier')
<div class="col-md-3">
   @if($caisse_ouverte!=null) 
   <a class="btn btn-sm btn-danger pull-left" id="btnFermerCaisse"><i class="fa fa-lock"></i> Fermer la caisse</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    @else
        <a class="btn btn-sm btn-success pull-left" id="btnOuvrirCaisse"><i class="fa fa-unlock"></i> Ouvrir la caisse</a> 
    @endif
    <br/><br/>
</div>
@if($caisse_ouverte!=null) 
<div class="col-md-5">
    <p class="text-bold h3"> Total vente caisse : <span class="text-bold text-green" id="total_caisse_caissier">0</span></p>
</div>
@endif
<div class="col-md-2">
    <div class="form-group">
       <input type="text" class="form-control" id="searchByTicket" placeholder="Rechercher par N° du ticket">
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <input type="text" class="form-control" id="searchByDate" placeholder="Rechercher par date de vente">
    </div>
</div>
@endif
<table id="table" class="table table-primary table-striped box box-primary"
               data-pagination="true"
               data-search="false" 
               data-toggle="table"
               data-unique-id="id"
               data-show-toggle="false"
               data-show-columns="false">
    <thead>
        <tr>
            <th data-formatter="tiketFormatter" data-width="100px" data-align="center">Ticket</th>
            <th data-field="numero_ticket">N° Ticket</th>
            <th data-field="date_ventes">Date</th>
            <th data-field="sommeTotale" data-formatter="montantFormatter">Montant TTC</th>
            <th data-formatter="montantRemisFormatter">Montant pay&eacute; </th>
            <th data-formatter="monnaieFormatter">Monnaie r&eacute;mise </th>
            @if(Auth::user()->role == 'Caissier')
            <th data-field="id" data-formatter="listeArticleFormatter" data-width="60px" data-align="center">Panier</th>
            @endif
            @if(Auth::user()->role != 'Caissier' && $caisse->ouvert==0)
            <th data-field="id" data-formatter="listeArticleFormatter" data-width="60px" data-align="center">Panier</th>
            @endif
            @if(Auth::user()->role != 'Caissier' && $caisse->ouvert!=0)
            <th data-field="id" data-formatter="optionFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
            @endif
        </tr>
    </thead>
</table>

<!-- Modal ajout et modification -->
<div class="modal fade bs-modal-ajout" role="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width: 75%">
        <form id="formAjout" ng-controller="formAjoutCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span style="font-size: 16px;">
                        <i class="fa fa-credit-card fa-2x"></i>
                        Gestion des ventes
                    </span>
                </div>
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idVenteModifier" name="idVente" ng-hide="true" ng-model="vente.id"/>
                    <input type="text" class="hidden" id="depot_id" name="depot_id" ng-hide="true" value="{{$depot->id}}"/>
                    @if(Auth::user()->role == 'Caissier' && $caisse_ouverte!=null)
                    <input type="text" class="hidden" id="caisses_id" name="caisse_id" value="{{$caisse_ouverte->caisse_id}}">
                    @endif
                    @if(Auth::user()->role != 'Caissier' && $caisse!=null)
                    <input type="text" class="hidden" id="caisses_id" name="caisse_id" value="{{$caisse->id}}">
                    @endif
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-primary">
                                <div class="col-md-6"> 
                                    <div class="widget-user-header ">
                                        <h3 class="widget-user-username">D&eacute;p&ocirc;t : {{$depot->libelle_depot}}</h3>
                                        <h5 class="widget-user-desc">Caisse : <b>{{$caisse!=null ? $caisse->libelle_caisse: ""}}</b></h5>
                                        <h5 class="widget-user-desc">Connect&eacute;(e) : <b>{{$auth_user->full_name}}</b></h5>
                                    </div>
                                </div>
                                <div class="col-md-6"> 
                                    <h5 class="text-bold text-red"><br/>
                                        <label>
                                            <input type="checkbox" id="attente" name="attente" ng-model="vente.attente" ng-checked="vente.attente">&nbsp; Cochez cette case pour metre le client en attente
                                        </label>
                                    </h5>
                                </div>
                                <div class="col-md-6 remise_add_row"> <br/>
                                    <a class="btn btn-sm btn-warning" id="btnRemise"><i class="fa fa-lock"></i> Faire une remise</a> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div id="div_enregistrement">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Code Barre</label>
                                    <input type="text" class="form-control" id="code_barre" autofocus>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Article *</label>
                                    <select class="form-control" id="article">
                                        <option value="">-- Selcetionner l'article --</option>
                                        @foreach($articles as $article)
                                        <option data-libellearticle="{{$article->description_article}}"  value="{{$article->id_article}}"> {{$article->description_article}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Colis *</label>
                                    <select class="form-control" id="unite">
                                        <option value="">-- Colis--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix HT</label>
                                    <input type="text" class="form-control" id="prixHT" placeholder="Prix HT" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Prix TTC</label>
                                    <input type="text" class="form-control" id="prixTTC" placeholder="Prix TTC" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>En Stock</label>
                                    <input type="number" class="form-control" id="en_stock" placeholder="Qté en stock" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Qt&eacute; &agrave; vendre *</label>
                                    <input type="number" min="0" class="form-control" id="quantite" placeholder="Qté à vendre">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Montant TTC</label>
                                    <input type="text" class="form-control" id="montantTC" placeholder="Montant TTC">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Remise</label>
                                    <input type="number" min="0" class="form-control" id="remise_sur_ligne" value="0" placeholder="Faire une remise">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group"><br/>
                                    <button type="button" class="btn btn-success btn-sm  add-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-xs delete-row">Supprimer ligne</button><br/><br/>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tableAddRowArticle" class="table table-success table-striped box box-success"
                                       data-toggle="table"
                                       data-id-field="id"
                                       data-unique-id="id"
                                       data-click-to-select="true"
                                       data-show-footer="false">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th data-field="id">ID</th>
                                            <th data-field="code_barre">Code barre</th>
                                            <th data-field="libelle_article">Article</th>
                                            <th data-field="libelle_unite">Colis</th>
                                            <th data-field="prix_ht">Prix HT</th>
                                            <th data-field="prix_ttc">Prix TTC</th>
                                            <th data-field="quantite">Qt&eacute;</th>
                                            <th data-field="montant_ttc">Montant TTC</th>
                                            <th data-field="montant_remise_ligne">Remise</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="div_update">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="button" id="btnModalAjoutArticle" class="btn btn-primary btn-xs pull-right"><i class="fa fa-plus">Ajouter un article</i></button>
                                </div>
                            </div> 
                        </div><br/>
                        <table id="tableArticle" class="table table-success table-striped box box-success"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.description_article">Article</th>
                                    <th data-field="unite.libelle_unite">Colis</th>
                                    <th data-formatter="montantHTFormatter">Prix HT</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix TTC</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant TTC </th>
                                    <th data-field="remise_sur_ligne" data-formatter="montantFormatter">Remise </th>
                                    @if(Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
                                    <th data-field="id" data-formatter="optionAArticleFormatter" data-width="100px" data-align="center"><i class="fa fa-wrench"></i></th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                        <div class="row"> 
                            <div class="col-md-6"><br/>
                                <ul class="nav nav-stacked" style="font-size: 15px;">
                                    <li><a class="text-bold" >Montant HT <span id="montantTHT_add" class="pull-right text-bold"></span></a></li>
                                    <li><a class="text-bold" >Montant TVA <span id="montantTTVA_add" class="pull-right text-bold"></span></a></li>
                                    <li><a class="text-bold" >Montant Remise <span id="montantRemise_add" class="pull-right text-bold"></span></a></li>
                                    <li><a class="text-bold" >Montant TTC<span id="montantTTTC_add" class="pull-right text-bold  text-red"></span></a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Montant &agrave; payer *</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">
                                                 <i class="fa fa-money"></i>
                                             </div>
                                             <input type="text" pattern="[0-9]*" class="form-control" min="0" id="montant_a_payer_add" name="montant_a_payer_add" placeholder="Montant à payer" readonly>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Montant pay&eacute;</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">
                                                 <i class="fa fa-money"></i>
                                             </div>
                                             <input type="text" pattern="[0-9]*" class="form-control" min="0" id="montant_payer_add" name="montant_payer_add" placeholder="Montant payé">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6">
                                     <p class="text-bold text-red">Reste d&ucirc; comme monnaie : &nbsp;&nbsp;&nbsp;<span class="text-bold text-red montant_restant_add"></span></p>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="moyen_reglement_id">Moyen de payement *</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">
                                                 <i class="fa fa-cog"></i>
                                             </div>
                                             <select name="moyen_reglement_id_add" id="moyen_reglement_id_add" class="form-control" required>
                                                 @foreach($moyenReglements as $moyenReglement)
                                                 <option  {{$moyenReglement->id==1 ? "selected"  : ""}} value="{{$moyenReglement->id}}">{{$moyenReglement->libelle_moyen_reglement}}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row" id="row_regle"> 
                        <div class="col-md-6"><br/>
                            <ul class="nav nav-stacked" style="font-size: 15px;">
                                <li><a class="text-bold">Montant HT <span class="pull-right text-bold montantHT"></span></a></li>
                                <li><a class="text-bold">Montant TVA <span class="pull-right text-bold montantTVA"></span></a></li>
                                <li><a class="text-bold">Montant Remise <span class="pull-right text-bold remiseTTC"></span></a></li>
                                <li><a class="text-bold">Montant TTC<span class="pull-right text-bold text-red montantTTC"></span></a></li>
                            </ul>
                        </div> 
                         <div class="col-md-6">
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Montant &agrave; payer *</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">
                                                 <i class="fa fa-money"></i>
                                             </div>
                                             <input type="text" pattern="[0-9]*" class="form-control" min="0" id="montant_a_payer" name="montant_a_payer" placeholder="Montant à payer" readonly>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label>Montant pay&eacute;</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">
                                                 <i class="fa fa-money"></i>
                                             </div>
                                             <input type="text" pattern="[0-9]*" class="form-control" min="0" id="montant_payer" name="montant_payer" placeholder="Montant payé">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             <div class="row">
                                 <div class="col-md-6">
                                     <p class="text-bold text-red">Reste d&ucirc; comme monnaie : &nbsp;&nbsp;&nbsp;<span class="text-bold text-red montant_restant"></span></p>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <label for="moyen_reglement_id">Moyen de payement *</label>
                                         <div class="input-group">
                                             <div class="input-group-addon">
                                                 <i class="fa fa-cog"></i>
                                             </div>
                                             <select name="moyen_reglement_id" id="moyen_reglement_id" class="form-control" required>
                                                 @foreach($moyenReglements as $moyenReglement)
                                                 <option  {{$moyenReglement->id==1 ? "selected"  : ""}} value="{{$moyenReglement->id}}">{{$moyenReglement->libelle_moyen_reglement}}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="sendButton" class="btn btn-primary"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal add article -->
<div class="modal fade bs-modal-add-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
        <form id="formAjoutArticle" ng-controller="formAjoutArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Ajout d'un article
                </div>
                @csrf
                <div class="modal-body ">
                   <input type="text" class="hidden" id="idArticleModifier"  ng-model="article.id"/>
                   <input type="text" class="hidden" id="vente"  name="vente_id"/>
                   <div class="row">
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Code Barre</label>
                               <input type="text" class="form-control" id="code_barre_add" autofocus>
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="form-group">
                               <label>Article *</label>
                               <select name="article_id" class="form-control" id="article_add" required ng-model="article.article_id">
                                   <option value="">-- Selcetionner l'article --</option>
                                   @foreach($articles as $article)
                                   <option value="{{$article->id_article}}"> {{$article->description_article}}</option>
                                   @endforeach
                               </select>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Colis *</label>
                               <select name="unite_id" class="form-control" id="unite_add" required>
                                   <option value="">-- Colis--</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Prix HT</label>
                               <input type="text" class="form-control" id="prixHT_add" placeholder="Prix HT" readonly>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Prix TTC</label>
                               <input type="text" class="form-control" name="prix" ng-model="article.prix" id="prixTTC_add" placeholder="Prix TTC" readonly>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>En Stock</label>
                               <input type="number" class="form-control" id="en_stock_add" placeholder="Qté en stock" readonly>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Qt&eacute; &agrave; vendre *</label>
                               <input type="number" min="0" name="quantite" ng-model="article.quantite" class="form-control" id="quantite_add" placeholder="Qté à vendre" required>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Montant TTC</label>
                               <input type="text" class="form-control" id="montantTC_add" placeholder="Montant TTC" readonly>
                           </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                               <label>Remise</label>
                               <input type="text" pattern="[0-9]*" class="form-control" name="remise_sur_ligne" ng-model="article.remise_sur_ligne" id="remise_sur_ligne_add" value="0" placeholder="Faire une remise">
                           </div>
                       </div>
                       <div class="col-md-2"> <br/>
                           <a class="btn btn-sm btn-warning" id="btnRemiseAdd"><i class="fa fa-lock"></i> Faire une remise</a> 
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-send"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span>Valider</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppresion article-->
<div class="modal fade bs-modal-supprimer-article" category="dialog" data-backdrop="static">
    <div class="modal-dialog ">
        <form id="formSupprimerArticle" ng-controller="formSupprimerArticleCtrl" action="#">
            <div class="modal-content">
                <div class="modal-header bg-red">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        Confimation de la suppression
                </div>
                @csrf
                <div class="modal-body ">
                    <input type="text" class="hidden" id="idArticleSupprimer"  ng-model="article.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer l'article <br/><b>@{{article.article.description_article}}</b></div>
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

<!-- Modal suppresion-->
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
                    <input type="text" class="hidden" id="idVenteSupprimer"  ng-model="vente.id"/>
                    <div class="clearfix">
                        <div class="text-center question"><i class="fa fa-question-circle fa-2x"></i> Etes vous certains de vouloir supprimer cette vente<br/><b>@{{vente.numero_ticket}}</b></div>
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

<!-- Modal ouverture caisse-->
<div class="modal fade bs-modal-ouverture-caisse" role="dialog" data-backdrop="static">
    <form id="formOuvertureCaisse" class="form-horizontal" action="#" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-green-active">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="circle">
                        Ouverture de caisse
                    </span>
                </div>
                @csrf
                <div class="modal-body">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="caisse_id" class="col-sm-4 control-label">Caisse *</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="caisse_id" id="caisse_id" required="required">
                                    <option value="">-- Chosir la caisse --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Montant &agrave; l'ouverture *</label>
                            <div class="col-sm-8">
                                <input type="number" min="0" name="montant_ouverture" class="form-control"  placeholder="Montant à l'ouverture" required="required"/>
                            </div>
                        </div>
                        <!--<span class="pull-right label label-danger">Toutes vos op&eacute;rations financi&egrave;res seront enregistr&eacute;es dans cette caisse.</span>-->
                    </div>                            
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span> Valider</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal fermeture caisse-->
<div class="modal fade bs-modal-fermeture-caisse" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
        <form id="formFermetureCaisse" action="#" method="post">
                <div class="modal-content">
                    <div class="modal-header bg-red">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <span class="circle">
                            Fermeture de caisse
                        </span>
                    </div>
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-3 border-right">
                                        <div class="description-block">
                                            <span class="description-text text-black">Montant a l'ouverture</span>
                                            <h5 class="description-header"><p class="text-black" id="montant_ouverture"></p></h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-right">
                                        <div class="description-block">
                                            <span class="description-text text-green">Total entree</span>
                                            <h5 class="description-header"><p class="text-green" id="total_entree"></p></h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 border-right">
                                        <div class="description-block">
                                            <span class="description-text text-red">Total sortie</span>
                                            <h5 class="description-header"><p class="text-red" id="total_sortie"></p></h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="description-block">
                                            <span class="description-text text-orange">Solde</span>
                                            <h5 class="description-header"><p class="text-orange" id="solde_caisse"></p></h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="h3">Montant &agrave; la fermeture : <span class="h3" id="solde_fermeture_aff"></span></label>
                                            <input type="text" class="hidden" name="solde_fermeture" id="solde_fermeture"/>
                                            <input type="text" class="hidden" name="caisses_fermeture" id="caisses_fermeture"/>
                                        </div>
                                    </div>
                                </div>
                                @if($auth_user->role!='Caissier')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="motif_non_conformite" placeholder="Motif de non confirmité de la caisse"/>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <label class="text-center text-red">Assurez-vous du montant r&eacute;el de votre caisse. Contacter l'administrateur en cas d'anomalie.</label><br/>
                            </div> 
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Billet *</label>
                                            <select class="form-control" id="billet">
                                                <option value="">-- Selcetionner un element --</option>
                                                <option value="10000"> 10000</option>
                                                <option value="5000"> 5000</option>
                                                <option value="2000"> 2000</option>
                                                <option value="1000"> 1000</option>
                                                <option value="500"> 500</option>
                                                <option value="250"> 250</option>
                                                <option value="250"> 250</option>
                                                <option value="200"> 200</option>
                                                <option value="100"> 100</option>
                                                  <option value="50"> 50</option>
                                                  <option value="25"> 25</option>
                                                  <option value="10"> 10</option>
                                                <option value="5"> 5</option>
                                                <option value="0"> 0</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Quantit&eacute; </label>
                                            <input type="number" min="0" class="form-control" id="quantite_billet" placeholder="Quantité">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Montant</label>
                                            <input type="text" class="form-control" id="montant_billet" placeholder="Montant" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group"><br/>
                                            <button type="button" class="btn btn-success btn-sm  add-billetage-row pull-left"><i class="fa fa-plus">Ajouter</i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger btn-xs delete-billetage-row">Supprimer ligne</button><br/><br/>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table id="tableBilletage" class="table table-success table-striped box box-warning"
                                               data-toggle="table"
                                               data-id-field="id"
                                               data-unique-id="id"
                                               data-click-to-select="true"
                                               data-show-footer="false">
                                            <thead>
                                                <tr>
                                                    <th data-field="state" data-checkbox="true"></th>
                                                    <th data-field="id">ID</th>
                                                    <th data-field="billet">Billet</th>
                                                    <th data-field="quantite_billet">Quantit&eacute;</th>
                                                    <th data-field="montant_billet">Montant</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-danger"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span> Fermer</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

<!-- Modal ouverture remise-->
<div class="modal fade bs-modal-unlok-input-remise-prix" role="dialog" data-backdrop="static">
    <form id="formUnlokInputRemisePrix" action="#" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <span class="circle">
                        Connexion pour la v&eacute;rification de vos droits d'acc&egrave;ss
                    </span>
                </div>
                @csrf
                <input type="text" class="hidden" id="depot_gerant" name="depot_gerant" ng-hide="true" value="{{$depot->id}}"/>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>E-mail *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-at"></i>
                                    </div>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse mail" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Mot de passe *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
                                </div>
                            </div>
                        </div>
                    </div>                            
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-warning"><span class="overlay loader-overlay"> <i class="fa fa-refresh fa-spin"></i> </span> Valider</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal panier -->
<div class="modal fade bs-modal-panier" id="panierArticle" ng-controller="panierArticleCtrl" category="dialog" data-backdrop="static">
    <div class="modal-dialog" style="width:65%">
            <div class="modal-content">
                <div class="modal-header bg-yellow">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    Panier du ticket <b>@{{vente.numero_ticket}}</b>
                </div>
                @csrf
                <div class="modal-body ">
                 <table id="tablePanierArticle" class="table table-warning table-striped box box-warning"
                               data-pagination="true"
                               data-search="false"
                               data-toggle="table"
                               data-unique-id="id"
                               data-show-toggle="false">
                            <thead>
                                <tr>
                                    <th data-field="article.description_article">Article</th>
                                    <th data-field="unite.libelle_unite">Colis</th>
                                    <th data-formatter="montantHTFormatter">Prix HT</th>
                                    <th data-field="prix" data-formatter="montantFormatter">Prix TTC</th>
                                    <th data-field="quantite" data-align="center">Quantit&eacute; </th>
                                    <th data-formatter="montantTttcLigneFormatter">Montant TTC </th>
                                    <th data-field="remise_sur_ligne" data-formatter="montantFormatter">Remise </th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
    </div>
</div>

<form>
    <input type="text" class="hidden" id="user_role" value="{{$auth_user->role}}"/>
</form>

<script type="text/javascript">
    var ajout = false;
    var ajoutArticle = false;
    var montantHT = 0;
    var montantTTC = 0;
    var remiseTTC = 0;
    var $table = jQuery("#table"), rows = [],$tableBilletage = jQuery("#tableBilletage"), $tablePanierArticle = jQuery("#tablePanierArticle"),$tableListeArticle = jQuery("#tableListeArticle"), $tableArticle = jQuery("#tableArticle"), rowsArticle = [], $tableAddRowArticle = jQuery("#tableAddRowArticle");
    var monPanier = [];
    var panierBillet = [];
    var idTablle =  0;
    var idTablleBillet =  0;
    var user_role = $("#user_role").val();
    var caisse_id = $("#caisses_id").val()
    
    appSmarty.controller('formAjoutCtrl', function ($scope) {
        $scope.populateForm = function (vente) {
        $scope.vente = vente;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.vente = {};
        };
    }); 
    
    appSmarty.controller('formAjoutArticleCtrl', function ($scope) {
        $scope.populateArticleForm = function (article) {
        $scope.article = article;
        };
        $scope.initForm = function () {
        ajout = true;
        $scope.article = {};
        };
    }); 
    
    appSmarty.controller('formSupprimerArticleCtrl', function ($scope) {
        $scope.populateSupArticleForm = function (article) {
        $scope.article = article;
        };
        $scope.initForm = function () {
        $scope.article = {};
        };
    });
    
    appSmarty.controller('formSupprimerCtrl', function ($scope) {
        $scope.populateForm = function (vente) {
        $scope.vente = vente;
        };
        $scope.initForm = function () {
        $scope.vente = {};
        };
    });

    appSmarty.controller('panierArticleCtrl', function ($scope) {
        $scope.populateFormPanier = function (vente) {
        $scope.vente = vente;
        };
    });
    
    $(function () {
        $table.on('load-success.bs.table', function (e, data) {
            rows = data.rows; 
            $("#total_caisse_caissier").html($.number(data.totalCaisse));
        });
       
        if(user_role=="Caissier"){
            $table.bootstrapTable('refreshOptions', {url: "{{url('boutique', ['action' => 'liste-ventes-caisse'])}}"});
        }else{
            $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-caisse/' + caisse_id});
        }
        
        $tableArticle.on('load-success.bs.table', function (e, data) {
            rowsArticle = data.rows;
            $("#montantTHT_add").html($.number(data.montantTHT_add));
            $("#montantTTVA_add").html($.number(data.montantTTTC_add-data.montantTHT_add));
            $("#montantRemise_add").html($.number(data.montantRemise_add));
            $("#montantTTTC_add").html($.number(data.montantTTTC_add-data.montantRemise_add));
            $("#montant_a_payer_add").val(data.montantTTTC_add-data.montantRemise_add);
        });
        $("#article").select2({width: '100%'});
        $(".remise_add_row").show();
        $("#div_enregistrement").show();
        $("#div_update").hide();
        $("#row_regle").hide();
        $(".delete-row").hide();
        $(".delete-billetage-row").hide();
        $('#searchByDate').datetimepicker({
            timepicker: false,
            formatDate: 'd-m-Y',
            format: 'd-m-Y',
            local : 'fr'
        });
        $("#quantite_billet").change(function (e) {
           var quantite_billet = $("#quantite_billet").val();
           var billet = $("#billet").val();
           if(billet!="" && quantite_billet!=""){
               var qte = parseInt(billet)*parseInt(quantite_billet);
               $("#montant_billet").val(qte);
           }else{
               $("#montant_billet").val("");
           }
        });
        $("#quantite_billet").keyup(function (e) {
           var quantite_billet = $("#quantite_billet").val();
           var billet = $("#billet").val();
           if(billet!="" && quantite_billet!=""){
               var qte = parseInt(billet)*parseInt(quantite_billet);
               $("#montant_billet").val(qte);
           }else{
               $("#montant_billet").val("");
           }
        });
        
       
        $("#searchByTicket").keyup(function (e) {
            var numero_ticke = $("#searchByTicket").val();
            $("#searchByDate").val("");
            if(numero_ticke == ""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-caisse/' + caisse_id });
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-numero-ticket/' + caisse_id + '/' + numero_ticke});
            }
        });
        $("#searchByDate").change(function (e) {
            var date = $("#searchByDate").val();
            $("#searchByTicket").val("");
            if(date == ""){
                $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-ventes-by-caisse/' + caisse_id});
            }else{
               $table.bootstrapTable('refreshOptions', {url: '../boutique/liste-vente-by-caisse-date-vente/' + caisse_id + '/' + date});
            }
        });
       
        
        $("#btnOuvrirCaisse").on("click", function () {
            if(user_role=="Caissier"){
                var depot = $('#depot_id').val();
                $.getJSON("../parametre/liste-caisses-fermees-by-depot/" + depot, function (reponse) {
                    $('#caisse_id').html("<option value=''>-- Selectionner la caisse --</option>");
                    $.each(reponse.rows, function (index, caisse) { 
                        $('#caisse_id').append('<option value=' + caisse.id + '>' + caisse.libelle_caisse + '</option>')
                    });
                });
            }
            if(user_role!="Caissier"){
                $.getJSON("../parametre/find-caisse-by-id/" + caisse_id, function (reponse) {
                    $('#caisse_id').html("<option value=''>-- Selectionner la caisse --</option>");
                    $.each(reponse.rows, function (index, caisse) { 
                        $('#caisse_id').append('<option selected value=' + caisse.id + '>' + caisse.libelle_caisse + '</option>')
                    });
                });
            }
           $(".bs-modal-ouverture-caisse").modal("show");
        });
        
        $("#btnFermerCaisse").on("click", function () {
            if(user_role=="Caissier"){
                $.getJSON("../boutique/get-one-caisse-ouverte-by-caisse/" + caisse_id, function (reponse) {
                    $.each(reponse.rows, function (index, caisse_ouverte) { 
                        var solde_fermeture = caisse_ouverte.sommeTotale+caisse_ouverte.montant_ouverture + caisse_ouverte.entree - caisse_ouverte.sortie;
                        $("#montant_ouverture").html($.number(caisse_ouverte.montant_ouverture));
                        $("#total_entree").html($.number(caisse_ouverte.sommeTotale+caisse_ouverte.entree));
                        $("#total_sortie").html($.number(caisse_ouverte.sortie));
                        $("#solde_caisse").html($.number(solde_fermeture));
                        $("#solde_fermeture_aff").html($.number(solde_fermeture));
                        $("#solde_fermeture").val(solde_fermeture); 
                    });
                }); 
            }
            if(user_role!="Caissier"){
                $.getJSON("../boutique/get-one-caisse-ouverte-by-caisse/" + caisse_id, function (reponse) {
                    $.each(reponse.rows, function (index, caisse_ouverte) { 
                         var solde_fermeture = caisse_ouverte.sommeTotale+caisse_ouverte.montant_ouverture + caisse_ouverte.entree - caisse_ouverte.sortie;
                        $("#montant_ouverture").html($.number(caisse_ouverte.montant_ouverture));
                        $("#total_entree").html($.number(caisse_ouverte.sommeTotale+caisse_ouverte.entree));
                        $("#total_sortie").html($.number(caisse_ouverte.sortie));
                        $("#solde_caisse").html($.number(solde_fermeture));
                        $("#solde_fermeture_aff").html($.number(solde_fermeture));
                        $("#solde_fermeture").val(solde_fermeture); 
                    });
                }); 
            }
           $(".bs-modal-fermeture-caisse").modal("show");
        });
        $("#btnRemise, #btnRemiseAdd").on("click", function () {
            document.forms["formUnlokInputRemisePrix"].reset();
           $(".bs-modal-unlok-input-remise-prix").modal("show");
        });
        $("#montantTC").prop('readonly',true);
        $("#remise_sur_ligne").prop('readonly',true);
        $("#btnModalAjoutArticle").on("click", function () {
            ajoutArticle = true;
            document.getElementById("code_barre_add").focus();
            var vente = $("#idVenteModifier").val();
            var depot = $("#depot_id").val();
            $("#article").val("");
            $("#remise_sur_ligne_add").prop('readonly',true);
            document.forms["formAjoutArticle"].reset();
            $("#vente").val(vente);
            $(".bs-modal-add-article").modal("show");
        });
       
        $("#btnModalAjout").on("click", function () {
            ajout = true;
            document.getElementById("code_barre").focus();
            $("#row_regle").hide();
            $(".remise_add_row").show();
            $("#div_enregistrement").show();
            $("#div_update").hide();
            $("#attente").attr("checked", false);
            $("#remise_sur_ligne").prop('readonly',true);
            $(".delete-row").hide();
            $("#article").select2("val","");
            $tableAddRowArticle.bootstrapTable('removeAll');
            monPanier = [];
            idTablle =  0;
            montantHT = 0;
            montantTTC = 0;
            remiseTTC = 0;
            $(".montantHT").html("<b>"+ $.number(montantHT)+"</b>");
            $(".montantTVA").html("<b>" + $.number(montantTTC-montantHT) + "</b>");
            $(".remiseTTC").html("<b>" + $.number(remiseTTC) +"</b>");
            $(".montantTTC").html("<b>" + $.number(montantTTC-remiseTTC) +"</b>");
            $("#montant_a_payer").val(0);
            $("#montant_payer").val("");
            $(".montant_restant").html("");
        });

        $('#code_barre').keyup(function(e){
            if(e.which == '10' || e.which == '13') {
            $("#remise_sur_ligne").prop('readonly',true);
            $("#prixHT").val("");
            $("#prixTTC").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montantTC").val("");
            var code_barre = $('#code_barre').val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function (reponse) {
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, retour) { 
                        $("#article").select2("val",retour.article.id);
                        $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + retour.article.id , function (reponse) {
                            $('#unite').html("<option value=''>-- Colis --</option>");
                            $.each(reponse.rows, function (index, colis) { 
                                $('#unite').append('<option data-libelleunite= "' + colis.unite.libelle_unite + '" value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                            });
                        })
                    });
                }else{
                    $("#article").select2("val","");
                }
            })
            e.preventDefault();
            e.stopPropagation();
            }
        });
        $('#code_barre_add').keyup(function(){
            $("#prixHT_add").val("");
            $("#prixTTC_add").val("");
            $("#en_stock_add").val("");
            $("#quantite_add").val("");
            $("#montantTC_add").val("");
            var code_barre = $('#code_barre_add').val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../boutique/liste-article-by-unite-in-depot-by-code/" + code_barre, function (reponse) {
                if(reponse.total>0){
                    $.each(reponse.rows, function (index, retour) { 
                         $("#article_add").val(retour.article.id);
                        $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + retour.article.id , function (reponse) {
                            $('#unite_add').html("<option value=''>-- Colis --</option>");
                            $.each(reponse.rows, function (index, colis) { 
                                $('#unite_add').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                            });
                        })
                    });
                }else{
                   $("#article_add").select2("val","");
                }
            })
        });
        $('#article').change(function(){
            $("#prixHT").val("");
            $("#prixTTC").val("");
            $("#en_stock").val("");
            $("#quantite").val("");
            $("#montantTC").val("");
            $("#code_barre").val("");
            $("#remise_sur_ligne").prop('readonly',true);
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) { 
                    $("#code_barre").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + article_id , function (reponse) {
                $('#unite').html("<option value=''>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) { 
                    $('#unite').append('<option data-libelleunite= "' + colis.unite.libelle_unite + '" value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
            })
        });
        $('#article_add').change(function(){
            $("#prixHT_add").val("");
            $("#prixTTC_add").val("");
            $("#en_stock_add").val("");
            $("#quantite_add").val("");
            $("#montantTC_add").val("");
            $("#code_barre_add").val("");
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_id").val();
            $.getJSON("../parametre/find-article/" + article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) { 
                    $("#code_barre_add").val(articles_trouver.code_barre);
                });
            })
            $.getJSON("../boutique/liste-unites-by-depot-article/" + depot_id + "/" + article_id , function (reponse) {
                $('#unite_add').html("<option value=''>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) { 
                    $('#unite_add').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
            })
        });
        $('#unite').change(function(){
            $("#quantite").val("");
            var article_id = $("#article").val();
            var depot_id = $("#depot_id").val();
            var unite_id = $("#unite").val();
            $.getJSON("../boutique/find-article-in-depot-by-unite-caisse/" + article_id + "/" + depot_id + "/" +  unite_id, function (reponse) {
                $.each(reponse.rows, function (index, article) { 
                    $("#prixTTC").val(article.prix_ventes);
                    //Calcul du prix HT
                    var tva = 0;
                   if(article.article.param_tva_id!=null){
                       $.getJSON("../parametre/find-param-tva/" + article.article.param_tva_id, function (reponse) {
                            $.each(reponse.rows, function (index, tvas_infos) { 
                                tva = tvas_infos.montant_tva;
                                var prix_ht_article = (article.prix_ventes/(tva + 1));
                                var prix = Math.round(prix_ht_article);
                                $("#prixHT").val(prix);
                            });
                        })
                   }else{
                       $("#prixHT").val(article.prix_ventes);
                   }
                    if(article.article.stockable==0){
                          $("#en_stock").val(1000);
                    }else{
                          $("#en_stock").val(article.quantite_disponible);
                    }
                });
            })
        });
        $('#unite_add').change(function(){
            $("#quantite_add").val("");
            var article_id = $("#article_add").val();
            var depot_id = $("#depot_id").val();
            var unite_id = $("#unite_add").val();
            $.getJSON("../boutique/find-article-in-depot-by-unite-caisse/" + article_id + "/" + depot_id + "/" +  unite_id, function (reponse) {
                $.each(reponse.rows, function (index, article) { 
                    if(article.article.stockable==0){
                          $("#en_stock_add").val(1000);
                    }else{
                          $("#en_stock_add").val(article.quantite_disponible);
                    }
                    $("#prixTTC_add").val(article.prix_ventes);
                    //Calcul du prix HT
                    var tva = 0;
                   if(article.article.param_tva_id!=null){
                       $.getJSON("../parametre/find-param-tva/" + article.article.param_tva_id, function (reponse) {
                            $.each(reponse.rows, function (index, tvas_infos) { 
                                tva = tvas_infos.montant_tva;
                                var prix_ht_article = (article.prix_ventes/(tva + 1));
                                var prix = Math.round(prix_ht_article);
                                $("#prixHT_add").val(prix);
                            });
                        })
                   }else{
                       $("#prixHT_add").val(article.prix_ventes);
                   }
                
                });
            })
        });
        
        $("#quantite").change(function (e) { 
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
          $("#montantTC").val(quantite*prix);
        });
        $("#quantite").keyup(function (e) { 
          var quantite = $("#quantite").val();
          var prix = $("#prixTTC").val();
          $("#montantTC").val(quantite*prix);
        });
        $("#quantite_add").change(function (e) { 
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          $("#montantTC_add").val(quantite*prix);
        });
        $("#quantite_add").keyup(function (e) { 
          var quantite = $("#quantite_add").val();
          var prix = $("#prixTTC_add").val();
          $("#montantTC_add").val(quantite*prix);
        });
        
        //Add row on table
        $(".add-row").click(function () {
            if($("#article").val() != '' && $("#quantite").val() != '' && $("#unite").val() != '' && $("#quantite").val()!=0) {
                var code_barre = $("#code_barre").val();
                var libelle_article = $("#article").children(":selected").data("libellearticle");
                var libelle_unite = $("#unite").children(":selected").data("libelleunite");
                var articleId = $("#article").val();
                var uniteId = $("#unite").val();
                var quantite = $("#quantite").val();
                var stock = $("#en_stock").val();
                var prixTTC = $("#prixTTC").val();
                var prixHT = $("#prixHT").val();
                var remise_sur_ligne = $("#remise_sur_ligne").val()!=0?$("#remise_sur_ligne").val():0;
                
                if(parseInt(quantite) > parseInt(stock)){
                    $.gritter.add({
                        title: "SMART-SFV",
                        text: "La quantité à vendre ne doit pas depasser la quantité disponible en stock",
                        sticky: false,
                        image: basePath + "/assets/img/gritter/confirm.png",
                    });
                    $("#quantite").val("");
                    return;
                }else{
                    //Vérification Si la ligne existe déja dans le tableau
                    var articleTrouver = _.findWhere(monPanier, {articles: articleId, unites:uniteId})
                    if(articleTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = articleTrouver.quantites;
                        idElementLigne = articleTrouver.id;
                       
                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite);
                        if(parseInt(sommeDeuxQtes)> parseInt(stock)){
                            $.gritter.add({
                                title: "SMART-SFV",
                                text: "Cet article existe dans votre panier, de plus la quantité de cette nouvelle ligne additionnée à celle de la ligne existante depasse celle disponible en stock",
                                sticky: false,
                                image: basePath + "/assets/img/gritter/confirm.png",
                            });
                            $("#quantite").val("");
                            return;
                        }else{
                            //MAJ de la ligne
                            montantHT = montantHT - (oldQte*prixHT);
                            montantTTC = parseInt(montantTTC) - parseInt(oldQte*prixTTC);
                            remiseTTC = parseInt(remiseTTC) - articleTrouver.remises;
                            $tableAddRowArticle.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite : sommeDeuxQtes,
                                    montant_ttc: $.number(prixTTC*sommeDeuxQtes),
                                    montant_remise_ligne: $.number(remise_sur_ligne)
                                }
                            });
                            articleTrouver.quantites = sommeDeuxQtes;
                            articleTrouver.remises = remise_sur_ligne;
                            
                            montantHT = montantHT + (sommeDeuxQtes*prixHT);
                            montantTTC = parseInt(montantTTC) + parseInt(sommeDeuxQtes*prixTTC);
                            remiseTTC = parseInt(remiseTTC) + parseInt(remise_sur_ligne);
                            $('#unite').html("<option value=''>-- Colis --</option>");
                            $("#quantite").val("");
                            $("#en_stock").val("");
                            $("#prixTTC").val("");
                            $("#prixHT").val("");
                            $("#montantTC").val("");
                            $("#remise_sur_ligne").val(0);
                            $("#code_barre").val("");
                             $("#article").select2("val","");
                            $("#remise_sur_ligne").prop('readonly',true);
                            $(".montantHT").html("<b>"+ $.number(montantHT)+"</b>");
                            $(".montantTVA").html("<b>" + $.number(montantTTC - montantHT) + "</b>");
                            $(".remiseTTC").html("<b>" + $.number(remiseTTC) +"</b>");
                            $(".montantTTC").html("<b>" + $.number(montantTTC-remiseTTC) +"</b>");
                            $("#montant_a_payer").val(montantTTC-remiseTTC);
                            $("#montant_payer").val("");
                            $(".montant_restant").html("");
                            return;
                        }
                    }
                    idTablle++; 
                    $tableAddRowArticle.bootstrapTable('insertRow',{
                        index: idTablle,
                        row: {
                          id: idTablle,
                          code_barre: code_barre,
                          libelle_article: libelle_article,
                          libelle_unite: libelle_unite,
                          prix_ht: $.number(prixHT),
                          prix_ttc: $.number(prixTTC),
                          quantite: quantite,
                          article: articleId,
                          unite: uniteId,
                          montant_ttc: $.number(quantite*prixTTC),
                          montant_remise_ligne: $.number(remise_sur_ligne)
                        }
                    })
                    montantHT = montantHT + (quantite*prixHT);
                    montantTTC = parseInt(montantTTC) + parseInt(quantite*prixTTC);
                    remiseTTC = parseInt(remiseTTC) + parseInt(remise_sur_ligne);
                    //Creation de l'article dans le tableau virtuel (panier)
                    var DataArticle = {'id':idTablle, 'articles':articleId, 'unites':uniteId, 'quantites':quantite,'prix':prixTTC,'prix_ht':prixHT,'remises':remise_sur_ligne};
                    monPanier.push(DataArticle);
                    $('#unite').html("<option value=''>-- Colis --</option>");
                    $("#quantite").val("");
                    $("#en_stock").val("");
                    $("#prixTTC").val("");
                    $("#prixHT").val("");
                    $("#montantTC").val("");
                    $("#remise_sur_ligne").val(0);
                    $("#code_barre").val("");
                    $("#article").select2("val","");
                    $("#remise_sur_ligne").prop('readonly',true);
                    $(".montantHT").html("<b>"+ $.number(montantHT)+"</b>");
                    $(".montantTVA").html("<b>" + $.number(montantTTC - montantHT) + "</b>");
                    $(".remiseTTC").html("<b>" + $.number(remiseTTC) +"</b>");
                    $(".montantTTC").html("<b>" + $.number(montantTTC-remiseTTC) +"</b>");
                    $("#montant_a_payer").val(montantTTC-remiseTTC);
                    $("#montant_payer").val("");
                    $(".montant_restant").html("");
                    if(idTablle>0){
                        $("#row_regle").show();
                        $(".delete-row").show();
                    }else{
                        $("#row_regle").hide();
                        $(".delete-row").hide();
                    }
                }
            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs article, colis et quantité ne doivent pas être vides et la quantité minimum à vendre doit être 1.",
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                return;
            }
        })
        // Find and remove selected table rows  
        $(".delete-row").click(function () {
           var selecteds = $tableAddRowArticle.bootstrapTable('getSelections');
           var ids = $.map($tableAddRowArticle.bootstrapTable('getSelections'), function (row) {
                        return row.id
                    })
                $tableAddRowArticle.bootstrapTable('remove', {
                    field: 'id',
                    values: ids 
                })
              
                $.each(selecteds, function (index, value) { 
                    var articleTrouver = _.findWhere(monPanier, {id: value.id})
                    montantHT = parseInt(montantHT) - (articleTrouver.quantites*articleTrouver.prix_ht);
                    montantTTC = parseInt(montantTTC) - parseInt(articleTrouver.quantites*articleTrouver.prix);
                    remiseTTC = parseInt(remiseTTC) - articleTrouver.remises;
                    monPanier = _.reject(monPanier, function (article) {
                        return article.id == value.id;
                    });
                });
              
                    $(".montantHT").html("<b>"+ $.number(montantHT)+"</b>");
                    $(".montantTVA").html("<b>" + $.number(montantTTC-montantHT) + "</b>");
                    $(".remiseTTC").html("<b>" + $.number(remiseTTC) +"</b>");
                    $(".montantTTC").html("<b>" + $.number(montantTTC-remiseTTC) +"</b>");
                    $("#montant_a_payer").val(montantTTC-remiseTTC);
                    $("#montant_payer").val("");
                    $(".montant_restant").html("");
                if(monPanier.length==0){
                    $("#row_regle").hide();
                    $(".delete-row").hide();
                    montantHT = 0;
                    montantTTC = 0;
                    remiseTTC = 0;
                    $(".montantHT").html("<b>"+ $.number(montantHT)+"</b>");
                    $(".montantTVA").html("<b>" + $.number(montantTTC-montantHT) + "</b>");
                    $(".remiseTTC").html("<b>" + $.number(remiseTTC) +"</b>");
                    $(".montantTTC").html("<b>" + $.number(montantTTC-remiseTTC) +"</b>");
                    $("#montant_a_payer").val(0);
                    $("#montant_payer").val("");
                    $(".montant_restant").html("");
                    idTablle = 0;
                }
        });
        
        
        //Add billet row on table
        $(".add-billetage-row").click(function () {
            if($("#billet").val() != '' && $("#quantite_billet").val() != '' && $("#quantite_billet").val()!=0) {
                var billet = $("#billet").val();
                var quantite_billet = $("#quantite_billet").val();
               
                //Vérification Si la ligne existe déja dans le tableau
                var ligneBilletTrouver = _.findWhere(panierBillet, {billets: billet})
                if(ligneBilletTrouver!=null) {
                        //Si la ligne existe on recupere l'ancienne quantité et l'id de la ligne
                        oldQte = ligneBilletTrouver.quantite_billets;
                        idElementLigne = ligneBilletTrouver.id;
                       
                        //Si la somme des deux quantités depasse la quantité à ajouter en stock alors on block
                        var sommeDeuxQtes = parseInt(oldQte) + parseInt(quantite_billet);
                            //MAJ de la ligne
                            $tableBilletage.bootstrapTable('updateByUniqueId', {
                                id: idElementLigne,
                                row: {
                                    quantite_billet : sommeDeuxQtes,
                                    montant_billet: $.number(billet*sommeDeuxQtes),
                                }
                            });
                            ligneBilletTrouver.quantite_billets = sommeDeuxQtes;
                          
                            $("#quantite_billet").val("");
                            $("#billet").val("");
                            $("#montant_billet").val("");
                            return;
                    }
                    idTablleBillet++; 
                    $tableBilletage.bootstrapTable('insertRow',{
                        index: idTablleBillet,
                        row: {
                          id: idTablleBillet,
                          billet: billet,
                          quantite_billet: quantite_billet,
                          montant_billet: $.number(quantite_billet*billet)
                        }
                    })
                  
                    //Creation de l'article dans le tableau virtuel (panier)
                    var DataBillet= {'id':idTablleBillet, 'billets':billet, 'quantite_billets':quantite_billet};
                    panierBillet.push(DataBillet);
                 
                    $("#quantite_billet").val("");
                    $("#billet").val("");
                    $("#montant_billet").val("");
                    if(idTablleBillet>0){
                        $(".delete-billetage-row").show();
                    }else{
                        $(".delete-billetage-row").hide();
                    }
                
            }else{
                $.gritter.add({
                    title: "SMART-SFV",
                    text: "Les champs billet et quantité ne doivent pas être vides et la quantité minimum doit être 1.",
                    sticky: false,
                    image: basePath + "/assets/img/gritter/confirm.png",
                });
                return;
            }
        })
        // Find and remove selected table rows  
        $(".delete-billetage-row").click(function () {
           var selecteds = $tableBilletage.bootstrapTable('getSelections');
           var ids = $.map($tableBilletage.bootstrapTable('getSelections'), function (row) {
                        return row.id
                    })
                $tableBilletage.bootstrapTable('remove', {
                    field: 'id',
                    values: ids 
                })
              
                $.each(selecteds, function (index, value) { 
                    var ligneTrouver = _.findWhere(panierBillet, {id: value.id})
                    panierBillet = _.reject(panierBillet, function (article) {
                        return article.id == value.id;
                    });
                });
               
                if(panierBillet.length==0){
                    $(".delete-billetage-row").hide();
                    idTablleBillet = 0;
                }
        });
        
        $("#montant_payer").keyup(function(){
           var montant_payer = $("#montant_payer").val();
           var montant_a_payer = $("#montant_a_payer").val();
           var reste = parseInt(montant_payer) - parseInt(montant_a_payer);
           $(".montant_restant").html("<b>" + $.number(reste) +"</b>"); 
        });
        $("#montant_payer").focusout(function(){
           var montant_payer = $("#montant_payer").val();
           var montant_a_payer = $("#montant_a_payer").val();
           var reste = parseInt(montant_payer) - parseInt(montant_a_payer);
           if(parseInt(reste)<0 && !document.getElementById('attente').checked){
               $(".montant_restant").html("");
               alert('Veillez vérifier le montant saisie pour le payement SVP');
           }else{
              $(".montant_restant").html("<b>" + $.number(reste) +"</b>"); 
           }
       });
        $("#montant_payer_add").keyup(function(){
           var montant_payer_add = $("#montant_payer_add").val();
           var montant_a_payer_add = $("#montant_a_payer_add").val();
           var reste = parseInt(montant_payer_add) - parseInt(montant_a_payer_add);
           $(".montant_restant_add").html("<b>" + $.number(reste) +"</b>"); 
       });
        $("#montant_payer_add").focusout(function(){
           var montant_payer_add = $("#montant_payer_add").val();
           var montant_a_payer_add = $("#montant_a_payer_add").val();
           var reste = parseInt(montant_payer_add) - parseInt(montant_a_payer_add);
           if(parseInt(reste)<0 && !document.getElementById('attente').checked){
               $(".montant_restant_add").html("");
               alert('Veillez vérifier le montant saisie pour le payement SVP');
           }else{
              $(".montant_restant_add").html("<b>" + $.number(reste) +"</b>"); 
           }
       });
        
        // Submit the add form
        $("#sendButton").click(function(){  
            $("#formAjout").submit(); 
            $("#sendButton").prop("disabled", true);
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
                var url = "{{route('boutique.ventes.store')}}";
                var formData = new FormData($(this)[0]);
                createFormData(formData, 'monPanier', monPanier);
            }else{
               var methode = 'POST';
                var url = "{{route('boutique.update-vente')}}";
                 var formData = new FormData($(this)[0]);
            }
            editerVenteAction(methode, url, $(this), formData, $ajaxLoader, $table, ajout);
        });
        $("#formAjoutArticle").submit(function (e) {
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formAjoutArticle .loader-overlay");

            if (ajoutArticle==true) {
                var methode = 'POST';
                var url = "{{route('boutique.articles-vente.store')}}";
             }else{
                var id = $("#idArticleModifier").val();
                var methode = 'PUT';
                var url = 'articles-vente/' + id;
             }
            editerVentesArticlesAction(methode, url, $(this), $(this).serialize(), $ajaxLoader, $tableArticle,$table, ajoutArticle);
        });
        $("#formOuvertureCaisse").submit(function(e){
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formOuvertureCaisse .loader-overlay");
            var methode = 'POST';
            var url = "{{route('boutique.caisse-ouverte.store')}}";
            ouvertureCaisseAction(methode, url, $(this), $(this).serialize(), $ajaxLoader);
        });
        $("#formFermetureCaisse").submit(function(e){
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var caisse = $("#caisses_id").val();
            $("#caisses_fermeture").val(caisse);
            var $ajaxLoader = $("#formFermetureCaisse .loader-overlay");
            var methode = 'POST';
            var url = "{{route('boutique.femeture-caisse')}}";
            var formData = new FormData($(this)[0]);
            createFormData(formData, 'panierBillet', panierBillet);
            fermetureCaisseAction(methode, url, $(this), formData, $ajaxLoader);
        });
        
        $("#formSupprimer").submit(function (e) {
            e.preventDefault();
            var id = $("#idVenteSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimer .question");
            var $ajaxLoader = $("#formSupprimer .processing");
            supprimerAction('ventes/' + id, $(this).serialize(), $question, $ajaxLoader,$table);
        });
        $("#formSupprimerArticle").submit(function (e) {
            e.preventDefault();
            var id = $("#idArticleSupprimer").val();
            var formData = $(this).serialize();
            var $question = $("#formSupprimerArticle .question");
            var $ajaxLoader = $("#formSupprimerArticle .processing");
            supprimerArticleAction('articles-vente/' + id, $(this).serialize(), $question, $ajaxLoader, $tableArticle, $table);
        });
        $("#formUnlokInputRemisePrix").submit(function(e){
            e.preventDefault();
            var $valid = $(this).valid();
            if (!$valid) {
                $validator.focusInvalid();
                return false;
            }
            var $ajaxLoader = $("#formUnlokInputRemisePrix .loader-overlay");
            var methode = 'POST';
            var url = "{{route('auth.verification-access')}}";
            unlokInputRemisePrixAction(methode, url, $(this), $(this).serialize(), $ajaxLoader);
        });
    });
    function createFormData(formData, key, data) {
        if (data === Object(data) || Array.isArray(data)) {
            for (var i in data) {
                createFormData(formData, key + '[' + i + ']', data[i]);
            }
        } else {
            formData.append(key, data);
        }
    }
    function updateRow(idVente) {
        ajout = false;
        var $scope = angular.element($("#formAjout")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $("#idVenteModifier").val(vente.id);
        $("#montant_payer_add").val(vente.montant_payer);
        vente.attente == 1 ? $("#attente").attr("checked", true) : $("#attente").attr("checked", false); 
        $tableArticle.bootstrapTable('refreshOptions', {url: "../boutique/liste-articles-vente/" + idVente});
        $(".remise_add_row").hide();
        $("#div_enregistrement").hide();
        $("#div_update").show();
        $(".bs-modal-ajout").modal("show");
    }
    
    function deleteRow(idVente){
        var $scope = angular.element($("#formSupprimer")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateForm(vente);
        });
        $(".bs-modal-suppression").modal("show");
    }
    
    function updateArticleRow(idArticle){
        ajoutArticle = false;
        var $scope = angular.element($("#formAjoutArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateArticleForm(article);
        });
        var vente = $("#idVenteModifier").val();
        $("#vente").val(vente);
        var depot = $("#depot_id").val();
        $("#remise_sur_ligne_add").prop('readonly',true);
        $("#quantite_add").val(article.quantite);
      
        $("#article_add").val(article.article_id);
        $.getJSON("../boutique/liste-unites-by-depot-article/" + depot + "/" + article.article_id , function (reponse) {
                $('#unite_add').html("<option value=''>-- Colis --</option>");
                $.each(reponse.rows, function (index, colis) { 
                    $('#unite_add').append('<option value=' + colis.unite.id + '>' + colis.unite.libelle_unite + '</option>')
                });
                $("#unite_add").val(article.unite_id);
        })
        $.getJSON("../parametre/find-article/" + article.article_id , function (reponse) {
                $.each(reponse.rows, function (index, articles_trouver) { 
                    $("#code_barre_add").val(articles_trouver.code_barre);
                });
        })
        $.getJSON("../boutique/find-article-in-depot-by-unite/" + article.article_id + "/" + depot + "/" +  article.unite_id, function (reponse) {
                $.each(reponse.rows, function (index, articles) { 
                    $("#en_stock_add").val(articles.quantite_disponible);
                   
                    //Calcul du prix HT
                    var tva = 0;
                   if(articles.article.param_tva_id!=null){
                       $.getJSON("../parametre/find-param-tva/" + articles.article.param_tva_id, function (reponse) {
                            $.each(reponse.rows, function (index, tvas_infos) { 
                                tva = tvas_infos.montant_tva;
                                var prix_ht_article = (article.prix/(tva + 1));
                                var prix = Math.round(prix_ht_article);
                                $("#prixHT_add").val(prix);
                            });
                        })
                   }else{
                       $("#prixHT_add").val(article.prix);
                   }
                });
        })
        $("#prixTTC_add").val(article.prix);
        $("#montantTC_add").val(article.prix*article.quantite);
        $(".bs-modal-add-article").modal("show");
    }
    
    function deleteArticleRow(idArticle){
        var $scope = angular.element($("#formSupprimerArticle")).scope();
        var article =_.findWhere(rowsArticle, {id: idArticle});
         $scope.$apply(function () {
            $scope.populateSupArticleForm(article);
        });
        $(".bs-modal-supprimer-article").modal("show");
    }

    function ticketPrintRow(idVente){
        window.open("ticket-vente-pdf/" + idVente ,'_blank')
    }
    function listeArticleRow(idVente){
        var $scope = angular.element($("#panierArticle")).scope();
        var vente =_.findWhere(rows, {id: idVente});
         $scope.$apply(function () {
            $scope.populateFormPanier(vente);
        });
        $tablePanierArticle.bootstrapTable('refreshOptions', {url: "../boutique/liste-articles-vente/" + idVente});
        $(".bs-modal-panier").modal("show");
    }
    function montantTttcLigneFormatter(id, row){
        var montant = row.quantite*row.prix;
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function montantHTFormatter(id, row){
        var prix_ttc = row.prix;
        if(row.article.param_tva_id!=null){
            var tva = row.montant_tva;
            var prix_ht_article = (prix_ttc/(tva + 1));
            var prix = Math.round(prix_ht_article);
            return '<span class="text-bold">' + $.number(prix)+ '</span>';
        }else{
           return '<span class="text-bold">' + $.number(prix_ttc)+ '</span>';
        }
    }
    function monnaieFormatter(id, row){
     var monnaie = row.montant_payer - row.sommeTotale
     if(row.attente!=0){
         return '<span class="text-bold text-red">En attente</span>';
     }else{
        return '<span class="text-bold">' + $.number(monnaie)+ '</span>';
     }
    }
    function montantRemisFormatter(id, row){
        if(row.attente!=0){
            return '<span class="text-bold text-red">En attente</span>';
        }else{
           return '<span class="text-bold">' + $.number(row.montant_payer)+ '</span>';
        }
    }
    function tiketFormatter(id, row){
        if(row.attente!=0){
            return '<button type="button" class="btn btn-xs btn-success" data-placement="left" data-toggle="tooltip" title="Recupérer" onClick="javascript:updateRow(' + row.id + ');">Recupérer</button>\n\
                    <br/><button type="button" class="btn btn-xs btn-default" data-placement="left" data-toggle="tooltip" title="Ticket" onClick="javascript:ticketPrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
        }else{
            return '<button type="button" class="btn btn-xs btn-info" data-placement="left" data-toggle="tooltip" title="Ticket" onClick="javascript:ticketPrintRow(' + row.id + ');"><i class="fa fa-file-pdf-o"></i></button>';
        }
    }
    function montantFormatter(montant){
        return '<span class="text-bold">' + $.number(montant)+ '</span>';
    }
    function optionFormatter(id, row) { 
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>\n\
                <button class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    function optionAArticleFormatter(id, row) { 
            return '<button type="button" class="btn btn-xs btn-primary" data-placement="left" data-toggle="tooltip" title="Modifier" onClick="javascript:updateArticleRow(' + id + ');"><i class="fa fa-edit"></i></button>\n\
                    <button type="button" class="btn btn-xs btn-danger" data-placement="left" data-toggle="tooltip" title="Supprimer" onClick="javascript:deleteArticleRow(' + id + ');"><i class="fa fa-trash"></i></button>';
    }
    
   function listeArticleFormatter(id, row){
        return '<button type="button" class="btn btn-xs btn-warning" data-placement="left" data-toggle="tooltip" title="Panier" onClick="javascript:listeArticleRow(' + id + ');"><i class="fa fa-cart-arrow-down"></i></button>';
    }
   function editerVenteAction(methode, url, $formObject, formData, $ajoutLoader, $table, ajout = true) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                var $scope = angular.element($formObject).scope();
                $scope.$apply(function () {
                    $scope.initForm();
                });
                if (ajout) { //creation
                    $table.bootstrapTable('refresh');
                    $("#row_regle").hide();
                    $(".remise_add_row").show();
                    $("#div_enregistrement").show();
                    $("#div_update").hide();
                    $("#attente").attr("checked", false);
                    $("#remise_sur_ligne").prop('readonly',true);
                    $(".delete-row").hide();
                    $tableAddRowArticle.bootstrapTable('removeAll');
                    monPanier = [];
                    idTablle =  0;
                     montantHT = 0;
                    montantTTC = 0;
                    remiseTTC = 0;
                    $(".montantHT").html("<b>"+ $.number(montantHT)+"</b>");
                    $(".montantTVA").html("<b>" + $.number(montantTTC-montantHT) + "</b>");
                    $(".remiseTTC").html("<b>" + $.number(remiseTTC) +"</b>");
                    $(".montantTTC").html("<b>" + $.number(montantTTC-remiseTTC) +"</b>");
                    $("#montant_a_payer").val(0);
                    $("#montant_payer").val("");
                    $(".montant_restant").html("");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $(".bs-modal-ajout").modal("hide");
                }
                $("#row_regle").hide();
                if(reponse.data.attente!=1){
                  window.open("ticket-vente-pdf/" + reponse.data.id ,'_blank')  
                }
                location.reload();
                $formObject.trigger('eventAjouter', [reponse.data]);
                $("#sendButton").prop("disabled", false);
            }else{
                $("#sendButton").prop("disabled", false);
            }
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
            $("#sendButton").prop("disabled", false);
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
   
    function editerVentesArticlesAction(methode, url, $formObject, formData, $ajoutLoader, $table,$tableVente, ajoutArticle = true) {
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
                if (ajoutArticle) { //creation
                    $table.bootstrapTable('refresh');
                    $tableVente.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                } else { //Modification
                    $table.bootstrapTable('updateByUniqueId', {
                        id: reponse.data.id,
                        row: reponse.data
                    });
                    $table.bootstrapTable('refresh');
                    $tableVente.bootstrapTable('refresh');
                    $(".bs-modal-add-article").modal("hide");
                }
                $formObject.trigger('eventAjouter', [reponse.data]);
                ajout = false;
            }
            $("#montant_payer_add").val(0);
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
  
   function ouvertureCaisseAction(methode, url, $formObject, formData, $ajoutLoader) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                //Si la caisse est ouverte on recharge la page
                location.reload();
            }
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
   function fermetureCaisseAction(methode, url, $formObject, formData, $ajoutLoader) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        contentType: false,
        processData: false,
        success:function (reponse, textStatus, xhr){
            if (reponse.code === 1) {
                //Si la caisse est fermée on génère l'etat et on recharge la page
                 window.open("billetage-pdf/" + reponse.data.id ,'_blank')
                location.reload();
            }
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
   function unlokInputRemisePrixAction(methode, url, $formObject, formData, $ajoutLoader) {
    jQuery.ajax({
        type: methode,
        url: url,
        cache: false,
        data: formData,
        success:function (reponse, textStatus, xhr){
            if(reponse.code === 1) {
                //Si les accès sont bons
                $("#remise_sur_ligne").prop('readonly',false);
                $("#remise_sur_ligne_add").prop('readonly',false);
                $(".bs-modal-unlok-input-remise-prix").modal("hide");
            }
            if(reponse.code != 1) {
                $("#remise_sur_ligne").prop('readonly',true);
            }
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
    
    //Supprimer un article 
    function supprimerArticleAction(url, formData, $question, $ajaxLoader, $table, $tableVente) {
    jQuery.ajax({
        type: 'DELETE',
        url: url,
        cache: false,
        data: formData,
        success: function (reponse) {
            if (reponse.code === 1) {
                 $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [reponse.data.id]
                });
                $table.bootstrapTable('refresh');
                $tableVente.bootstrapTable('refresh');
                $(".bs-modal-supprimer-article").modal("hide");
                ajout = false;
                $("#montant_payer_add").val(0);
            }
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
            //alert(res.message);
            //alert(Object.getOwnPropertyNames(res));
            $.gritter.add({
                // heading of the notification
                title: "SMART-SFV",
                // the text inside the notification
                text: res.message,
                sticky: false,
                image: basePath + "/assets/img/gritter/confirm.png"
            });
            $ajaxLoader.hide();
            $question.show();
        },
        beforeSend: function () {
            $question.hide();
            $ajaxLoader.show();
        },
        complete: function () {
            $ajaxLoader.hide();
            $question.show();
        }
    });
}
</script>
@endsection