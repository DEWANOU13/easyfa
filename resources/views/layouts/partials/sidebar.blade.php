@php
    $emballageValueB = \App\Models\PrefixeReference::first()->emballage; // ou le namespace correct de votre modèle
@endphp
<div class="left side-menu">
    <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
        <i class="ion-close"></i>
    </button>

    <div class="left-side-logo d-block d-lg-none" style="background-color: aliceblue;">
        <div class="text-center">
            <a href="/" class="logo"><img src="{{ asset('images/logo_easyfac.png') }}" height="70"
                    widht="150" alt="logo" class="mt-2"></a>
        </div>
    </div>

    <div class="sidebar-inner slimscrollleft">
        <div id="sidebar-menu">
            <ul>
                <li>
                    <a href="{{ route('home') }}" class="waves-effect">
                        <i class="dripicons-meter"></i>
                        <strong>Tableau de bord </strong>
                    </a>
                </li>

                {{-- Acceuil --}}
                @canany(['consulter-liste-magasin', 'consulter-liste-fournisseurs', 'consulter-categorie-client',
                    'consulter-client', 'consulter-liste-categorie-produit', 'consulter-unite-comptage',
                    'consulter-liste-produits', 'consulter-prix-vente'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Acceuil </strong>
                        </a>
                    </li>
                @endcanany

                @can('consulter-liste-magasin')
                    <li class="">
                        <a href="{{ route('magasin') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                            <strong>Magasins</strong></a>
                    </li>
                @endcan

                @can('consulter-liste-fournisseurs')
                    <li class="">
                        <a href="{{ route('fournisseur') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                            <strong>Fournisseurs</strong>
                        </a>
                    </li>
                @endcan

                @canany(['consulter-categorie-client', 'consulter-client'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="dripicons-document"></i>
                            <span> Clients</span>
                            <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span>
                        </a>

                        <ul class="list-unstyled">
                            @can('consulter-categorie-client')
                                <li><a href="{{ route('categorieclient') }}">Catégories</a></li>
                            @endcan
                            @can('consulter-client')
                                <li><a href="{{ route('client') }}">Client</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['consulter-liste-categorie-produit', 'consulter-unite-comptage', 'consulter-liste-produits',
                    'consulter-prix-vente'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span> Produits
                            </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            @can('consulter-liste-categorie-produit')
                                <li><a href="{{ route('page.produit.categorie') }}">Catégories</a></li>
                            @endcan
                            @can('liste-groupe-categorie-produit')
                                <li><a href="{{ route('groupeCategorie') }}">Groupe Catégorie</a></li>
                            @endcan

                            @can('consulter-unite-comptage')
                                <li><a href="{{ route('page.produit.unite_comptage') }}">Unités de comptage</a></li>
                            @endcan

                            @can('consulter-liste-produits')
                                <li><a href="{{ route('page.produit.produit') }}">Produits</a></li>
                            @endcan

                            @can('seuil-stock')
                                <li><a href="{{ route('seuilStock') }}">Seuil de stock</a></li>
                            @endcan

                            @can('consulter-prix-vente')
                                <li><a href="{{ route('gestion_des_prix') }}">Gestion des prix</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Embalages --}}

                @canany(['liste-categorie-emballage', 'liste-emballage'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Gestion des Emballages </strong>
                        </a>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                Emballages
                            </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            @can('liste-categorie-emballage')
                                <li><a href="{{ route('categorie_emballage') }}">Catégorie emballage</a></li>
                            @endcan
                            @can('liste-emballage')
                                <li><a href="{{ route('emballage') }}">Emballage</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Facturation --}}
                @canany(['consulter-proformas', 'consulter-factures', 'consulter-avoirs'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Facturations </strong>
                        </a>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                Facturations
                            </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            @can('consulter-proformas')
                                <li><a href="{{ route('proforma') }}">Proformas</a></li>
                            @endcan
                            @can('consulter-factures')
                                <li><a href="{{ route('facture') }}">Facture</a></li>
                            @endcan
                            @can('consulter-avoirs')
                                <li><a href="{{ route('avoir') }}">Avoir</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                {{-- Reglement --}}
                @canany(['consignation', 'consignation-entree'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Consignation</strong>
                        </a>
                    </li>

                    @can('consignation')
                        <li class="">
                            <a href="{{ route('consignation') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Consignation</strong></a>
                        </li>
                    @endcan

                    @can('consignation-entree')
                        <li class="">
                            <a href="{{ route('consignation_entree') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Consignation Entrée</strong></a>
                        </li>
                    @endcan
                @endcanany
                {{-- Reglement --}}
                @can('consulter-reglement')
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Reglements</strong>
                        </a>
                    </li>


                    <li class="">
                        <a href="{{ route('reglement') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                            <strong>Reglements</strong></a>
                    </li>
                @endcan
                {{-- caisse --}}
                @canany(['voir-liste-categorie-depense', 'voir-liste-categorie-recette', 'voir-liste-depense',
                    'voir-liste-recette', 'voir-caisse'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Caisse</strong>
                        </a>
                    </li>
                @endcanany
                @canany(['voir-liste-categorie-depense', 'voir-liste-categorie-recette'])
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="dripicons-document"></i>
                            <span> Catégorie D/R</span>
                            <span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span>
                        </a>

                        <ul class="list-unstyled">
                            @can('voir-liste-categorie-depense')
                                <li><a href="{{ route('categorie_depense') }}">Catégorie
                                        dépense</a></li>
                            @endcan
                            @can('voir-liste-categorie-recette')
                                <li><a href="{{ route('categorie_recette') }}">Categorie
                                        recette</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @can('voir-liste-depense')
                    <li class="">
                        <a href="{{ route('depense') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                            <strong>Dépenses </strong></a>
                    </li>
                @endcan
                @can('voir-liste-recette')
                    <li class="">
                        <a href="{{ route('recette') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                            <strong>Recette </strong></a>
                    </li>
                @endcan
                @can('voir-caisse')
                    <li class="">
                        <a href="{{ route('caisse_index') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                            <strong>Caisse </strong></a>
                    </li>
                @endcan

                {{-- Statistiques --}}
                @canany(['statistique-vente', 'statistique-achat', 'statistique-stock', 'statistique-marge',
                    'voir-rapport-statistique', 'voir-point-de-vente'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Statistiques</strong>
                        </a>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                Statistiques
                            </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            @can('statistique-achat')
                                <li><a href="{{ route('statistiques') }}">Achat</a></li>
                            @endcan

                            @can('statistique-vente')
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Vente
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        @can('statistique-vente-valeur')
                                            <a class="dropdown-item" href="{{ route('vente') }}">Valeur</a>
                                        @endcan
                                        @can('statistique-vente-quantite')
                                            <a class="dropdown-item" href="{{ route('venteq') }}">Quantite</a>
                                        @endcan
                                    </div>
                                </li>
                            @endcan

                            @can('statistique-stock')
                                <li><a href="{{ route('stocks') }}">Stock</a></li>
                            @endcan

                            @can('statistique-marge')
                                <li><a href="{{ route('marge') }}">Marge</a></li>
                            @endcan
                            @can('voir-rapport-statistique')
                                <li><a href="{{ route('rapport') }}">Rapport</a></li>
                            @endcan
                            @can('voir-point-de-vente')
                                <li><a href="{{ route('pointVente') }}">Point de vente</a></li>
                            @endcan
                            @can('releve-sortie-produit')
                                <li><a href="{{ route('releveSortie') }}">Relévé sortie</a></li>
                            @endcan
                            @can('statistique-rapport-caisse')
                                <li><a href="{{ route('rapportCaisse') }}">Caisse</a></li>
                            @endcan

                        </ul>
                    </li>
                @endcanany


                {{-- Inventaire --}}
                @canany(['consulter-stock-produit', 'consulter-entree-produit', 'consulter-sortie-produit',
                    'consulter-inventaire', 'consulter-transfert-produit'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Inventaire </strong>
                        </a>
                    </li>

                    @canany(['consulter-stock-produit', 'consulter-entree-produit', 'consulter-sortie-produit',
                        'consulter-inventaire'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Gestion
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('consulter-stock-produit')
                                    <li><a href="{{ route('page.stock.stock') }}">Stock</a></li>
                                @endcan

                                @can('consulter-entree-produit')
                                    <li><a href="{{ route('page.entree.entree') }}">Entrées</a></li>
                                @endcan

                                @can('consulter-sortie-produit')
                                    <li><a href="{{ route('page.sortie.sortie') }}">Sorties</a></li>
                                @endcan

                                @can('consulter-inventaire')
                                    <li><a href="{{ route('page.inventaire.inventaire') }}">Inventaires</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @can('consulter-transfert-produit')
                        <li class="">
                            <a href="{{ route('page.transfert.transfert') }}" class="waves-effect">
                                <i class="mdi mdi-cart-plus"></i>
                                <strong>Transferts</strong>
                            </a>
                        </li>
                    @endcan
                @endcanany

                @canany(['voir-inventaire-stock-emballage', 'voir-liste-entree-emballage',
                    'voir-liste-sortie-emballage', 'voir-liste-inventaire', 'transfert-emballage'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Inventaire Emballage </strong>
                        </a>
                    </li>

                    @canany(['voir-inventaire-stock-emballage', 'voir-liste-entree-emballage',
                        'voir-liste-sortie-emballage', 'voir-liste-inventaire'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Gestion Emballage
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('voir-inventaire-stock-emballage')
                                    <li><a href="{{ route('stock_emballage') }}">Stock Emballage</a></li>
                                @endcan

                                @can('voir-liste-entree-emballage')
                                    <li><a href="{{ route('entree_emballage') }}">Entrées Emballages</a></li>
                                @endcan

                                @can('voir-liste-sortie-emballage')
                                    <li><a href="{{ route('sortie_emballage') }}">Sorties Emballages</a></li>
                                @endcan


                                @can('voir-liste-inventaire-emballage')
                                    <li><a href="{{ route('inventaire_emballage') }}">Inventaires Emballages</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany

                    @can('transfert-emballage')
                        {{-- <li class="">
                            <a href="{{ route('transfert_emballage') }}" class="waves-effect">
                                <i class="mdi mdi-cart-plus"></i>
                                <strong>Transferts Emballages</strong>
                            </a>
                        </li> --}}
                    @endcan
                @endcanany

                @canany(['consulter-liste-approvisionnement', 'consulter-liste-reception-approvisionnement',
                    'consulter-liste-acheminenment', 'consulter-liste-reception-acheminenment'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>APPROV & ARCH </strong>
                        </a>
                    </li>
                    @canany(['consulter-liste-approvisionnement', 'consulter-liste-reception-approvisionnement'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Approvisionnement
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('consulter-liste-approvisionnement')
                                    <li><a href="{{ route('approvisionner') }}">Approvisionner</a></li>
                                @endcan
                                @can('consulter-liste-reception-approvisionnement')
                                    <li><a href="{{ route('reception_approvisionnement') }}">Receptionner</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['consulter-liste-acheminenment', 'consulter-liste-reception-acheminenment'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Acheminement
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('consulter-liste-acheminenment')
                                    <li><a href="{{ route('acheminer') }}">Acheminer</a></li>
                                @endcan
                                @can('consulter-liste-reception-acheminenment')
                                    <li><a href="{{ route('reception_acheminement') }}">Receptionner</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                @endcanany

                @canany(['consulter-liste-approvisionnement-emballage',
                    'consulter-liste-reception-approvisionnement-emballage', 'consulter-liste-acheminenment-emballage',
                    'consulter-liste-reception-acheminenment-emballage'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>APPROV & ARCH EMBALLGAGE </strong>
                        </a>
                    </li>
                    @canany(['consulter-liste-approvisionnement-emballage',
                        'consulter-liste-reception-approvisionnement-emballage'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Approvisionnement emballage
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('consulter-liste-approvisionnement-emballage')
                                    <li><a href="{{ route('approvisionner_emballage') }}">Approvisionner emballage</a></li>
                                @endcan
                                @can('consulter-liste-reception-approvisionnement-emballage')
                                    <li><a href="{{ route('reception_approvisionnement_emballage') }}">Receptionner emballage</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                    @canany(['consulter-liste-acheminenment-emballage',
                        'consulter-liste-reception-acheminenment-emballage'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Acheminement emballage
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('consulter-liste-acheminenment-emballage')
                                    <li><a href="{{ route('acheminer_emballage') }}">Acheminer emballage</a></li>
                                @endcan
                                @can('consulter-liste-reception-acheminenment-emballage')
                                    <li><a href="{{ route('reception_acheminement_emballage') }}">Receptionner emballage</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcanany
                @endcanany

                {{-- Administration --}}
                @canany(['consulter-liste-users', 'consulter-liste-groupe', 'droit-acces', 'attribuer-agence'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Administration </strong>
                        </a>
                    </li>

                    @canany(['consulter-liste-users', 'consulter-liste-groupe'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Utilisateur
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="list-unstyled">
                                @can('consulter-liste-groupe')
                                    <li><a href="{{ route('groupe.index') }}">Groupes</a></li>
                                @endcan
                                @can('consulter-liste-users')
                                    <li><a href="{{ route('user.index') }}">Utilisateurs</a></li>
                                @endcan
                                <!-- <li><a href="{{ route('gestion.activites') }}">Centre des activités</a></li> -->
                            </ul>
                        </li>
                    @endcan

                    @canany(['droit-acces', 'attribuer-agence'])
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-document"></i><span>
                                    Affectations
                                </span><span class="menu-arrow float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                @can('attribuer-agence')
                                    <li><a href="{{ route('agenceUtilisateur.index') }}">Agence</a></li>
                                @endcan
                                @can('droit-acces')
                                    <li><a href="{{ route('groupeUser.index') }}">Groupe - Droits d'accès</a></li>
                                @endcan
                            </ul>
                        </li>
                        <li class="">
                            <a href="{{ route('admin.surveillance') }}" class="waves-effect"><i
                                    class="mdi mdi-cart-plus"></i>
                                <strong>Centre de contrôle</strong></a>
                        </li>
                    @endcanany

                    @if (in_array(auth()->user()->id, [1, 3]))
                        <li>
                            <a href="{{ route('maintenance') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Maintenance</strong></a>
                        </li>
                    @endif

                @endcanany

                @canany(['parametres', 'show-liste-facture', 'consulter-liste-agence', 'importation'])
                    <li>
                        <a class="waves-effect text-uppercase text-white" style="{{ background_color_1() }}">
                            <strong>Paramètre </strong>
                        </a>
                    </li>

                    @can('parametres')
                        <li class="">
                            <a href="{{ route('parametre') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Paramètres</strong></a>
                        </li>
                    @endcan

                    @can('voir-taxes')
                        <li class="">
                            <a href="{{ route('taxe') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Taxes</strong></a>
                        </li>
                    @endcan

                    @can('show-liste-facture')
                        <li class="">
                            <a href="{{ route('facture_FF__FLF') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Factures (FF) et lignes de factures (FLF)</strong></a>
                        </li>
                    @endcan

                    @can('consulter-liste-agence')
                        <li class="">
                            <a href="{{ route('agences') }}" class="waves-effect"><i class="mdi mdi-cart-plus"></i>
                                <strong>Agences</strong></a>
                        </li>
                    @endcan

                    @can('importation')
                        <li class="">
                            <a href="{{ route('importation') }}" class="waves-effect"><i
                                    class="fa fa-question"></i><strong>Importation </strong></a>
                        </li>
                    @endcan
                @endcanany
                {{-- <li class="">
                    <a href="{{ route('gestion_des_prix') }}" class="waves-effect"><i class="fa fa-question"></i><strong>Gestion des prix </strong></a>
                </li> --}}
            </ul>
        </div>
        <div class="clearfix"></div>
    </div> <!-- end sidebarinner -->
</div>
