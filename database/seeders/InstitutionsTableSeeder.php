<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class InstitutionsTableSeeder extends Seeder
{
    public function run()
    {
        $institutions = [
            'Ministère des ressources halieutiques, animales et de la réglementation de la transhumance',
            'Ministère de l’agriculture et de l’hydraulique villageoise et du développement rural',
            'Ministère de la Réforme du service public, du travail et du dialogue social',
            'Ministère de l’administration territoriale, de la décentralisation et de la chefferie coutumière',
            'Ministère des Travaux Publics et des Infrastructures',
            'Ministère de l\'Eau et de l’assainissement',
            'Ministère de l’Action Sociale, de la Solidarité et de la Promotion de la Femme',
            'Ministère de la Justice et de la Législation',
            'Ministère de l’Enseignement Supérieur et de la Recherche',
            'Ministère des Enseignement primaire et secondaire',
            'Ministère de l’Enseignement Technique, de la Formation Professionnelle et de l\'Apprentissage',
            'Ministère des droits de l’homme, de la formation à la citoyenneté et des relations avec les institutions de la République',
            'Ministère du Commerce, de l\'Artisanat et de la Consommation Locale',
            'Ministère de la communication et des médias, de la culture, porte-parole du gouvernement',
            'Ministère des Mines et des Ressources Energétiques',
            'Ministère des Sports et des Loisirs',
            'Ministère de la Santé et de l’Hygiène Publique',
            'Ministère des Transports des transports terrestre, aérien et ferroviaire',
            'Ministère de l’industrie et de la Promotion de l\'Investissement',
            'Ministère de l’Economie Maritime et de la Protection Côtière',
            'Ministère de la Planification, du Développement et de la Coopération',
            'Ministère de l’Urbanisme et de la réforme foncière',
            'Ministère de l’Environnement et des Ressources Forestières',
            'Ministère des Affaires Etrangères, de l\'Intégration Régionale et des Togolais de l’Extérieur',
            'Ministère du développement à la base, de l’inclusion financière, de la jeunesse et de l’emploi des jeunes',
            'Ministère de la Sécurité et de la Protection Civile',
            'Ministère de l’accès aux soins universels et de la couverture sanitaire',
            'Ministère du Désenclavement et des Pistes Rurales',
            'Ministère de l’économie numérique et de la transformation digitale',
            'Ministère de l’aménagement et du développement',
            'Ministère délégué auprès du ministre des Mines et des ressources énergétiques',
            'Ministère délégué auprès du ministre de la sécurité et de la protection civile',
            'Ministère délégué auprès du ministre du commerce, de l’artisanat et de la consommation Locale',
            'Ministère délégué auprès du ministre du développement à la base, de l’inclusion financière, de la jeunesse, de l’emploi des jeunes',
            'Ministère de la Culture et du Tourisme',
            'Cour des Comptes',
            'Office Togolais des Recettes',
            'Inspection Générale des Finances',
            'Inspection Générale d’Etat',
            'Direction Nationale du Contrôle Financier',
            'Direction Générale du Trésor et de la Comptabilité Publique',
            'Direction de la Dette publique et du Financement',
            'Direction Générale du Budget et des Finances',
            'Direction des Finances',
            'Direction Nationale du Contrôle de la Commande Publique',
            'Autorité de Régulation de la Commande Publique',
            'Direction Générale des Etudes et Analyses Economiques',
            'Cellule Informatique'
        ];

        foreach ($institutions as $institution) {
            DB::table('institutions')->insert([
                'libelle' => $institution,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
