<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SMART-SFV</title>

        <!-- Scripts -->
        <script src="{{asset('assets/plugins/angular/angular.js')}}"></script>
        <script src="{{asset('assets/plugins/jQuery/jquery-3.1.0.min.js')}}"></script>
        <script src="{{asset('assets/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminLte/dist/js/adminlte.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminLte/plugins/pace/pace.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('assets/js/jquery.cookies.js')}}" type="text/javascript"></script>
        <script src="{{asset('assets/js/jquery.gritter.min.js')}}" type="text/javascript"></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

        <!-- Styles --> 
        <link href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
         <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/select2/select2-bootstrap.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/bootstrap/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/template/admin/css/AdminLTE.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/template/admin/css/skins/skin-blue.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/pace/pace.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/jquery.gritter.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/costumer-style.css') }}" rel="stylesheet">
        <link href="{{ asset('adminLte/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        <link href="{{ asset('adminLte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet">
        <link href="{{ asset('adminLte/plugins/pace/pace.min.css') }}" rel="stylesheet">
    </head>
<body id="smartyApp" ng-app="smartyApp" class="hold-transition skin-blue sidebar-mini">
    <script type="text/javascript">
            var appSmarty = angular.module('smartyApp', []);
            var basePath = "{{url('')}}";
    </script>
    <div class="wrapper" id="app">
   <header class="main-header">
                <!-- Logo -->
                <a href="#" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini">S-<b>S</b></span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg">SMART-<b>SFV</b></span>
                </a>
                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                           
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="{{asset('images/profil.png')}}" class="user-image" alt="User Image">
                                    <span class="hidden-xs">{{Auth::user()->full_name}}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="{{asset('images/profil.png')}}" class="img-circle" alt="User Image">

                                        <p>
                                            {{ Auth::user()->full_name .' - '. Auth::user()->role}}
                                            <small><?= 'Inscrit le ' . date('d-m-Y à H:i', strtotime(Auth::user()->created_at)); ?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->
                                    <li class="user-body">
                                        <div class="row">

                                        </div>
                                        <!-- /.row -->
                                    </li>
                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                    
                                        <div class="pull-left">
                                            <a class="btn btn-primary btn-flat" href="{{route('auth.profil-informations')}}">
                                                Profil
                                            </a>
                                        </div>
                                       
                                        <div class="pull-right">
                                            <a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                D&eacute;connexion
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
    <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <!--Liste des menus-->
    <section class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="{{ request()->is('/parametre')
                        || Route::currentRouteName() === 'home'
                        ? 'active' : ''}}">
                <a href="{{route('home')}}"><i class="fa fa-dashboard"></i> <span>Tableau de bord</span></a>
            </li>
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Comptable')
                @include('layouts.partials.partials_menu.menu_parametre') 
            @endif
            @if(Auth::user()->role == 'Caissier')
            <li class="{{ request()->is('/boutique')
                        || Route::currentRouteName() === 'boutique.ponit-caisse'
                        ? 'active' : ''
                }}">
                <a href="{{route('boutique.ponit-caisse')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-hdd-o"></i>Point de caisse
                </a>
            </li>
            <li class="{{ request()->is('/boutique')
                        || Route::currentRouteName() === 'boutique.operation-caisses'
                        ? 'active' : ''
                }}">
                <a href="{{route('boutique.operation-caisses')}}">
                    &nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i>Op&eacute;rations de caisse
                </a>
            </li>
            @endif
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
            @include('layouts.partials.partials_menu.menu_stock') 
            @include('layouts.partials.partials_menu.menu_boutique') 
            @include('layouts.partials.partials_menu.menu_comptabilite') 
            @include('layouts.partials.partials_menu.menu_etat') 
            @endif 
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Agence')
              
                <li class="{{ request()->is('/canal')
                            || Route::currentRouteName() === 'canal.abonnes.index'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('canal.abonnes.index')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-users"></i>Abonn&eacute;s
                    </a>
                </li>
                <li class="{{ request()->is('/boutique')
                            || Route::currentRouteName() === 'canal.abonnements.index'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('canal.abonnements.index')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-minus-circle"></i>Abonnement
                    </a>
                </li>
                <li class="{{Route::currentRouteName() === 'canal.reabonnements.index'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('canal.reabonnements.index')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-retweet"></i>R&eacute;abonnement
                    </a>
                </li>
                <li class="{{ request()->is('/canal') || Route::currentRouteName() === 'canal.vente-materiels.index'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('canal.vente-materiels.index')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-cog"></i>Vente de mat&eacute;riel
                    </a>
                </li>
<!--                <li class="{{Route::currentRouteName() === 'boutique.retour-materiel'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('boutique.retour-materiel')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-reply"></i>Retour de mat&eacute;riel
                    </a>
                </li>-->
<!--                <li class="{{ request()->is('/boutique')
                            || Route::currentRouteName() === 'canal.rebis-agence'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('canal.rebis-agence')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i>Rechargements
                    </a>
                </li>-->
                 <li class="{{ request()->is('/boutique')
                            || Route::currentRouteName() === 'canal.caution-agence'
                            ? 'active' : ''
                    }}">
                    <a href="{{route('canal.caution-agence')}}">
                        &nbsp;&nbsp;&nbsp;<i class="fa fa-chrome"></i>Cautions
                    </a>
                </li>
            @endif 
            @if(Auth::user()->role == 'Comptable' or Auth::user()->role == 'Logistic')
            @include('layouts.partials.partials_menu.menu_stock') 
            @endif
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur')
            <li class="{{ request()->is('/auth')
                               || Route::currentRouteName() === 'auth.users.index'
                               || Route::currentRouteName() === 'auth.profil-informations'
                               || Route::currentRouteName() === 'auth.infos-profil-to-update'
                               ? 'active' : ''}}">
                <a href="{{route('auth.users.index')}}"><i class="fa fa-users"></i> <span>Utilisateurs</span></a>
            </li>
            @endif
            @if(Auth::user()->role == 'Concepteur')
             <li class="{{Route::currentRouteName() === 'configuration' ? 'active' : ''}}">
             <a href="{{route('configuration')}}"><i class="fa fa-cog"></i> <span>Configuration</span></a>
             </li>
             <li class="{{Route::currentRouteName() === 'parametre.depots.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.depots.index')}}">
                    <i class="fa fa-bank"></i> D&eacute;p&ocirc;t
                  </a>
              </li>
            @endif
            @if(Auth::user()->id == 1)
            <li class="{{Route::currentRouteName() === 'auth.restaurages.index' || request()->is('auth/one_table/*')? 'active' : ''}}">
             <a href="{{route('auth.restaurages.index')}}"><i class="fa fa-database"></i> <span>Restaurer donn&eacute;es</span></a>
             </li>
            @endif
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> <span>D&eacute;connexion</span></a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
         {{ $menuPrincipal }} 
         <small>> {{$titleControlleur}}</small>
        @if ($btnModalAjout === 'TRUE')
          <button id="btnModalAjout" class="btn btn-sm btn-primary pull-right">
            <i class="fa fa-plus"></i> Ajouter
          </button>
        @endif
      </h1>
    </section>
    <section class="content">
     @yield('content')
    </section>
   </div>
   <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2021 <a href="http://groupsmarty.com/">Group Smarty</a>.</strong> All rights
    reserved.
  </footer>
  <script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
  <script src="{{asset('assets/plugins/select2/i18n/fr.js')}}"></script>
  <script type="text/javascript">
//      jQuery.cookie.json = true;
    $(document).ajaxStart(function() { Pace.restart(); });//Loader pour toutes les requetes Ajax, fourni par le template
    $('.loader, .loader-overlay, .processing, .loader-login').hide();
    
    $(function () {
       
             $('.loader, .loader-overlay, .processing').hide();
             $(document).ajaxComplete(function (event, xhr, settings) {
                    //console.log("xhr.status", xhr.status);
                    if (xhr.status === 302) {
                        $.gritter.add({
                            // heading of the notification
                            title: "SMART-SFV",
                            // the text inside the notification
                            text: reponse.msg,
                            sticky: false,
                            image: "../assets/img/gritter/confirm.png",
                        });
                        return;
                    }
                });
             
             //Reactivation de fenetre modal (le cas ou 2 fenetres sont superposées)
                $(document).on('hidden.bs.modal', function (e) {
                    if ($('.modal:visible').length) {
                        $("body").addClass('modal-open');
                    }
                });
             $("#btnModalAjout").on("click", function () {
                  ajout = true;
                  document.forms["formAjout"].reset();
                  $(".bs-modal-ajout").modal("show");
              });
             
   });
  </script>
</body>
</html>
