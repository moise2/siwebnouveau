<header id="top-header">
    {{-- Le top-header est visible uniquement sur les écrans "medium" (md) et plus grands --}}
    <div class="container-fluid d-none d-md-flex align-items-center py-2 px-lg-5"
        style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; justify-content: center;">

        {{-- Section du logo et du titre - Alignement ajusté et responsivité améliorée --}}
        <div class="d-flex align-items-center me-auto"> {{-- Utilisation de me-auto pour pousser vers la gauche --}}
            <div class="flex-shrink-0 me-3">
                {{-- Hauteur du logo ajustée pour être plus grande et responsive --}}
                <img src="{{ asset('assets/img/20210406125513_Armoiries_du_Togo__1_-removebg-preview.png') }}"
                    alt="Logo" class="img-fluid" style="height: 65px; max-height: 80px;">
            </div>
            <div class="text-start">
                <small class="mb-0 d-block lh-sm">
                    <strong>{!! __('custom.top_header_title') !!}</strong>
                </small>
            </div>
        </div>

        {{-- Section des boutons, recherche et langue - Alignement ajusté --}}
        <div class="d-flex align-items-center ms-auto"> {{-- Utilisation de ms-auto pour pousser vers la droite --}}
            {{-- Bouton de suivi des réformes --}}
            <div class="me-3">
                <a href="{{ route('login') }}" class="btn btn-danger px-3 py-2 d-flex align-items-center gap-1"
                    style="text-decoration: none; color: white;">
                    <i class="fas fa-chart-line"></i>{{ __('custom.suivi_reform') }}
                </a>
            </div>

            {{-- Icône de recherche --}}
            <div style="margin-left: 1rem; margin-right: 2rem;">
                <a href="{{ route('search.index') }}" class="text-dark"
                    style="color: #6c757d !important; font-size: 1.5rem; font-weight: normal;">
                    <i class="fas fa-search"></i>
                </a>
            </div>

            {{-- Sélecteur de langue --}}
            <div class="lang-switcher">
                <a href="{{ route('change.lang', 'fr') }}"
                    class="fw-bold {{ App::getLocale() == 'fr' ? 'text-danger' : 'text-secondary' }}"
                    style="text-decoration: none;">FR</a>
                <span class="text-secondary"> | </span>
                <a href="{{ route('change.lang', 'en') }}"
                    class="fw-bold {{ App::getLocale() == 'en' ? 'text-danger' : 'text-secondary' }}"
                    style="text-decoration: none;">ENG</a>
            </div>
        </div>
    </div>
</header>

{{-- Votre barre de navigation principale (custom-navbar) --}}
<div class="row">
    <nav id="custom-navbar" class="navbar navbar-expand-lg">
        <div class="container-fluid">
            {{-- Logo blanc sur mobile --}}
            <a class="navbar-brand d-lg-none" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/20210406125513_Armoiries_du_Togo__1_-removebg-previewblanc.png') }}"
                    alt="Logo Mobile" class="img-fluid" style="height: 60px;">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                {{-- Bouton de fermeture pour le mobile --}}
                <div class="d-lg-none text-end pe-3 pt-2">
                    <button type="button" class="btn-close" aria-label="Close"
                        data-bs-toggle="collapse" data-bs-target="#navbarNav"></button>
                </div>

                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">{{ __('custom.accueil') }}</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('qui_sommes_nous') }}"
                            id="navbarDropdown1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('custom.qui_somme_nous') }}
                        </a>
                        <div class="dropdown-menu megamenu" aria-labelledby="navbarDropdown1">
                            {{-- Croix de fermeture pour le sous-menu mobile --}}
                            <button type="button" class="btn-close d-lg-none float-end p-2" aria-label="Close"
                                data-bs-dismiss="dropdown"></button>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>{{ __('custom.mot_de_sp') }}</strong>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('sp') }}">{{ __('custom.mot_de_sp') }}</a></li>
                                    </ul>
                                    <strong>Organisation</strong>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('organigramme') }}">{{ __('custom.organigramme') }}</a>
                                        </li>
                                        <li><a href="{{ route('attributions') }}">{{ __('attributions') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('articles.index') }}"
                            id="navbarDropdown2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('custom.actualites') }}
                        </a>
                        <div class="dropdown-menu megamenu" aria-labelledby="navbarDropdown2">
                            {{-- Croix de fermeture pour le sous-menu mobile --}}
                            <button type="button" class="btn-close d-lg-none float-end p-2" aria-label="Close"
                                data-bs-dismiss="dropdown"></button>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('articles.index') }}">{{ __('custom.actualites') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('articlereformes.index') }}">{{ __('custom.actualites_sur_les_reformes') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('articles.economie_finance.index') }}">{{ __('custom.actualites_sur_les_finances') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('conseilministre.index') }}">{{ __('custom.conseil_ministre') }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('custom.communiques_lois') }}</strong>
                                    <ul class="list-unstyled">
                                        <li><a
                                                href="{{ route('documents.communique_presse.index') }}">{{ __('custom.communiques_de_presse') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('documents.lois_decrets.index') }}">{{ __('custom.lois_decret') }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('custom.avis_et_appels') }}</strong>
                                    <ul class="list-unstyled">
                                        <li><a href="{{ route('documents.ami.index') }}">{{ __('custom.avis') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('documents.rapports_reformes.index') }}"
                            id="navbarDropdown3" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('custom.point_reformes') }}
                        </a>
                        <div class="dropdown-menu megamenu" aria-labelledby="navbarDropdown3">
                            {{-- Croix de fermeture pour le sous-menu mobile --}}
                            <button type="button" class="btn-close d-lg-none float-end p-2" aria-label="Close"
                                data-bs-dismiss="dropdown"></button>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>{{ __('custom.reformes') }}</strong>
                                    <ul class="list-unstyled">
                                        <li><a
                                                href="{{ route('articlereformes.index') }}">{{ __('custom.actualites_reformes') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('documents.rapports_reformes.index') }}">{{ __('custom.rapport_reforme') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown4" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('custom.finances_publiques') }}
                        </a>
                        <div class="dropdown-menu megamenu" aria-labelledby="navbarDropdown4">
                            {{-- Croix de fermeture pour le sous-menu mobile --}}
                            <button type="button" class="btn-close d-lg-none float-end p-2" aria-label="Close"
                                data-bs-dismiss="dropdown"></button>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Rapports prioritaires</strong>
                                    <ul class="list-unstyled">
                                        <li><a
                                                href="{{ route('documents.actifs_financiers.index') }}">{{ __('custom.actifs_financiers') }}</a>
                                        </li>
                                        <ul class="list-unstyled sub-menu">
                                            <strong>{{ __('custom.Budget_Etat') }}</strong>
                                            <ul class="list-unstyled">
                                                <li><a
                                                        href="{{ route('documents.programmation_budgetaire.index') }}">{{ __('custom.Programmation_budgetaire') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.budget_programme.index') }}">{{ __('custom.Budget_programme') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.loi_finances.index') }}">{{ __('custom.Lois_de_finances') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.recettes.index') }}">{{ __('custom.recettes') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.budget_depense.index') }}">{{ __('custom.Depenses') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.budget_vert.index') }}">{{ __('custom.budget_vert') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.budget_citoyen.index') }}">{{ __('custom.Budget_citoyen') }}</a>
                                                </li>
                                                <li><a
                                                        href="{{ route('documents.rapports_execution_budget_etat.index') }}">{{ __('custom.rapport_budget') }}</a>
                                                </li>
                                            </ul>
                                        </ul>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('custom.Autres_rapports') }}</strong>
                                    <ul class="list-unstyled">
                                        <li></li>
                                        <li><a href="{{ route('documents.tofe.index') }}">TOFE</a></li>
                                    </ul>
                                    <strong><a href="{{ route('documents.general.index') }}"
                                            class="btn btn-danger btn-sm rounded-pill"
                                            style="color: white;">{{ __('custom.Tous_les_documents') }}</a></strong>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown4" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">{{ __('custom.Dette_publique') }} </a>
                        <div class="dropdown-menu megamenu" aria-labelledby="navbarDropdown4">
                            {{-- Croix de fermeture pour le sous-menu mobile --}}
                            <button type="button" class="btn-close d-lg-none float-end p-2" aria-label="Close"
                                data-bs-dismiss="dropdown"></button>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><a
                                                href="{{ route('documents.dette_publique.apropos') }}">{{ __('custom.Bulletins_apropos') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('documents.bulletins_statistiques.index') }}">{{ __('custom.Bulletins_statistiques') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('documents.dette_publique.index') }}">{{ __('custom.Rapport_Dette_publique') }}</a>
                                        </li>
                                        <li><a
                                                href="{{ route('documents.strategie_endettement.index') }}">{{ __('custom.strategie_endettement') }}</a>
                                        </li>
                                        <li><a href="{{ route('documents.all_dette.index') }}"
                                                class="btn btn-danger btn-sm rounded-pill"
                                                style="color: white;">{{ __('custom.tous_documents_dette') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('agenda') }}">Agenda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown4" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Liens utiles
                        </a>
                        <div class="dropdown-menu megamenu" aria-labelledby="navbarDropdown4"
                            style="left: auto; right: 0;">
                            {{-- Croix de fermeture pour le sous-menu mobile --}}
                            <button type="button" class="btn-close d-lg-none float-end p-2" aria-label="Close"
                                data-bs-dismiss="dropdown"></button>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Gouvernement</strong>
                                    <ul class="list-unstyled">
                                        <li><a href="https://presidence.gouv.tg"
                                                target="_blank">{{ __('custom.presidence') }}</a></li>
                                        <li><a href="https://primature.gouv.tg"
                                                target="_blank">{{ __('custom.Primature') }}</a></li>
                                        <li><a href="https://assemblee-nationale.tg"
                                                target="_blank">{{ 'custom.assemblee' }}</a></li>
                                    </ul>
                                    <br>
                                    <br>
                                    <ul>

                                    </ul>

                                    <strong>Ministères | Institutions | Agences</strong>
                                    <ul class="list-unstyled">
                                        <li><a href="https://finances.gouv.tg"
                                                target="_blank">{{ __('custom.economie') }}</a></li>
                                        <li><a href="https://www.togo-port.net/"
                                                target="_blank">{{ __('custom.port_autonome_lome') }}</a></li>
                                        <li><a href="https://www.aeroportdelome.com/"
                                                target="_blank">{{ __('custom.aeroport_gnassingbe_eyadema') }}</a>
                                        </li>
                                        <li><a href="https://pia-togo.com/fr/" target="_blank">{{ __('custom.pia') }}</a>
                                        </li>
                                        <li><a href="https://arcep.tg/" target="_blank">{{ __('custom.arcep') }}</a>
                                        </li>
                                        <li><a href="https://www.imf.org/fr/Home"
                                                target="_blank">{{ __('custom.fmi') }}</a></li>
                                        <li><a href="https://www.banquemondiale.org/ext/fr/home"
                                                target="_blank">{{ __('custom.bm') }}</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">

                                    <ul class="list-unstyled">
                                        <li><a href="https://www.afdb.org/fr"
                                                target="_blank">{{ __('custom.bad') }}</a></li>
                                        <li><a href="https://european-union.europa.eu/index_fr"
                                                target="_blank">{{ __('custom.ue') }}</a></li>
                                        <li><a href="https://inseed.tg/" target="_blank">{{ __('custom.inseed') }}</a>
                                        </li>
                                        <li><a href="https://togoinvest.tg"
                                                target="_blank">{{ __('custom.togo_invest') }}</a></li>
                                        <li><a href="https://finances.gouv.tg/domaine/tresor/"
                                                target="_blank">{{ __('custom.tresor') }}</a></li>
                                        <li><a href="https://www.umoatitres.org"
                                                target="_blank">{{ __('custom.umoa_titres') }}</a></li>
                                        <li><a href="https://www.bceao.int" target="_blank">{{ __('custom.bceao') }}</a>
                                        </li>
                                        <li><a href="https://www.ecowas.int"
                                                target="_blank">{{ __('custom.cedeao') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>