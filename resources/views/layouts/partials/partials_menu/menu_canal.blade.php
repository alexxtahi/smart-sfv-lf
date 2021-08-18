<li class="{{request()->is('canal/*') || Route::currentRouteName() === 'auth.users-agence-canal' ? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-contao"></i> <span>Canal</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ Route::currentRouteName() === 'canal.localites.index' ? 'active' : '' }}">
            <a href="{{route('canal.localites.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-institution"></i> Localit&eacute;
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.type-abonnements.index' ? 'active' : ''}}">
            <a href="{{route('canal.type-abonnements.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Offres
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.options-canal.index' ? 'active' : ''}}">
            <a href="{{route('canal.options-canal.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-opera"></i> Options canal
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.type-cautions.index' ? 'active' : ''}}">
            <a href="{{route('canal.type-cautions.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Type de caution
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.type-pieces.index' ? 'active' : ''}}">
            <a href="{{route('canal.type-pieces.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Type de pi&egrave;ce
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.materiels.index'? 'active' : ''}}">
            <a href="{{route('canal.materiels.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-tv"></i> Mat&eacute;riel
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.agences.index' ? 'active' : ''}}">
            <a href="{{route('canal.agences.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-hospital-o"></i> Agence
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.demande-approv.index' ? 'active' : ''}}">
            <a href="{{route('canal.demande-approv.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-plus"></i> Caution Canal
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.caution-agences.index' ? 'active' : ''}}">
            <a href="{{route('canal.caution-agences.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-chrome"></i> Caution Agence
            </a>
        </li> 
        <li class="{{ Route::currentRouteName() === 'canal.rebis.index' ? 'active' : ''}}">
            <a href="{{route('canal.rebis.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i> Liste des rechargements
            </a>
        </li> 
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
        <li class="{{Route::currentRouteName() === 'canal.mouvement-vente'
                            ? 'active' : ''
            }}">
            <a href="{{route('canal.mouvement-vente')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i>Mouvement des ventes
            </a>
        </li>
        <li class="{{ Route::currentRouteName() === 'auth.users-agence-canal' ? 'active' : ''}}">
            <a href="{{route('auth.users-agence-canal')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-users"></i> Utilisateur agence
            </a>
        </li> 
    </ul>
