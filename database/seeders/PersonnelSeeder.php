<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PersonnelSeeder extends Seeder
{
    /**
     * Seed personnel data from the actual Excel file.
     * Colonnes: Nom, Prenom, Email, Telephone IP, Poste, Entite, Date embauche, Responsable Hiérarchique
     */
    public function run(): void
    {
        $employeRole = Role::where('slug', 'collaborateur')->first();
        $managerRole = Role::where('slug', 'manager')->first();

        // Données extraites du fichier CSV modele_import_personel_sgpme.csv
        $personnel = [
            ['nom' => 'YAO Epse KOUASSI', 'prenom' => 'N\'guessan Joelle', 'email' => 'joelle.kouassi@sgpme.ci', 'telephone' => '', 'poste' => 'Directrice Générale', 'entite' => 'DG', 'date_embauche' => '01/05/2023', 'responsable' => ''],
            ['nom' => 'HIEN', 'prenom' => 'Oho Pennina', 'email' => 'pennina.hien@sgpme.ci', 'telephone' => '', 'poste' => 'Assistante de Direction', 'entite' => 'DG', 'date_embauche' => '01/05/2023', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'DAO Epse DAGNOGO', 'prenom' => 'Rahmata', 'email' => 'rahmata.dagnogo@sgpme.ci', 'telephone' => '', 'poste' => 'Directeur Administration et Ressources', 'entite' => 'DAR', 'date_embauche' => '18/08/2025', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'DIARRASSOUBA', 'prenom' => 'Abain Aissata', 'email' => 'aissata.diarrassouba@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable des Moyens Généraux', 'entite' => 'MG', 'date_embauche' => '01/07/2023', 'responsable' => 'rahmata.dagnogo@sgpme.ci'],
            ['nom' => 'DJE', 'prenom' => 'Komenan Ehui', 'email' => 'dje.komenan@sgpme.ci', 'telephone' => '', 'poste' => 'Agent de Liaison', 'entite' => 'MG', 'date_embauche' => '01/05/2023', 'responsable' => 'aissata.diarrassouba@sgpme.ci'],
            ['nom' => 'DIOMANDE', 'prenom' => 'Moha', 'email' => 'moha.diomande@sgpme.ci', 'telephone' => '', 'poste' => 'Assistante Moyens Généraux', 'entite' => 'MG', 'date_embauche' => '01/07/2023', 'responsable' => 'aissata.diarrassouba@sgpme.ci'],
            ['nom' => 'BRAWA', 'prenom' => 'Agbedje Datte', 'email' => 'benjamin.brawa@sgpme.ci', 'telephone' => '', 'poste' => 'Directeur Otrois et Engagements', 'entite' => 'DOE', 'date_embauche' => '01/07/2023', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'KOUASSI', 'prenom' => 'Kouadio Jean Claude', 'email' => 'jean-claude.kouassi@sgpme.ci', 'telephone' => '', 'poste' => 'Directeur des Risques, de la Conformité et du Contrôle Permanent', 'entite' => 'DR', 'date_embauche' => '01/12/2023', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'CISSE', 'prenom' => 'Hamed', 'email' => 'hamed.cisse@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Gestion des Risques et Contrôle Interne', 'entite' => 'GRCP', 'date_embauche' => '02/09/2024', 'responsable' => 'jean-claude.kouassi@sgpme.ci'],
            ['nom' => 'KOUAKOU', 'prenom' => 'Nadege Ghyslaine', 'email' => 'ghyslaine.kouakou@sgpme.ci', 'telephone' => '', 'poste' => 'Contrôleur permanent', 'entite' => 'GRCP', 'date_embauche' => '01/07/2023', 'responsable' => 'hamed.cisse@sgpme.ci'],
            ['nom' => 'OKOUBO', 'prenom' => 'Amy Marlaine', 'email' => 'stephanie.okoubo@sgpme.ci', 'telephone' => '', 'poste' => 'Analyste engagements', 'entite' => 'DOE', 'date_embauche' => '01/07/2023', 'responsable' => 'benjamin.brawa@sgpme.ci'],
            ['nom' => 'KOUAKOU', 'prenom' => 'Cynthia Armande', 'email' => 'cynthia.kouakou@sgpme.ci', 'telephone' => '', 'poste' => 'Comptable', 'entite' => 'FC', 'date_embauche' => '01/07/2023', 'responsable' => 'rahmata.dagnogo@sgpme.ci'],
            ['nom' => 'DIABY', 'prenom' => 'Abdel Ousmane', 'email' => 'abdel.diaby@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé du suivi des engagements', 'entite' => 'DOE', 'date_embauche' => '01/07/2023', 'responsable' => 'benjamin.brawa@sgpme.ci'],
            ['nom' => 'AFENI Epse', 'prenom' => 'Anna Laetitia Marine', 'email' => 'laetitia.koffi@sgpme.ci', 'telephone' => '', 'poste' => 'Directrice Commerciale', 'entite' => 'DC', 'date_embauche' => '01/07/2023', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'KOUAME', 'prenom' => 'Adjoua Diane Marie', 'email' => 'diane.kouame@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée de Clientèle', 'entite' => 'DC', 'date_embauche' => '01/07/2023', 'responsable' => 'laetitia.koffi@sgpme.ci'],
            ['nom' => 'KOUADIO', 'prenom' => 'Amoin Edith Ferdy', 'email' => 'edith.kouadio@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée d\'Accueil', 'entite' => 'MG', 'date_embauche' => '01/07/2023', 'responsable' => 'aissata.diarrassouba@sgpme.ci'],
            ['nom' => 'SEHI Epse ANET', 'prenom' => 'Anceany Sophia', 'email' => 'anceany.anet@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable des Ressources Humaines', 'entite' => 'RH', 'date_embauche' => '28/08/2023', 'responsable' => 'rahmata.dagnogo@sgpme.ci'],
            ['nom' => 'SONZAHI', 'prenom' => 'Bioh Dji Fabrice', 'email' => 'fabrice.sonzahi@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Systèmes d\'information', 'entite' => 'SI', 'date_embauche' => '01/07/2023', 'responsable' => 'rahmata.dagnogo@sgpme.ci'],
            ['nom' => 'NAMESSI', 'prenom' => 'Hoademeno', 'email' => 'richarde.namessi@sgpme.ci', 'telephone' => '', 'poste' => 'Assistante de Direction et chargée des ressources humaines', 'entite' => 'RH', 'date_embauche' => '01/07/2023', 'responsable' => 'anceany.anet@sgpme.ci'],
            ['nom' => 'YAO', 'prenom' => 'Namoin Jessica Marie', 'email' => 'jessica.yao@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée Marketing et Communication', 'entite' => 'DG', 'date_embauche' => '04/09/2023', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'AMANLAMAN', 'prenom' => 'Animan Christine', 'email' => 'christine.aka@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Juridique et Contentieux', 'entite' => 'DG', 'date_embauche' => '18/09/2023', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'SUINI', 'prenom' => 'Herve', 'email' => 'herve.suini@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable des Finances et de Comptabilité', 'entite' => 'FC', 'date_embauche' => '23/10/2023', 'responsable' => 'rahmata.dagnogo@sgpme.ci'],
            ['nom' => 'DAGBO', 'prenom' => 'Aissa Grace', 'email' => 'aissa.dagbo@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé RSE Sénior', 'entite' => 'RSE', 'date_embauche' => '26/10/2023', 'responsable' => 'jean-claude.kouassi@sgpme.ci'],
            ['nom' => 'OUATTARA', 'prenom' => 'Roger', 'email' => 'roger.ouattara@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Audit Interne', 'entite' => 'DG', 'date_embauche' => '01/03/2024', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'APPOH', 'prenom' => 'Thomas Rodrigue', 'email' => 'rodrigue.appoh@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé d\'Affaires', 'entite' => 'DC', 'date_embauche' => '01/08/2024', 'responsable' => 'laetitia.koffi@sgpme.ci'],
            ['nom' => 'DOGNE', 'prenom' => 'N\'guessan', 'email' => 'luc.dogne@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé de reseau et de sécurité informatique', 'entite' => 'SI', 'date_embauche' => '02/09/2024', 'responsable' => 'fabrice.sonzahi@sgpme.ci'],
            ['nom' => 'KOUASSI', 'prenom' => 'Serge Didier Frejus', 'email' => 'didier.kouassi@sgpme.ci', 'telephone' => '', 'poste' => 'Analyste Credit', 'entite' => 'DOE', 'date_embauche' => '03/03/2025', 'responsable' => 'benjamin.brawa@sgpme.ci'],
            ['nom' => 'KONAN', 'prenom' => 'Carine Laurinda', 'email' => 'laurinda.konan@sgpme.ci', 'telephone' => '', 'poste' => 'Contrôleur de Gestion', 'entite' => 'FC', 'date_embauche' => '01/04/2025', 'responsable' => 'rahmata.dagnogo@sgpme.ci'],
            ['nom' => 'ZABAVY', 'prenom' => 'Marie-suzette', 'email' => 'marie-suzette.zabavy@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée de conformité', 'entite' => 'GRCP', 'date_embauche' => '03/11/2025', 'responsable' => 'jean-claude.kouassi@sgpme.ci'],
            ['nom' => 'COULIBALY', 'prenom' => 'Allhassane Simon', 'email' => 'allhassane.coulibaly@sgpme.ci', 'telephone' => '', 'poste' => 'Tech Lead Applicatif', 'entite' => 'SI', 'date_embauche' => '15/12/2025', 'responsable' => 'fabrice.sonzahi@sgpme.ci'],
            ['nom' => 'KAMARA', 'prenom' => 'Raissa Manuella', 'email' => 'raissa.kamara@sgpme.ci', 'telephone' => '', 'poste' => 'Juriste', 'entite' => 'DG', 'date_embauche' => '02/01/2026', 'responsable' => 'joelle.kouassi@sgpme.ci'],
            ['nom' => 'OURA', 'prenom' => 'N\'da Leonce', 'email' => 'leonce.oura@sgpme.ci', 'telephone' => '', 'poste' => 'Auditeur Interne', 'entite' => 'DG', 'date_embauche' => '02/02/2026', 'responsable' => 'joelle.kouassi@sgpme.ci'],
        ];

        foreach ($personnel as $person) {
            // Skip if email is empty or user already exists
            if (empty($person['email']) || User::where('email', $person['email'])->exists()) {
                continue;
            }

            // Find entity by acronym
            $entity = null;
            if (!empty($person['entite'])) {
                $entity = Entity::where('acronym', trim($person['entite']))->first();
            }

            // Determine role based on position
            // Par défaut, tous les utilisateurs ont le rôle 'Collaborateur' (employe)
            // Sauf les Directeurs et Responsables qui ont le rôle 'Manager'
            $role = $employeRole; // Collaborateur par défaut
            $positionLower = strtolower($person['poste']);
            if (str_contains($positionLower, 'directeur') || str_contains($positionLower, 'directrice') || 
                str_contains($positionLower, 'responsable')) {
                $role = $managerRole; // Manager pour les postes de direction
            }

            // Find supervisor by email
            // Si le responsable hiérarchique est identique à l'email de la personne, elle n'a pas de superviseur
            $supervisor = null;
            if (!empty($person['responsable'])) {
                // Si le responsable est différent de l'email de la personne elle-même
                if (trim($person['responsable']) !== trim($person['email'])) {
                    // Chercher le superviseur par email parmi les utilisateurs déjà créés
                    $supervisor = User::where('email', trim($person['responsable']))->first();
                }
            }

            // Parse hire date
            $hireDate = null;
            if (!empty($person['date_embauche'])) {
                try {
                    $hireDate = \Carbon\Carbon::createFromFormat('d/m/Y', $person['date_embauche'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $hireDate = null;
                }
            }

            User::create([
                'first_name' => $person['prenom'],
                'last_name' => $person['nom'],
                'full_name' => $person['nom'] . ' ' . $person['prenom'],
                'email' => $person['email'],
                'avatar' => "users/default.jpg",
                'password' => Hash::make('password'),
                'phone' => $person['telephone'] ?? '',
                'position' => $person['poste'],
                'hire_date' => $hireDate,
                'entity_uuid' => $entity?->uuid,
                'supervisor_uuid' => $supervisor?->uuid,
                'role_uuid' => $role?->uuid,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
