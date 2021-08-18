<li class="{{           Route::currentRouteName() === 'etat.etat-approvisionnements' 
                        || Route::currentRouteName() === 'etat.etat-ventes' 
                        || Route::currentRouteName() === 'etat.etat-articles' 
                        || Route::currentRouteName() === 'etat.etat-fournisseurs' 
                        || Route::currentRouteName() === 'etat.etat-clients' 
                        || Route::currentRouteName() === 'etat.etat-depots' 
                        || Route::currentRouteName() === 'etat.etat-inventaires' 
                        || Route::currentRouteName() === 'etat.etat-transfert-stock' 
                        || Route::currentRouteName() === 'etat.etat-destockage' 
                        || Route::currentRouteName() === 'etat.etat-reglements' 
                        || Route::currentRouteName() === 'etat.articles-vendus-par-quantite' 
                        || Route::currentRouteName() === 'etat.articles-recus-par-quantite' 
                        || Route::currentRouteName() === 'etat.articles-retournes' 
                        ? 'active treeview' : 'treeview'}}">
          <a href="#">
            <i class="glyphicon glyphicon-th-large"></i> <span>Etats</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li class="{{ Route::currentRouteName() === 'etat.etat-ventes'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-ventes')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Vente caisse
                  </a>
              </li> 
<!--              <li class="{{Route::currentRouteName() === 'etat.etat-reglements'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-reglements')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> R&egrave;glement
                  </a>
              </li>-->
              <li class="{{Route::currentRouteName() === 'etat.etat-articles'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-articles')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Article
                  </a>
              </li>
               <li class="{{Route::currentRouteName() === 'etat.articles-vendus-par-quantite'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.articles-vendus-par-quantite')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Articles vendus
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'etat.articles-recus-par-quantite'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.articles-recus-par-quantite')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Articles re&ccedil;us
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'etat.articles-retournes'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.articles-retournes')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Articles retourn&eacute;s
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'etat.etat-fournisseurs'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-fournisseurs')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Fournisseur
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'etat.etat-clients'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-clients')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Client
                  </a>
              </li>
              <li class="{{Route::currentRouteName() === 'etat.etat-depots'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-depots')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;p&ocirc;t
                  </a>
              </li>
<!--              <li class="{{Route::currentRouteName() === 'etat.etat-inventaires'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-inventaires')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Inventaire
                  </a>
              </li>-->
<!--              <li class="{{Route::currentRouteName() === 'etat.etat-transfert-stock'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-transfert-stock')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> Transfert de stock
                  </a>
              </li>-->
<!--              <li class="{{Route::currentRouteName() === 'etat.etat-destockage'
                        ? 'active' : ''
                  }}">
                  <a href="{{route('etat.etat-destockage')}}">
                      &nbsp;&nbsp;&nbsp;<i class="fa fa-circle"></i> D&eacute;stockage
                  </a>
              </li>-->
          </ul>
