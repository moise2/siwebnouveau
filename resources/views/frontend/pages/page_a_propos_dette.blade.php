@extends('frontend.layouts.page', [
    'pageTitle' => 'À propos de la Direction de la Gestion et du Financement de la Dette Publique',
    'pageId' => 'articles'
])

@section('page-content')

<section class="container my-5">
    <div class="row">
        <!-- Colonne gauche : Présentation -->
        <div class="col-lg-8 mb-4">
            <h1 class="mb-4 display-5 fw-bold">
                {{ __('À propos de la Direction de la Gestion et du Financement de la Dette Publique') }}
            </h1>
            <p class="lead text-justify">
                La Direction de la Gestion et du Financement de la Dette Publique (DDPF) joue un rôle central dans la mise en œuvre de la stratégie d’endettement public de la République Togolaise. Elle assure une gestion efficiente, transparente et responsable de la dette pour soutenir le développement national et la stabilité économique.
            </p>

            <h2 class="mt-5 mb-3 h4">{{ __('Missions principales') }}</h2>
            <ul class="list-unstyled">
                <li class="mb-3">
                    <i class="fas fa-check-circle text-danger me-2"></i>
                    <strong>Mobilisation des ressources :</strong> Obtenir des financements tant sur le plan national qu’international.
                </li>
                <li class="mb-3">
                    <i class="fas fa-check-circle text-danger me-2"></i>
                    <strong>Relations investisseurs :</strong> Gérer les partenariats avec les institutions financières et les marchés régionaux.
                </li>
                <li class="mb-3">
                    <i class="fas fa-check-circle text-danger me-2"></i>
                    <strong>Stratégie et viabilité de la dette :</strong> Élaborer et exécuter des stratégies d’endettement public solides.
                </li>
                <li class="mb-3">
                    <i class="fas fa-check-circle text-danger me-2"></i>
                    <strong>Suivi du service de la dette :</strong> Garantir le paiement régulier et le bon service de la dette publique.
                </li>
                <li class="mb-3">
                    <i class="fas fa-check-circle text-danger me-2"></i>
                    <strong>Garanties d’emprunt :</strong> Superviser les emprunts rétrocédés, garantis ou avalisés.
                </li>
            </ul>
        </div>

        <!-- Colonne droite : Contacts -->
        <div class="col-lg-4">
            <div class="card shadow-sm p-4">
                <h3 class="h5 mb-3 text-danger">{{ __('Informations de contact') }}</h3>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                        <strong>Adresse :</strong><br>
                        Quartier administratif, Ministère de l’Économie et des Finances,<br>
                        Immeuble CASEF, Rue de l’Indépendance, Lomé – Togo
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope text-danger me-2"></i>
                        <strong>Email :</strong> 
                        <a href="mailto:dette@tresorpublic.gouv.tg" class="text-danger">
                            dette@tresorpublic.gouv.tg
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone-alt text-danger me-2"></i>
                        <strong>Téléphone :</strong> (00228) 22 21 02 29
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-mail-bulk text-danger me-2"></i>
                        <strong>BP :</strong> 387, Lomé – Togo
                    </li>
                </ul>

                <!-- Bouton de consultation -->
                <div class="mt-4 text-center">
                    <a href="{{ route('documents.all_dette.index') }}" class="btn btn-danger w-100">
                        <i class="fas fa-file-alt me-2"></i>
                        {{ __('Consulter les documents sur la dette publique') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
