@extends('frontend.layouts.page', [
    'pageTitle' => 'Qui sommes-nous ?',
    'pageId' => 'articles'
])

@section('page-content')

    {{-- Contenu principal --}}
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <h1 class="text-center mb-4">Qui sommes-nous ?</h1>
                <div class="card border-0 rounded-3">
                    <div class="card-body p-4">
                        <p class="card-text">
                            <strong>Le Secrétariat Permanent pour le Suivi des Réformes et des Programmes Financiers</strong> est un organe créé par le gouvernement pour garantir la mise en œuvre efficace des réformes et des programmes financiers. Il a pour mission de développer et de superviser les activités en lien avec ces réformes et programmes, en collaboration avec les différentes parties prenantes.
                        </p>
                        <p class="card-text">
                            Son fonctionnement repose sur une approche collaborative qui implique la participation active des ministères, institutions publiques, partenaires au développement, organisations de la société civile et du secteur privé. Cette approche assure que les réformes et programmes financiers répondent aux besoins réels de la population tout en étant en phase avec les objectifs de développement du pays.
                        </p>
                        <p class="card-text">
                            Dirigé par un Secrétaire Permanent, le Secrétariat supervise une équipe de professionnels dans les domaines de l’économie, des finances et de la gestion des projets. Ils sont chargés de la planification, mise en œuvre et évaluation des réformes et programmes financiers. Le Secrétariat est également doté d’un système de suivi et d’évaluation qui mesure l’impact de ces initiatives sur la population, grâce à des données fiables et mises à jour régulièrement.
                        </p>
                        <p class="card-text">
                            En résumé, le Secrétariat Permanent est un acteur clé dans la réussite des réformes et des programmes financiers du pays. Son approche collaborative, son organisation efficace et son système de suivi en font un outil essentiel pour la mise en œuvre de la politique de développement du Togo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inclure les composants supplémentaires --}}

@endsection
