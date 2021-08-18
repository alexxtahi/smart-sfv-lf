@extends('layouts.app')
@section('content')
<script src="{{asset('assets/plugins/jQuery/jquery-3.1.0.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-table/locale/bootstrap-table-fr-FR.js')}}"></script>
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{$clients->count()}}</h3>

                <p>Clients</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{url('parametre/clients')}}" class="small-box-footer">Voir <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{$articles->count()}}</h3>

                <p>Articles</p>
            </div>
            <div class="icon">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{url('parametre/articles')}}" class="small-box-footer">Voir <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{$depots->count()}}</h3>

                <p>D&eacute;p&ocirc;ts</p>
            </div>
            <div class="icon">
                <i class="fa fa-bank"></i>
            </div>
            <a href="{{url('boutique/depot-articles')}}" class="small-box-footer">Voir <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{$fournisseurs->count()}}</h3>

                <p>Fournisseurs</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
            <a href="{{url('parametre/fournisseurs')}}" class="small-box-footer">Voir <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Caisses ouvertes</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur')
                                <th>D&eacute;p&ocirc;t</th>
                            @endif
                                <th>Caisse</th>
                                <th>Date d'ouverture</th>
                                <th>Ouverte par</th>
                                <th>Solde d'ouverture</th>
                                <th>Solde actuel</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($caisse_ouvertes as $caisse_ouverte)
                            <tr>
                                <td>{{$caisse_ouverte->libelle_depot}}</td>
                                <td>{{$caisse_ouverte->libelle_caisse}}</td>
                                <td>{{$caisse_ouverte->date_ouvertures}}</td>
                                <td>{{$caisse_ouverte->full_name}}</td>
                                <td>{{number_format($caisse_ouverte->montant_ouverture, 0, ',', ' ')}}</td>
                                <td>{{number_format($caisse_ouverte->sommeTotale+$caisse_ouverte->entree+$caisse_ouverte->montant_ouverture-$caisse_ouverte->sortie, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('boutique.point-caisse-admin')}}" class="btn btn-xs btn-success pull-right">Voir plus</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
   
</div>
@endif
@if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant' or Auth::user()->role == 'Comptable' or Auth::user()->role == 'Logistic')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Articles en voie de p&eacute;remption</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>D&eacute;p&ocirc;t</th>
                                <th>Article</th>
                                <th>Lot</th>
                                <th>Date p&eacute;remption</th>
                                <th>Sera parim&eacute; dans</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($article_envoie_peremptions as $article)
                                <?php 
                                    $now = date("Y-m-d");
                                    $diff = strtotime($article->date_peremption) - strtotime($now); 
                                    $nbJour = $diff/86400;
                                    if($nbJour > 61){ 
                                        continue;
                                    }
                                ?>
                            <tr>
                                <td>{{$article->libelle_depot}}</td>
                                <td>{{$article->description_article}}</td>
                                <td>{{$article->libelle_unite}}</td>
                                <td>{{$article->date_peremptions}}</td>
                            @if($nbJour>30 && $nbJour<60)   
                                 <td><span class="text-bold text-warning">{{$nbJour}} jour(s)</span></td>
                            @endif
                            @if($nbJour<30)   
                                 <td><span class="text-bold text-red">{{$nbJour}} jour(s)</span></td>
                            @endif
                            @if($nbJour>60)   
                                 <td>{{$nbJour}} jour(s)</td>
                            @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('boutique.articles-en-voie-peremption-pdf')}}" target="_blank" class="btn btn-xs btn-success pull-right">Imprimer</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Articles en voie de rupture</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Cat&eacute;gorie</th>
                                <th>Sous cat&eacute;gorie</th>
                                <th>En stock</th>
                                <th>D&eacute;p&ocirc;t</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($articleRupture as $article)
                            <tr>
                                <td>{{$article->description_article}}</td>
                                <td>{{$article->categorie->libelle_categorie}}</td>
                                <td>{{$article->sous_categorie_id!=null ? $article->sous_categorie->libelle_sous_categorie : ""}}</td>
                                <td>{{$article->totalStock}}</td>
                                <td>{{$article->libelle_depot}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('boutique.depot-articles.index')}}" class="btn btn-xs btn-success pull-right">Voir plus</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</div>

<div class="row">
    @if(Auth::user()->role != 'Logistic')
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des 5 meilleurs clients</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($tenBesteClients as $client)
                            <tr>
                                <td>{{$client->full_name_client}}</td>
                                <td>{{$client->contact_client}}</td>
                                <td>{{number_format($client->sommeTotale, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('etat.chiffre-affaire-client')}}" target="_blank" class="btn btn-xs btn-success pull-right">Impirmer</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div> 
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des 5 clients les moins rentables</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($tenBadClients as $client)
                            <tr>
                                <td>{{$client->full_name_client}}</td>
                                <td>{{$client->contact_client}}</td>
                                <td>{{number_format($client->sommeTotale, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('etat.chiffre-affaire-client')}}" target="_blank" class="btn btn-xs btn-success pull-right">Impirmer</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div> 
     @endif
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des 5 articles les plus vendus</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Qauntit&eacute;</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($articlesPlusVendus as $article)
                            <tr>
                                <td>{{$article->description_article}}</td>
                                <td>{{$article->qteTotale}}</td>
                                <td>{{number_format($article->sommeTotale, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('etat.liste-vente-by-quantite')}}" target="_blank" class="btn btn-xs btn-success pull-right">Impirmer</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div> 
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des 5 articles les moins vendus</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Qauntit&eacute;</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($articlesMoinsVendus as $article)
                            <tr>
                                <td>{{$article->description_article}}</td>
                                <td>{{$article->qteTotale}}</td>
                                <td>{{number_format($article->sommeTotale, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('etat.liste-vente-by-quantite')}}" target="_blank" class="btn btn-xs btn-success pull-right">Impirmer</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div> 
   
    @if(Auth::user()->role != 'Logistic')
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des 5 clients les plus endett&eacute;s</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Adresse</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($clientsPlusEndettes as $client)
                            <tr>
                                <td>{{$client->full_name_client}}</td>
                                <td>{{$client->contact_client}}</td>
                                <td>{{$client->adresse_client}}</td>
                                <td>{{number_format($client->sommeTotale-$client->accompteTotale, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('etat.solde-client-pdf')}}" target="_blank" class="btn btn-xs btn-success pull-right">Impirmer</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div> 
    @endif
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Commandes en cours</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Date commande</th>
                                <th>N° Bon</th>
                                <th>Fournisseur</th>
                                <th>Montant</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @foreach($commande_encours as $commande)
                            <tr>
                                <td>{{$commande->date_bon_commandes}}</td>
                                <td>{{$commande->numero_bon}}</td>
                                <td>{{$commande->fournisseur->full_name_fournisseur}}</td>
                                <td>{{number_format($commande->montantBon, 0, ',', ' ')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <a href="{{route('boutique.bon-commandes.index')}}" class="btn btn-xs btn-success pull-right">Voir plus</a>
            </div>
            <!-- /.box-footer -->
        </div>
    </div> 
</div>
@endif
@if(Auth::user()->role == 'Caissier')
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-10">
        <img class="img-responsive text-center" src="{{asset($get_configuration_infos->logo)}}" alt="Photo" style="width:70%;">
    </div>
</div>
@endif
@endsection
