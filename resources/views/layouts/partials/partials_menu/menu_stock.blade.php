<li class="{{
                         Route::currentRouteName() === 'boutique.approvisionnements.index' 
                        || Route::currentRouteName() === 'boutique.inventaires.index' 
                        || Route::currentRouteName() === 'boutique.destockages.index' 
                        || Route::currentRouteName() === 'boutique.transfert-stocks.index' 
                        || Route::currentRouteName() === 'boutique.depot-articles.index' 
                        || Route::currentRouteName() === 'boutique.bon-commandes.index' 
                        || Route::currentRouteName() === 'boutique.reception-commande' 
                        || Route::currentRouteName() === 'boutique.vu-liste-article-by-unite-in-depot' 
                        || Route::currentRouteName() === 'boutique.mouvements-stocks.index' 
                        || Route::currentRouteName() === 'boutique.mouvement-stocks-grouper' 
                        || request()->is('boutique/inventaires/*/edit')
                        ? 'active treeview' : 'treeview'}}">
          <a href="#">
            <i class="fa fa-hourglass"></i> <span>Stock</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              @if(Auth::user()->role == 'Comptable' or Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
              <li class="{{Route::currentRouteName() === 'boutique.bon-commandes.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.bon-commandes.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-file-text"></i> Bon de commande
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.reception-commande'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.reception-commande')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-shopping-cart"></i> R&eacute;ception de commande
                  </a>
              </li>
            @endif
            @if(Auth::user()->role == 'Logistic')
             <li class="{{Route::currentRouteName() === 'boutique.bon-commandes.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.bon-commandes.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-file-text"></i> Bon de commande
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'boutique.reception-commande'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.reception-commande')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-shopping-cart"></i> R&eacute;ception de commande
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'boutique.approvisionnements.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.approvisionnements.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-subway"></i> Approvisionnement
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.transfert-stocks.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.transfert-stocks.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-map-signs"></i> Transfert de stock
                  </a>
              </li>
            @endif
            @if(Auth::user()->role == 'Concepteur' or Auth::user()->role == 'Administrateur' or Auth::user()->role == 'Gerant')
             <li class="{{Route::currentRouteName() === 'boutique.approvisionnements.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.approvisionnements.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-subway"></i> Approvisionnement
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.depot-articles.index'
                            || Route::currentRouteName() === 'boutique.vu-liste-article-by-unite-in-depot'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.depot-articles.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-institution"></i> Stock par d&eacute;p&ocirc;t
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.transfert-stocks.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.transfert-stocks.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-map-signs"></i> Transfert de stock
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.destockages.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.destockages.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-download"></i> D&eacute;stockage
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.inventaires.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.inventaires.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-calendar-plus-o"></i> Inventaire
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'boutique.mouvements-stocks.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.mouvements-stocks.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-arrows-alt"></i> Mouvements stock
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'boutique.mouvement-stocks-grouper'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.mouvement-stocks-grouper')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-cubes"></i> Mouvements stock article
                  </a>
              </li>
              @endif
          </ul>
