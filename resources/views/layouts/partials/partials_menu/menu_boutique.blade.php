<li class="{{ Route::currentRouteName() === 'boutique.promotions.index' 
                        || Route::currentRouteName() === 'boutique.ventes.index' 
                        || Route::currentRouteName() === 'boutique.reglements.index' 
                        || Route::currentRouteName() === 'boutique.point-caisse-admin' 
                        || Route::currentRouteName() === 'boutique.ponit-caisse-vu-by-admin-gerant'
                        || Route::currentRouteName() === 'boutique.point-vente'
                        || Route::currentRouteName() === 'boutique.vente-divers'
                        || Route::currentRouteName() === 'boutique.operation-caisses-admin'
                        || Route::currentRouteName() === 'boutique.retour-articles.index' 
                        ? 'active treeview' : 'treeview'}}">
          <a href="#">
            <i class="fa fa-building"></i> <span>Boutique</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           
              <li class="{{Route::currentRouteName() === 'boutique.point-vente'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.point-vente')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-credit-card"></i> Vente
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.point-caisse-admin'
                        || Route::currentRouteName() === 'boutique.ponit-caisse-vu-by-admin-gerant'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.point-caisse-admin')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-hdd-o"></i> Point de caisse
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.vente-divers'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.vente-divers')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-angellist"></i> Vente divers
                  </a>
              </li>
                <li class="{{Route::currentRouteName() === 'boutique.retour-articles.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.retour-articles.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-mail-forward"></i> Retour d'articles
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.reglements.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.reglements.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-money"></i> R&egrave;glement de facture
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.operation-caisses-admin'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.operation-caisses-admin')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-exchange"></i> Op&eacute;ration caisse
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'boutique.promotions.index'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('boutique.promotions.index')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-tripadvisor"></i> Promotion
                  </a>
              </li>
          </ul>
