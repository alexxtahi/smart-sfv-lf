<li class="{{Route::currentRouteName() === 'etat.solde-client' 
                || Route::currentRouteName() === 'etat.solde-fournisseur' 
                || Route::currentRouteName() === 'boutique.ticket-declare' 
                || Route::currentRouteName() === 'etat.marge-vente' 
                || Route::currentRouteName() === 'etat.tva-airsi' 
                || Route::currentRouteName() === 'etat.points-caisses-clotures' 
                || Route::currentRouteName() === 'etat.timbre-fiscal' 
                || Route::currentRouteName() === 'etat.declaration-fiscal' 
                || Route::currentRouteName() === 'boutique.depenses.index' 
                ? 'active treeview' : 'treeview'}}">
    <a href="#">
        <i class="fa fa-book"></i> <span>Comptabilit&eacute;</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ Route::currentRouteName() === 'etat.solde-client'
                        ? 'active' : ''
            }}">
            <a href="{{route('etat.solde-client')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Solde client
            </a>
        </li> 
        <li class="{{Route::currentRouteName() === 'etat.solde-fournisseur'
                        ? 'active' : ''
            }}">
            <a href="{{route('etat.solde-fournisseur')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Solde fournisseur
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.declaration-fiscal'
                        ? 'active' : ''
            }}">
            <a href="{{route('etat.declaration-fiscal')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;claration TVA
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'boutique.ticket-declare'
                        ? 'active' : ''
            }}">
            <a href="{{route('boutique.ticket-declare')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Ticket d&eacute;clar&eacute; pour TVA
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.timbre-fiscal'
                        ? 'active' : ''
            }}">
            <a href="{{route('etat.timbre-fiscal')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Timbre fiscal
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.marge-vente'
                        ? 'active' : ''
            }}">
            <a href="{{route('etat.marge-vente')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Marge sur vente
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'etat.points-caisses-clotures'
                        ? 'active' : ''
            }}">
            <a href="{{route('etat.points-caisses-clotures')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Points de caisse clotur&eacute;s
            </a>
        </li>
        <li class="{{Route::currentRouteName() === 'boutique.depenses.index'
                        ? 'active' : ''
            }}">
            <a href="{{route('boutique.depenses.index')}}">
                &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;penses
            </a>
        </li>
    </ul>
