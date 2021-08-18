<li class="{{
                        || Route::currentRouteName() === 'auth.users.index'
                        || Route::currentRouteName() === 'auth.profil-informations'
                        || Route::currentRouteName() === 'auth.infos-profil-to-update'
                        ? 'active treeview' : 'treeview'}}">
          <a href="#">
            <i class="fa fa-users"></i> <span>Utilisateurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{
                               || Route::currentRouteName() === 'auth.users.index'
                               || Route::currentRouteName() === 'auth.profil-informations'
                               || Route::currentRouteName() === 'auth.infos-profil-to-update'
                               ? 'active' : ''}}">
                <a href="{{route('auth.users.index')}}"><i class="fa fa-user-circle"></i> <span>Administrateurs</span></a>
            </li>
            <li class="{{
                               || Route::currentRouteName() === 'auth.users.index'
                               ? 'active' : ''}}">
                               <a href="{{route('auth.users.index')}}"><i class="fa fa-users"></i> <span>G&eacute;rants et caissiers</span></a>
            </li>
          </ul>
