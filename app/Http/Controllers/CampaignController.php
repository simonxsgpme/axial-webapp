<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Entity;
use App\Models\Objective;
use App\Models\ObjectiveCategory;
use App\Models\User;
use App\Models\UserCampaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->get();
        return view('campaigns.index', compact('campaigns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'required|digits:4',
            'objective_starts_at' => 'nullable|date',
            'objective_stops_at' => 'nullable|date|after_or_equal:objective_starts_at',
            'midterm_starts_at' => 'nullable|date',
            'midterm_stops_at' => 'nullable|date|after_or_equal:midterm_starts_at',
            'evaluation_starts_at' => 'nullable|date',
            'evaluation_stops_at' => 'nullable|date|after_or_equal:evaluation_starts_at',
        ]);

        Campaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'year' => $request->year,
            'objective_starts_at' => $request->objective_starts_at,
            'objective_stops_at' => $request->objective_stops_at,
            'midterm_starts_at' => $request->midterm_starts_at,
            'midterm_stops_at' => $request->midterm_stops_at,
            'evaluation_starts_at' => $request->evaluation_starts_at,
            'evaluation_stops_at' => $request->evaluation_stops_at,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campagne créée avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['userCampaigns.user.entity', 'userCampaigns.supervisor']);
        $existingUserUuids = $campaign->userCampaigns->pluck('user_uuid')->toArray();
        $availableUsers = User::where('is_active', true)
            ->whereNotIn('uuid', $existingUserUuids)
            ->orderBy('full_name')
            ->get();
        return view('campaigns.show', compact('campaign', 'availableUsers'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if ($campaign->status === 'archived') {
            return response()->json([
                'success' => false,
                'message' => 'Une campagne archivée ne peut pas être modifiée.',
            ], 422);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year' => 'required|digits:4',
            'objective_starts_at' => 'nullable|date',
            'objective_stops_at' => 'nullable|date|after_or_equal:objective_starts_at',
            'midterm_starts_at' => 'nullable|date',
            'midterm_stops_at' => 'nullable|date|after_or_equal:midterm_starts_at',
            'evaluation_starts_at' => 'nullable|date',
            'evaluation_stops_at' => 'nullable|date|after_or_equal:evaluation_starts_at',
        ]);

        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'year' => $request->year,
            'objective_starts_at' => $request->objective_starts_at,
            'objective_stops_at' => $request->objective_stops_at,
            'midterm_starts_at' => $request->midterm_starts_at,
            'midterm_stops_at' => $request->midterm_stops_at,
            'evaluation_starts_at' => $request->evaluation_starts_at,
            'evaluation_stops_at' => $request->evaluation_stops_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Campagne modifiée avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function updateStatus(Request $request, Campaign $campaign)
    {
        $request->validate([
            'action' => 'required|in:start-objectives,complete-objectives,start-midterm,complete-midterm,start-evaluations,complete-evaluations,archive',
            'force' => 'nullable|boolean',
        ]);

        $transitions = [
            'start-objectives' => ['from' => 'draft', 'to' => 'objective_in_progress'],
            'complete-objectives' => ['from' => 'objective_in_progress', 'to' => 'objective_completed'],
            'start-midterm' => ['from' => 'objective_completed', 'to' => 'midterm_in_progress'],
            'complete-midterm' => ['from' => 'midterm_in_progress', 'to' => 'midterm_completed'],
            'start-evaluations' => ['from' => 'midterm_completed', 'to' => 'evaluation_in_progress'],
            'complete-evaluations' => ['from' => 'evaluation_in_progress', 'to' => 'evaluation_completed'],
            'archive' => ['from' => 'evaluation_completed', 'to' => 'archived'],
        ];

        $transition = $transitions[$request->action];

        if ($campaign->status !== $transition['from']) {
            return response()->json([
                'success' => false,
                'message' => 'Cette action n\'est pas possible pour le statut actuel de la campagne.',
            ], 422);
        }

        // Vérifier qu'il y a au moins un participant avant de démarrer la phase objectifs
        if ($request->action === 'start-objectives') {
            if ($campaign->userCampaigns()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de démarrer la phase objectifs : aucun participant dans cette campagne. Ajoutez des participants avant de continuer.',
                ], 422);
            }
        }

        // Vérifier que tous les participants ont terminé la validation avant de clôturer la phase objectifs
        if ($request->action === 'complete-objectives') {
            $totalParticipants = $campaign->userCampaigns()->count();
            $completedParticipants = $campaign->userCampaigns()->where('objective_status', 'completed')->count();

            if ($totalParticipants === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun participant dans cette campagne.',
                ], 422);
            }

            if ($completedParticipants < $totalParticipants) {
                $remaining = $totalParticipants - $completedParticipants;
                
                // Si force n'est pas activé, retourner un avertissement avec les détails
                if (!$request->boolean('force')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de terminer la phase objectifs : ' . $remaining . ' participant(s) sur ' . $totalParticipants . ' n\'ont pas encore terminé la validation de leurs objectifs.',
                        'require_force' => true,
                        'not_completed_count' => $remaining,
                        'total_count' => $totalParticipants,
                    ], 422);
                }
                
                // Si force est activé, marquer comme "not_evaluated" uniquement les participants qui n'ont AUCUN objectif
                $userCampaigns = $campaign->userCampaigns()
                    ->where('objective_status', '!=', 'completed')
                    ->get();
                
                foreach ($userCampaigns as $uc) {
                    // Ne marquer 'not_evaluated' que si l'utilisateur n'a aucun objectif
                    if ($uc->objectives()->count() === 0) {
                        $uc->update(['objective_status' => 'not_evaluated']);
                    }
                }
            }
        }

        // Vérifier qu'il y a au moins un participant avant de terminer la phase mi-parcours
        if ($request->action === 'complete-midterm') {
            if ($campaign->userCampaigns()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun participant dans cette campagne.',
                ], 422);
            }
        }

        // Vérifier que tous les participants ont validé leur évaluation avant de clôturer la phase évaluations
        if ($request->action === 'complete-evaluations') {
            $totalParticipants = $campaign->userCampaigns()->count();
            
            // Compter les participants qui ont des objectifs renseignés
            $participantsWithObjectives = $campaign->userCampaigns()
                ->whereHas('objectives')
                ->count();
            
            // Compter les participants validés
            $validatedParticipants = $campaign->userCampaigns()
                ->where('evaluation_status', 'validated')
                ->count();

            if ($totalParticipants === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun participant dans cette campagne.',
                ], 422);
            }

            // Vérifier si des participants avec objectifs ne sont pas encore évalués
            if ($validatedParticipants < $participantsWithObjectives) {
                $remaining = $participantsWithObjectives - $validatedParticipants;
                
                // Demander confirmation avant de terminer
                if (!$request->boolean('confirm')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Attention : ' . $remaining . ' participant(s) ayant renseigné leurs objectifs n\'ont pas encore été évalués. Voulez-vous vraiment terminer la phase évaluation ?',
                        'require_confirmation' => true,
                        'not_evaluated_count' => $remaining,
                        'total_with_objectives' => $participantsWithObjectives,
                    ], 422);
                }
            }
        }

        $campaign->update(['status' => $transition['to']]);

        $labels = [
            'objective_in_progress' => 'Phase objectifs démarrée.',
            'objective_completed' => 'Phase objectifs terminée.',
            'midterm_in_progress' => 'Phase mi-parcours démarrée.',
            'midterm_completed' => 'Phase mi-parcours terminée.',
            'evaluation_in_progress' => 'Phase évaluation démarrée.',
            'evaluation_completed' => 'Phase évaluation terminée.',
            'archived' => 'Campagne archivée.',
        ];

        return response()->json([
            'success' => true,
            'message' => $labels[$transition['to']],
            'urlback' => 'back',
        ]);
    }

    public function results(Campaign $campaign)
    {
        if (!in_array($campaign->status, ['evaluation_completed', 'archived'])) {
            return redirect()->route('campaigns.show', $campaign->uuid);
        }

        $campaign->load(['userCampaigns.user.entity', 'userCampaigns.supervisor']);

        // Tous les participants avec leur note
        $participants = $campaign->userCampaigns->sortByDesc('rating');

        // Score global moyen de la campagne
        $ratedParticipants = $participants->whereNotNull('rating');
        $globalScore = $ratedParticipants->count() > 0 ? round($ratedParticipants->avg('rating'), 1) : 0;
        $totalParticipants = $participants->count();
        $validatedCount = $participants->where('evaluation_status', 'validated')->count();

        // Podium: top 3
        $podium = $participants->take(3)->values();

        // Grouper par entité avec support imbrication (parent > enfants)
        $entities = Entity::with('parent')->orderBy('name')->get();
        $entitiesWithParticipants = [];

        // Séparer entités racines (sans parent) et enfants
        $rootEntities = $entities->whereNull('parent_uuid');
        $childEntities = $entities->whereNotNull('parent_uuid');

        foreach ($rootEntities as $rootEntity) {
            // Membres directement dans l'entité racine
            $directMembers = $participants->filter(function ($uc) use ($rootEntity) {
                return $uc->user && $uc->user->entity_uuid === $rootEntity->uuid;
            })->sortByDesc('rating')->values();

            // Sous-entités
            $children = $childEntities->where('parent_uuid', $rootEntity->uuid);
            $subEntities = [];
            $allMembers = collect($directMembers);

            foreach ($children as $child) {
                $childMembers = $participants->filter(function ($uc) use ($child) {
                    return $uc->user && $uc->user->entity_uuid === $child->uuid;
                })->sortByDesc('rating')->values();

                if ($childMembers->count() > 0) {
                    $subEntities[] = [
                        'entity' => $child,
                        'members' => $childMembers,
                        'avg_rating' => round($childMembers->whereNotNull('rating')->avg('rating'), 1),
                    ];
                    $allMembers = $allMembers->merge($childMembers);
                }
            }

            if ($allMembers->count() > 0) {
                $entitiesWithParticipants[] = [
                    'entity' => $rootEntity,
                    'members' => $directMembers,
                    'sub_entities' => $subEntities,
                    'all_members' => $allMembers,
                    'avg_rating' => round($allMembers->whereNotNull('rating')->avg('rating'), 1),
                ];
            }
        }

        // Entités orphelines (parent supprimé) - traiter comme racines
        $orphanChildren = $childEntities->filter(function ($child) use ($rootEntities) {
            return !$rootEntities->contains('uuid', $child->parent_uuid);
        });
        foreach ($orphanChildren as $orphan) {
            $members = $participants->filter(function ($uc) use ($orphan) {
                return $uc->user && $uc->user->entity_uuid === $orphan->uuid;
            })->sortByDesc('rating')->values();
            if ($members->count() > 0) {
                $entitiesWithParticipants[] = [
                    'entity' => $orphan,
                    'members' => $members,
                    'sub_entities' => [],
                    'all_members' => $members,
                    'avg_rating' => round($members->whereNotNull('rating')->avg('rating'), 1),
                ];
            }
        }

        // Participants sans entité
        $noEntity = $participants->filter(function ($uc) {
            return !$uc->user || !$uc->user->entity_uuid;
        })->sortByDesc('rating')->values();
        if ($noEntity->count() > 0) {
            $entitiesWithParticipants[] = [
                'entity' => (object) ['name' => 'Sans entité', 'uuid' => null, 'acronym' => null],
                'members' => $noEntity,
                'sub_entities' => [],
                'all_members' => $noEntity,
                'avg_rating' => round($noEntity->whereNotNull('rating')->avg('rating'), 1),
            ];
        }

        return view('campaigns.results', compact(
            'campaign', 'globalScore', 'totalParticipants', 'validatedCount',
            'podium', 'entitiesWithParticipants'
        ));
    }

    public function editParticipantObjectives(Campaign $campaign, UserCampaign $userCampaign)
    {
        $userCampaign->load(['user', 'objectives.category']);
        $categories = ObjectiveCategory::all();
        return view('campaigns.participant-objectives', compact('campaign', 'userCampaign', 'categories'));
    }

    public function storeParticipantObjective(Request $request, Campaign $campaign, UserCampaign $userCampaign)
    {
        $request->validate([
            'objective_category_uuid' => 'required|exists:objective_categories,uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'nullable|integer|min:0|max:100',
        ]);

        $objective = Objective::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'objective_category_uuid' => $request->objective_category_uuid,
            'title' => $request->title,
            'description' => $request->description,
            'weight' => $request->weight ?? 0,
            'status' => 'validated',
        ]);

        $objective->load('category');
        $this->checkAndCompleteObjectives($userCampaign);

        return response()->json([
            'success' => true,
            'message' => 'Objectif ajouté avec succès.',
            'objective' => $objective,
        ]);
    }

    public function updateParticipantObjective(Request $request, Campaign $campaign, UserCampaign $userCampaign, Objective $objective)
    {
        $request->validate([
            'objective_category_uuid' => 'required|exists:objective_categories,uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'nullable|integer|min:0|max:100',
        ]);

        $objective->update([
            'objective_category_uuid' => $request->objective_category_uuid,
            'title' => $request->title,
            'description' => $request->description,
            'weight' => $request->weight ?? 0,
            'status' => 'validated',
        ]);

        $objective->load('category');
        $this->checkAndCompleteObjectives($userCampaign);

        return response()->json([
            'success' => true,
            'message' => 'Objectif modifié avec succès.',
            'objective' => $objective,
        ]);
    }

    private function checkAndCompleteObjectives(UserCampaign $userCampaign): void
    {
        $totalWeight = $userCampaign->objectives()->where('status', 'validated')->sum('weight');
        if ($totalWeight >= 100) {
            $userCampaign->update(['objective_status' => 'completed']);
        }
    }

    public function destroyParticipantObjective(Campaign $campaign, UserCampaign $userCampaign, Objective $objective)
    {
        $objective->delete();
        return response()->json([
            'success' => true,
            'message' => 'Objectif supprimé avec succès.',
        ]);
    }

    public function pdfObjectives(Campaign $campaign, UserCampaign $userCampaign)
    {
        $userCampaign->load(['user.entity', 'supervisor', 'objectives.category']);

        // Grouper les objectifs par catégorie
        $objectivesByCategory = $userCampaign->objectives->groupBy('objective_category_uuid');
        
        $userName = $userCampaign->user->full_name;
        $campaignName = $campaign->name;
        $year = $campaign->year;

        // Generate simple HTML-based PDF using browser print
        return view('campaigns.pdf-objectives', compact('campaign', 'userCampaign', 'objectivesByCategory', 'userName', 'campaignName', 'year'));
    }

    public function wordObjectives(Campaign $campaign, UserCampaign $userCampaign)
    {
        $userCampaign->load(['user.entity', 'supervisor', 'objectives.category']);

        // Grouper les objectifs par catégorie
        $objectivesByCategory = $userCampaign->objectives->groupBy('objective_category_uuid');

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $sectionStyle = [
            'marginTop'    => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
            'marginLeft'   => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginRight'  => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'headerHeight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.5),
            'footerHeight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.5),
        ];
        $section = $phpWord->addSection($sectionStyle);

        // Filigrane
        $logoPath    = public_path('assets/images/logo.png');
        $filigramePath = public_path('assets/images/filigrane.png');

        if (file_exists($filigramePath)) {
            $header = $section->addHeader(\PhpOffice\PhpWord\Element\Header::FIRST);
            $header->addWatermark($filigramePath, ['marginTop' => 3000, 'marginLeft' => 3000]);
            $defaultHeader = $section->addHeader();
            $defaultHeader->addWatermark($filigramePath, ['marginTop' => 3000, 'marginLeft' => 3000]);
        } else {
            $defaultHeader = $section->addHeader();
        }

        // En-tête : logo gauche + www.sgpme.ci droite
        $headerTable = $defaultHeader->addTable(['borderSize' => 0, 'borderColor' => 'ffffff', 'cellMargin' => 0, 'width' => 100 * 50]);
        $headerTable->addRow();
        $logoCell = $headerTable->addCell(4500);
        if (file_exists($logoPath)) {
            $logoCell->addImage($logoPath, ['width' => 100, 'height' => 40, 'wrappingStyle' => 'inline']);
        } else {
            $logoCell->addText('SGPME', ['bold' => true, 'size' => 14]);
        }
        $headerTable->addCell(5500)->addText('www.sgpme.ci', ['bold' => true, 'size' => 13, 'color' => 'FF6600'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

        // Pied de page
        $footer = $section->addFooter();
        $footerText = 'Société d\'Etat, créée par Décret N° 2022-261 du 13 avril 2022, au Capital de 10 000 000 000 F CFA - Siège Social : Abidjan - Adjamé 220 Logements - Indénié en face de Fraternité Matin - Immeuble CGRAE, 4ème et 5ème étages, 09 BP 1634 Abidjan 09 Côte d\'Ivoire - Tel : (+225) 2720236020 – N° RCCM : I-ABJ-03-2022-B15-00060 – N° IDU : CI-2022-0036513 N – N° agrément en qualité d\'Etablissement Financier à caractère Bancaire : A 0264 G';
        $footer->addText($footerText, ['size' => 7, 'color' => '444444'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Styles
        $phpWord->addFontStyle('titleStyle', ['bold' => true, 'size' => 14]);
        $phpWord->addFontStyle('headingStyle', ['bold' => true, 'size' => 12]);
        $phpWord->addFontStyle('normalStyle', ['size' => 11]);
        $phpWord->addFontStyle('smallStyle', ['size' => 10]);

        // Titre
        $section->addText(
            'Rapport individuel de gestion de la performance ' . $campaign->year,
            'titleStyle'
        );
        $section->addTextBreak(1);

        // Informations employé
        $nameParts = explode(' ', $userCampaign->user->full_name);
        $lastName = strtoupper(array_pop($nameParts));
        $firstName = implode(' ', $nameParts);

        $section->addText('Nom : ' . $lastName, 'normalStyle');
        $section->addText('Prénoms : ' . $firstName, 'normalStyle');
        $section->addText('Fonction : ' . ($userCampaign->user->position ?? '-'), 'normalStyle');
        $section->addText('Date d\'embauche : ' . ($userCampaign->user->hire_date ? \Carbon\Carbon::parse($userCampaign->user->hire_date)->format('d/m/Y') : '-'), 'normalStyle');
        $section->addText('Direction / Service : ' . ($userCampaign->user->entity->name ?? '-'), 'normalStyle');
        $section->addText('Étape de la campagne : ' . $campaign->status_label, 'normalStyle');
        $section->addText('Nom de l\'évaluateur : ' . ($userCampaign->supervisor->full_name ?? '-'), 'normalStyle');
        $section->addTextBreak(1);

        // Objectifs par catégorie
        foreach ($objectivesByCategory as $categoryUuid => $categoryObjectives) {
            $firstObjective = $categoryObjectives->first();
            $categoryName = $firstObjective->category->name ?? 'Autres objectifs';
            $categoryAbbr = 'OI';

            if (stripos($categoryName, 'collectif') !== false) {
                $categoryAbbr = 'OC';
            } elseif (stripos($categoryName, 'développement') !== false || stripos($categoryName, 'comportemental') !== false) {
                $categoryAbbr = 'OD';
            }

            $section->addText($categoryName, 'headingStyle');
            $section->addTextBreak(1);

            // Tableau des objectifs
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
                'width' => 100 * 50
            ]);

            // En-tête
            $table->addRow();
            $table->addCell(800)->addText('', 'smallStyle');
            $table->addCell(3700)->addText('Description de l\'objectif', ['bold' => true, 'size' => 10]);
            $table->addCell(1100)->addText('Poids (P)', ['bold' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(2200)->addText('Commentaires de l\'évalué', ['bold' => true, 'size' => 10]);
            $table->addCell(2200)->addText('Commentaires de l\'évaluateur', ['bold' => true, 'size' => 10]);
            $table->addCell(0)->addText('Note', ['bold' => true, 'size' => 10]);

            // Objectifs
            $categoryTotal = 0;
            $index = 1;
            foreach ($categoryObjectives as $obj) {
                $table->addRow();
                $table->addCell(800)->addText($categoryAbbr . ' ' . $index++, 'smallStyle');
                $cell = $table->addCell(3700);
                $cell->addText($obj->title, ['bold' => true, 'size' => 10]);
                if ($obj->description) {
                    $cell->addText($obj->description, ['size' => 9, 'color' => '333333']);
                }
                $table->addCell(1100)->addText($obj->weight . '%', 'smallStyle', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $table->addCell(2200)->addText('', 'smallStyle');
                $table->addCell(2200)->addText('', 'smallStyle');
                $table->addCell(0)->addText('', 'smallStyle');
                $categoryTotal += $obj->weight;
            }

            // Total
            $table->addRow();
            $table->addCell(800)->addText('', 'smallStyle');
            $table->addCell(3700)->addText('Total', ['bold' => true, 'size' => 10]);
            $table->addCell(1100)->addText($categoryTotal . '%', ['bold' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(2200)->addText('', 'smallStyle');
            $table->addCell(2200)->addText('', 'smallStyle');
            $table->addCell(0)->addText('', 'smallStyle');

            $section->addTextBreak(1);
        }

        // Score individuel (si la campagne a dépassé la phase objectifs)
        if (!in_array($campaign->status, ['draft', 'objective_in_progress'])) {
            $section->addText('Score individuel : 100/100 - Répond à toutes les attentes', 'headingStyle');
            $section->addTextBreak(1);

            $scoreTable = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
                'width' => 100 * 50
            ]);

            $scoreTable->addRow();
            $scoreTable->addCell(2500)->addText('Commentaire général de l\'évaluateur', ['bold' => true, 'size' => 11]);
            $scoreTable->addCell(7500)->addText($userCampaign->supervisor_comment ?? 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'smallStyle');

            $scoreTable->addRow();
            $scoreTable->addCell(2500)->addText('Commentaire général de l\'évalué', ['bold' => true, 'size' => 11]);
            $scoreTable->addCell(7500)->addText($userCampaign->employee_comment ?? 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'smallStyle');
        }

        // Générer le fichier Word
        $fileName = 'Fiche_Objectifs_' . str_replace(' ', '_', $userCampaign->user->full_name) . '_' . $campaign->year . '.docx';
        $tempFile = storage_path('app/temp/' . $fileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function wordMidterm(Campaign $campaign, UserCampaign $userCampaign)
    {
        $userCampaign->load(['user.entity', 'supervisor', 'objectives.category']);

        // Grouper les objectifs par catégorie
        $objectivesByCategory = $userCampaign->objectives->groupBy('objective_category_uuid');

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Styles
        $phpWord->addFontStyle('titleStyle', ['bold' => true, 'size' => 14]);
        $phpWord->addFontStyle('headingStyle', ['bold' => true, 'size' => 12]);
        $phpWord->addFontStyle('normalStyle', ['size' => 11]);
        $phpWord->addFontStyle('smallStyle', ['size' => 10]);

        // Titre
        $section->addText(
            'Fiche Mi-Parcours - Gestion de la performance ' . $campaign->year,
            'titleStyle'
        );
        $section->addTextBreak(1);

        // Informations employé
        $nameParts = explode(' ', $userCampaign->user->full_name);
        $lastName = strtoupper(array_pop($nameParts));
        $firstName = implode(' ', $nameParts);

        $section->addText('Nom : ' . $lastName, 'normalStyle');
        $section->addText('Prénoms : ' . $firstName, 'normalStyle');
        $section->addText('Fonction : ' . ($userCampaign->user->position ?? '-'), 'normalStyle');
        $section->addText('Date d\'embauche : ' . ($userCampaign->user->hire_date ? \Carbon\Carbon::parse($userCampaign->user->hire_date)->format('d/m/Y') : '-'), 'normalStyle');
        $section->addText('Direction / Service : ' . ($userCampaign->user->entity->name ?? '-'), 'normalStyle');
        $section->addText('Étape de la campagne : ' . $campaign->status_label, 'normalStyle');
        $section->addText('Nom de l\'évaluateur : ' . ($userCampaign->supervisor->full_name ?? '-'), 'normalStyle');
        $section->addTextBreak(1);

        // Objectifs par catégorie
        foreach ($objectivesByCategory as $categoryUuid => $categoryObjectives) {
            $firstObjective = $categoryObjectives->first();
            $categoryName = $firstObjective->category->name ?? 'Autres objectifs';
            $categoryAbbr = 'OI';

            if (stripos($categoryName, 'collectif') !== false) {
                $categoryAbbr = 'OC';
            } elseif (stripos($categoryName, 'développement') !== false || stripos($categoryName, 'comportemental') !== false) {
                $categoryAbbr = 'OD';
            }

            $section->addText($categoryName, 'headingStyle');
            $section->addTextBreak(1);

            // Tableau des objectifs
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 80,
                'width' => 100 * 50
            ]);

            // En-tête
            $table->addRow();
            $table->addCell(800)->addText('', 'smallStyle');
            $table->addCell(3700)->addText('Description de l\'objectif', ['bold' => true, 'size' => 10]);
            $table->addCell(1100)->addText('Poids (P)', ['bold' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(2200)->addText('Avancement mi-parcours', ['bold' => true, 'size' => 10]);
            $table->addCell(2200)->addText('Commentaires', ['bold' => true, 'size' => 10]);
            $table->addCell(0)->addText('Note', ['bold' => true, 'size' => 10]);

            // Objectifs
            $categoryTotal = 0;
            $index = 1;
            foreach ($categoryObjectives as $obj) {
                $table->addRow();
                $table->addCell(800)->addText($categoryAbbr . ' ' . $index++, 'smallStyle');
                $cell = $table->addCell(3700);
                $cell->addText($obj->title, ['bold' => true, 'size' => 10]);
                if ($obj->description) {
                    $cell->addText($obj->description, ['size' => 9, 'color' => '333333']);
                }
                $table->addCell(1100)->addText($obj->weight . '%', 'smallStyle', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $table->addCell(2200)->addText('', 'smallStyle');
                $table->addCell(2200)->addText('', 'smallStyle');
                $table->addCell(0)->addText('', 'smallStyle');
                $categoryTotal += $obj->weight;
            }

            // Total
            $table->addRow();
            $table->addCell(800)->addText('', 'smallStyle');
            $table->addCell(3700)->addText('Total', ['bold' => true, 'size' => 10]);
            $table->addCell(1100)->addText($categoryTotal . '%', ['bold' => true, 'size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $table->addCell(2200)->addText('', 'smallStyle');
            $table->addCell(2200)->addText('', 'smallStyle');
            $table->addCell(0)->addText('', 'smallStyle');

            $section->addTextBreak(1);
        }

        // Section commentaires mi-parcours
        $section->addText('Commentaires Mi-Parcours', 'headingStyle');
        $section->addTextBreak(1);

        $commentTable = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
            'width' => 100 * 50
        ]);

        $commentTable->addRow();
        $commentTable->addCell(2500)->addText('Commentaire du supérieur', ['bold' => true, 'size' => 11]);
        $commentTable->addCell(7500)->addText($userCampaign->midterm_supervisor_comment ?? '', 'smallStyle');

        $commentTable->addRow();
        $commentTable->addCell(2500)->addText('Commentaire de l\'évalué', ['bold' => true, 'size' => 11]);
        $commentTable->addCell(7500)->addText($userCampaign->midterm_employee_comment ?? '', 'smallStyle');

        // Générer le fichier Word
        $fileName = 'Fiche_MiParcours_' . str_replace(' ', '_', $userCampaign->user->full_name) . '_' . $campaign->year . '.docx';
        $tempFile = storage_path('app/temp/' . $fileName);

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function skipPhase(Request $request, Campaign $campaign, UserCampaign $userCampaign)
    {
        $statusTransitions = [
            'objective_in_progress' => [
                'objective_status' => 'completed',
            ],
            'midterm_in_progress' => [],
            'evaluation_in_progress' => [
                'evaluation_status' => 'validated',
            ],
        ];

        if (!isset($statusTransitions[$campaign->status])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de passer la phase pour le statut actuel de la campagne.',
            ], 422);
        }

        $updates = $statusTransitions[$campaign->status];
        if (!empty($updates)) {
            $userCampaign->update($updates);
        }

        return response()->json([
            'success' => true,
            'message' => 'Le participant a été passé à la phase suivante.',
            'urlback' => 'back',
        ]);
    }

    public function midtermReport(Campaign $campaign, UserCampaign $userCampaign)
    {
        $userCampaign->load(['user', 'objectives.category', 'objectives.histories.changedBy']);

        $objectives = $userCampaign->objectives->sortBy(fn($o) => $o->category->name ?? '');
        $userName = $userCampaign->user->full_name;

        return view('campaigns.midterm-report', compact('campaign', 'userCampaign', 'objectives', 'userName'));
    }

    public function uploadMidterm(Request $request, Campaign $campaign, UserCampaign $userCampaign)
    {
        $request->validate([
            'midterm_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('midterm_file');
        $filename = 'midterm_' . $userCampaign->uuid . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('midterm_files', $filename, 'public');

        $userCampaign->update(['midterm_file' => 'midterm_files/' . $filename]);

        return response()->json([
            'success' => true,
            'message' => 'Fiche mi-parcours importée avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function downloadMidterm(Campaign $campaign, UserCampaign $userCampaign)
    {
        if (!$userCampaign->midterm_file) {
            abort(404, 'Aucune fiche mi-parcours disponible.');
        }

        return response()->download(storage_path('app/public/' . $userCampaign->midterm_file));
    }

    public function destroy(Campaign $campaign)
    {
        if (!in_array($campaign->status, ['draft', 'archived'])) {
            return response()->json([
                'success' => false,
                'message' => 'Seules les campagnes en brouillon ou archivées peuvent être supprimées.',
            ], 422);
        }

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campagne supprimée avec succès.',
            'urlback' => 'back',
        ]);
    }
}
