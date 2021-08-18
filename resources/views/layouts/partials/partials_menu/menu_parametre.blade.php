<li class="{{request()->is('parametre/*') && Route::currentRouteName()!=='parametre.depots.index'
                        ? 'active treeview' : 'treeview'}}">
          <a href="#">
              <i class="fa fa-cogs"></i> <span>Param&egrave;tre</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Comptable') 
              <li class="{{Route::currentRouteName() === 'parametre.clients.index'
                          || request()->is('parametre/fiche-client/*')
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.clients.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-briefcase"></i> Client
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.fournisseurs.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.fournisseurs.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-truck"></i> Fournisseur
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'parametre.banques.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.banques.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-bank"></i> Banque
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'parametre.param-tva.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.param-tva.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-text-height"></i> TVA
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.regimes.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.regimes.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle-o"></i> R&eacute;gime
                  </a>
              </li>
              @endif
              @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur') 
              <li class="{{Route::currentRouteName() === 'parametre.articles.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.articles.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-cubes"></i> Article
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.caisses.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.caisses.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-hdd-o"></i> Caisse
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.nations.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.nations.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-flag"></i> Pays
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.categories.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.categories.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Cat&eacute;gorie
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'parametre.sous-categories.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.sous-categories.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Sous cat&eacute;gorie
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.moyen-reglements.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.moyen-reglements.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-cog"></i> Moyen de payement
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.rayons.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.rayons.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows-h"></i> Rayon
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.rangees.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.rangees.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-bars"></i> Rang&eacute;e
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.casiers.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.casiers.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows"></i> Casier
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.unites.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.unites.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-tachometer"></i> Unit&eacute;
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.tailles.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.tailles.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-tachometer"></i> Taille
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.divers.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.divers.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Divers
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'parametre.categorie-depenses.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('parametre.categorie-depenses.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-list"></i> Cat&eacute;gorie d&eacute;pense
                  </a>
              </li>
               @endif
          </ul>
