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

        // Données réelles du fichier Excel modele_import_personel_sgpme_renseigne.xlsx
        $personnel = [
            ['nom' => 'KOUASSI', 'prenom' => 'N\'Guessan Joelle', 'email' => 'joelle.kouassi@sgpme.ci', 'telephone' => '', 'poste' => 'Directrice Générale', 'entite' => 'Direction Générale', 'date_embauche' => '01/05/2023', 'responsable' => ''],
            ['nom' => 'HIEN', 'prenom' => 'Oho Pennina', 'email' => 'pennina.hien@sgpme.ci', 'telephone' => '', 'poste' => 'Assistante de Direction', 'entite' => 'Direction Générale', 'date_embauche' => '01/05/2023', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'DJE', 'prenom' => 'Komenan Ehui', 'email' => 'dje.komenan@sgpme.ci', 'telephone' => '', 'poste' => 'Agent de Liaison', 'entite' => 'Direction Administration et Ressources / Moyens Généraux', 'date_embauche' => '01/05/2023', 'responsable' => 'DIARRASSOUBA Abain Aissata'],
            ['nom' => 'DIOMANDE', 'prenom' => 'Moha', 'email' => 'moha.diomande@sgpme.ci', 'telephone' => '', 'poste' => 'Assistante Moyens Généraux', 'entite' => 'Direction Administration et Ressources / Moyens Généraux', 'date_embauche' => '01/07/2023', 'responsable' => 'DIARRASSOUBA Abain Aissata'],
            ['nom' => 'DIARRASSOUBA', 'prenom' => 'Abain Aissata', 'email' => 'aissata.diarrassouba@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable des Moyens Généraux', 'entite' => 'Direction Administration et Ressources / Moyens Généraux', 'date_embauche' => '01/07/2023', 'responsable' => 'DAGNOGO Rahmata'],
            ['nom' => 'BRAWA', 'prenom' => 'Agbedje Datte', 'email' => 'benjamin.brawa@sgpme.ci', 'telephone' => '', 'poste' => 'Directeur Otrois et Engagements', 'entite' => 'Direction Générale', 'date_embauche' => '01/07/2023', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'KOUAKOU', 'prenom' => 'Nadege Ghyslaine', 'email' => 'ghyslaine.kouakou@sgpme.ci', 'telephone' => '', 'poste' => 'Contrôleur permanent', 'entite' => 'Direction des Risques / Gestion des risques et contrôle permanent', 'date_embauche' => '01/07/2023', 'responsable' => 'CISSE Hamed'],
            ['nom' => 'OKOUBO', 'prenom' => 'Amy Marlaine', 'email' => 'stephanie.okoubo@sgpme.ci', 'telephone' => '', 'poste' => 'Analyste engagements', 'entite' => 'Direction Octrois et Engagements', 'date_embauche' => '01/07/2023', 'responsable' => 'BRAWA Agbedje Datte'],
            ['nom' => 'KOUAKOU', 'prenom' => 'Cynthia Armande', 'email' => 'cynthia.kouakou@sgpme.ci', 'telephone' => '', 'poste' => 'Comptable', 'entite' => 'Direction Administration et Ressources / Finances & Comptabilité', 'date_embauche' => '01/07/2023', 'responsable' => 'SUINI Herve'],
            ['nom' => 'DIABY', 'prenom' => 'Abdel Ousmane', 'email' => 'abdel.diaby@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé du suivi des engagements', 'entite' => 'Direction Octrois et Engagements', 'date_embauche' => '01/07/2023', 'responsable' => 'BRAWA Agbedje Datte'],
            ['nom' => 'KOFFI', 'prenom' => 'Anna Laetitia Marine', 'email' => 'laetitia.koffi@sgpme.ci', 'telephone' => '', 'poste' => 'Directrice Commerciale', 'entite' => 'Direction Générale', 'date_embauche' => '01/07/2023', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'KOUAME', 'prenom' => 'Adjoua Diane Marie', 'email' => 'diane.kouame@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée de Clientèle', 'entite' => 'Direction Commerciale', 'date_embauche' => '01/07/2023', 'responsable' => 'KOFFI Anna Laetitia Marine'],
            ['nom' => 'KOUADIO', 'prenom' => 'Amoin Edith Ferdy', 'email' => 'edith.kouadio@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée d\'Accueil', 'entite' => 'Direction Administration et Ressources / Moyens Généraux', 'date_embauche' => '01/07/2023', 'responsable' => 'DIARRASSOUBA Abain Aissata'],
            ['nom' => 'SONZAHI', 'prenom' => 'Bioh Dji Fabrice', 'email' => 'fabrice.sonzahi@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Systèmes d\'information', 'entite' => 'Direction Administration et Ressources / Systèmes d\'Information', 'date_embauche' => '01/07/2023', 'responsable' => 'DAGNOGO Rahmata'],
            ['nom' => 'NAMESSI', 'prenom' => 'Hoademeno', 'email' => 'richarde.namessi@sgpme.ci', 'telephone' => '', 'poste' => 'Assistante de Direction et chargée des ressources humaines', 'entite' => 'Direction Administration et Ressources / Ressources Humaines', 'date_embauche' => '01/07/2023', 'responsable' => 'ANET Anceany Sophia'],
            ['nom' => 'ANET', 'prenom' => 'Anceany Sophia', 'email' => 'anceany.anet@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable des Ressources Humaines', 'entite' => 'Direction Administration et Ressources / Ressources Humaines', 'date_embauche' => '28/08/2023', 'responsable' => 'DAGNOGO Rahmata'],
            ['nom' => 'YAO', 'prenom' => 'Namoin Jessica Marie', 'email' => 'jessica.yao@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée Marketing et Communication', 'entite' => 'Direction Générale', 'date_embauche' => '04/09/2023', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'AMANLAMAN', 'prenom' => 'Animan Christine', 'email' => 'christine.aka@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Juridique et Contentieux', 'entite' => 'Direction Générale', 'date_embauche' => '18/09/2023', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'SUINI', 'prenom' => 'Herve', 'email' => 'herve.suini@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable des Finances et de Comptabilité', 'entite' => 'Direction Administration et Ressources/ Finances & Comptabilité', 'date_embauche' => '23/10/2023', 'responsable' => 'DAGNOGO Rahmata'],
            ['nom' => 'DAGBO', 'prenom' => 'Aissa Grace', 'email' => 'aissa.dagbo@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé RSE Sénior', 'entite' => 'Direction des Risques / RSE', 'date_embauche' => '26/10/2023', 'responsable' => 'KOUASSI Kouadio Jean Claude'],
            ['nom' => 'KOUASSI', 'prenom' => 'Kouadio Jean Claude', 'email' => 'jean-claude.kouassi@sgpme.ci', 'telephone' => '', 'poste' => 'Directeur des Risques, de la Conformité et du Contrôle Permanent', 'entite' => 'Direction Générale', 'date_embauche' => '01/12/2023', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'OUATTARA', 'prenom' => 'Roger', 'email' => 'roger.ouattara@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Audit Interne', 'entite' => 'Direction Générale', 'date_embauche' => '01/03/2024', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'KIE', 'prenom' => 'Bi Foua Jean-Paul', 'email' => '', 'telephone' => '', 'poste' => 'Chauffeur', 'entite' => 'Direction Administration et Ressources / Moyens Généraux', 'date_embauche' => '01/07/2024', 'responsable' => 'DIARRASSOUBA Abain Aissata'],
            ['nom' => 'APPOH', 'prenom' => 'Thomas Rodrigue', 'email' => 'rodrigue.appoh@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé d\'Affaires', 'entite' => 'Direction Commerciale', 'date_embauche' => '01/08/2024', 'responsable' => 'KOFFI Anna Laetitia Marine'],
            ['nom' => 'DOGNE', 'prenom' => 'N\'Guessan', 'email' => 'luc.dogne@sgpme.ci', 'telephone' => '', 'poste' => 'Chargé de reseau et de sécurité informatique', 'entite' => 'Direction Administration et Ressources / Systèmes d\'Information', 'date_embauche' => '02/09/2024', 'responsable' => 'SONZAHI Bioh Dji Fabrice'],
            ['nom' => 'CISSE', 'prenom' => 'Hamed', 'email' => 'hamed.cisse@sgpme.ci', 'telephone' => '', 'poste' => 'Responsable Gestion des Risques et Contrôle Interne', 'entite' => 'Direction des Risques / Gestion des risques et contrôle permanent', 'date_embauche' => '02/09/2024', 'responsable' => 'KOUASSI Kouadio Jean Claude'],
            ['nom' => 'KOUASSI', 'prenom' => 'Serge Didier Frejus', 'email' => 'didier.kouassi@sgpme.ci', 'telephone' => '', 'poste' => 'Analyste Credit', 'entite' => 'Direction Octrois et Engagements', 'date_embauche' => '03/03/2025', 'responsable' => 'BRAWA Agbedje Datte'],
            ['nom' => 'KONAN', 'prenom' => 'Carine Laurinda', 'email' => 'laurinda.konan@sgpme.ci', 'telephone' => '', 'poste' => 'Contrôleur de Gestion', 'entite' => 'Direction Administration et Ressources / Finances & Comptabilité', 'date_embauche' => '01/04/2025', 'responsable' => 'SUINI Herve'],
            ['nom' => 'DAGNOGO', 'prenom' => 'Rahmata', 'email' => 'rahmata.dagnogo@sgpme.ci', 'telephone' => '', 'poste' => 'Directeur Administration et Ressources', 'entite' => 'Direction Générale', 'date_embauche' => '18/08/2025', 'responsable' => 'KOUASSI N\'Guessan Joelle'],
            ['nom' => 'ZABAVY', 'prenom' => 'Marie-Suzette', 'email' => 'marie-suzette.zabavy@sgpme.ci', 'telephone' => '', 'poste' => 'Chargée de conformité', 'entite' => 'Direction des Risques / Conformité', 'date_embauche' => '03/11/2025', 'responsable' => 'KOUASSI Kouadio Jean Claude'],
            ['nom' => 'COULIBALY', 'prenom' => 'Allhassane Simon', 'email' => 'allhassane.coulibaly@sgpme.ci', 'telephone' => '', 'poste' => 'Tech Lead Applicatif', 'entite' => 'Direction Administration et Ressources / Systèmes d\'Information', 'date_embauche' => '15/12/2025', 'responsable' => 'SONZAHI Bioh Dji Fabrice'],
            ['nom' => 'KAMARA', 'prenom' => 'Raissa Manuella', 'email' => 'raissa.kamara@sgpme.ci', 'telephone' => '', 'poste' => 'Juriste', 'entite' => 'Direction Générale / Juridique & Contentieux', 'date_embauche' => '02/01/2026', 'responsable' => 'AMANLAMAN Animan Christine'],
            ['nom' => 'SAHOURE', 'prenom' => 'Kouassi Stephane', 'email' => '', 'telephone' => '', 'poste' => 'Chauffeur', 'entite' => 'Direction Administration et Ressources / Moyens Généraux', 'date_embauche' => '01/01/2026', 'responsable' => 'DIARRASSOUBA Abain Aissata'],
            ['nom' => 'OURA', 'prenom' => 'N\'Da Leonce', 'email' => 'leonce.oura@sgpme.ci', 'telephone' => '', 'poste' => 'Auditeur Interne', 'entite' => 'Direction Générale / Audit Interne', 'date_embauche' => '02/02/2026', 'responsable' => 'OUATTARA Roger'],
        ];

        foreach ($personnel as $person) {
            // Skip if email is empty or user already exists
            if (empty($person['email']) || User::where('email', $person['email'])->exists()) {
                continue;
            }

            // Find entity by name
            // Si l'entité contient un "/", prendre la partie après (le service/département)
            // Sinon, prendre l'entité complète (la direction)
            $entity = null;
            if (!empty($person['entite'])) {
                $entityParts = explode('/', $person['entite']);
                if (count($entityParts) > 1) {
                    // Il y a un parent et un enfant, on prend l'enfant (service/département)
                    $entityName = trim($entityParts[1]);
                } else {
                    // Pas de hiérarchie, on prend l'entité directement (direction)
                    $entityName = trim($entityParts[0]);
                }
                $entity = Entity::where('name', 'LIKE', '%' . $entityName . '%')->first();
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

            // Find supervisor by full name
            $supervisor = null;
            if (!empty($person['responsable'])) {
                $supervisorParts = explode(' ', trim($person['responsable']));
                if (count($supervisorParts) >= 2) {
                    $lastName = $supervisorParts[0];
                    $supervisor = User::where('last_name', $lastName)->first();
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
