@extends('frontend.layouts.page', [
    'pageTitle' => 'Attributions',
    'pageId' => 'articles'
])

@section('page-content')

    {{-- Titre de la page --}}
    @section('titre_page_contenu', 'Attributions')

    {{-- Contenu principal --}}
    <p>Conformément au décret N°2017-112 /PR du 29 septembre 2017, le Secrétariat Permanent est chargé de :</p>
    <ul>
        <li>Coordonner la mise en œuvre et le suivi des politiques et programmes financiers, notamment celles inscrites dans les conventions signées avec les institutions de Bretton Woods et les autres partenaires techniques et financiers.</li>
        <li>Exploiter, analyser et diffuser l’ensemble des données et informations nécessaires au suivi des politiques de réformes et des programmes financiers.</li>
        <li>Préparer et gérer les programmes économiques, financiers et d’appui budgétaire du Togo avec les institutions partenaires.</li>
        <li>Contribuer à assurer la cohérence et la complémentarité des actions programmées dans le cadre des plans sectoriels ou régionaux avec la politique nationale de développement.</li>
        <li>Organiser et coordonner, dans le cadre des différents appuis budgétaires, les négociations et les revues avec les partenaires techniques et financiers.</li>
        <li>Coordonner la préparation et la mise en œuvre des documents référentiels de politique économique et de dialogue du gouvernement avec les partenaires techniques et financiers.</li>
        <li>Suivre la mise en œuvre des réformes structurelles, en particulier celles relatives au respect des engagements internationaux du Togo.</li>
        <li>Veiller à ce que les réformes structurelles améliorent effectivement les performances de l’économie nationale et inscrivent le Togo sur la voie de l’émergence économique.</li>
        <li>Appuyer les ministères sectoriels dans la consommation des ressources financières pour atteindre les résultats définis dans le cadre des appuis budgétaires.</li>
        <li>Coordonner, en liaison avec le groupe de coordination des partenaires techniques et financiers, la mise en œuvre de la déclaration de Paris sur l’efficacité de l’aide.</li>
        <li>Organiser et suivre les missions d’appui au système de gestion des finances publiques, en veillant à l’intégration ou à la bonne articulation des recommandations avec le plan d’actions pour l’amélioration de la gestion des finances publiques, ainsi qu’à leur mise en œuvre.</li>
        <li>Rechercher, en relation avec les structures compétentes, les financements nécessaires à la mise en œuvre des actions de réforme.</li>
    </ul>

    {{-- Inclure la carte de contenu --}}
    <!-- @include('frontend.components.content-card') -->

@endsection
