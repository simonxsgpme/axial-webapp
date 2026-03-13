<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Objective;
use App\Models\ObjectiveCategory;
use App\Models\ObjectiveDecision;
use App\Models\ObjectiveHistory;
use App\Models\UserCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObjectiveController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Chercher la campagne en cours
        $campaign = Campaign::whereIn('status', ['objective_in_progress', 'midterm_in_progress', 'midterm_completed', 'evaluation_in_progress'])->first();

        if (!$campaign) {
            return view('objectives.index', [
                'campaign' => null,
                'userCampaign' => null,
                'categories' => collect(),
                'objectives' => collect(),
                'decisions' => collect(),
                'phase' => null,
            ]);
        }

        // Vérifier si l'utilisateur est participant
        $userCampaign = UserCampaign::where('campaign_uuid', $campaign->uuid)
            ->where('user_uuid', $user->uuid)
            ->first();

        if (!$userCampaign) {
            return view('objectives.index', [
                'campaign' => $campaign,
                'userCampaign' => null,
                'categories' => collect(),
                'objectives' => collect(),
                'decisions' => collect(),
                'phase' => $campaign->status,
            ]);
        }

        $categories = ObjectiveCategory::all();
        $objectives = Objective::where('user_campaign_uuid', $userCampaign->uuid)
            ->with(['category', 'comments.user'])
            ->get();
        $decisions = $userCampaign->decisions()->with('actor')->get();

        return view('objectives.index', [
            'campaign' => $campaign,
            'userCampaign' => $userCampaign,
            'categories' => $categories,
            'objectives' => $objectives,
            'decisions' => $decisions,
            'phase' => $campaign->status,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_campaign_uuid' => 'required|exists:user_campaigns,uuid',
            'objective_category_uuid' => 'required|exists:objective_categories,uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'nullable|integer|min:0|max:100',
        ]);

        // Vérifier que le user_campaign appartient à l'utilisateur connecté
        $userCampaign = UserCampaign::where('uuid', $request->user_campaign_uuid)
            ->where('user_uuid', Auth::id())
            ->firstOrFail();

        // Vérifier que l'employé peut encore modifier (draft, returned, ou midterm)
        $campaign = $userCampaign->campaign;
        $canAdd = in_array($userCampaign->objective_status, ['draft', 'returned'])
            || in_array($campaign->status, ['midterm_in_progress']);

        if (!$canAdd) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus ajouter d\'objectifs.',
            ], 403);
        }

        $objective = Objective::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'objective_category_uuid' => $request->objective_category_uuid,
            'title' => $request->title,
            'description' => $request->description,
            'weight' => $request->weight ?? 0,
        ]);

        // Track history if created during midterm
        if ($campaign->status === 'midterm_in_progress') {
            ObjectiveHistory::create([
                'objective_uuid' => $objective->uuid,
                'changed_by_uuid' => Auth::id(),
                'field' => 'created',
                'old_value' => null,
                'new_value' => $objective->title,
                'phase' => 'midterm',
            ]);
        }

        $objective->load(['category', 'comments.user']);

        return response()->json([
            'success' => true,
            'message' => 'Objectif créé avec succès.',
            'objective' => $objective,
        ]);
    }

    public function show(Objective $objective)
    {
        $objective->load(['category', 'comments.user', 'userCampaign']);

        return response()->json([
            'success' => true,
            'objective' => $objective,
        ]);
    }

    public function update(Request $request, Objective $objective)
    {
        // Vérifier que l'objectif appartient à l'utilisateur connecté
        if ($objective->userCampaign->user_uuid != Auth::id() && $objective->userCampaign->user->supervisor_uuid != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cet objectif.',
            ], 403);
        }

        // Vérifier que l'employé peut encore modifier
        $userCampaign = $objective->userCampaign;
        $campaign = $userCampaign->campaign;
        $isMidterm = $campaign->status === 'midterm_in_progress';
        
        // Pendant la phase mi-parcours, permettre la modification sans restriction
        if (!$isMidterm) {
            if (!in_array($userCampaign->objective_status, ['draft', 'returned', 'submitted'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez plus modifier vos objectifs.',
                ], 403);
            }

            if ($objective->status === 'validated') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet objectif a déjà été validé et ne peut plus être modifié.',
                ], 403);
            }
        }

        $request->validate([
            'objective_category_uuid' => 'required|exists:objective_categories,uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'nullable|integer|min:0|max:100',
        ]);

        // Track history if during midterm phase
        $campaign = $userCampaign->campaign;
        if ($campaign->status === 'midterm_in_progress') {
            $fields = ['title', 'description', 'weight', 'objective_category_uuid'];
            foreach ($fields as $field) {
                $newVal = $request->$field;
                $oldVal = $objective->$field;
                if ($newVal != $oldVal) {
                    ObjectiveHistory::create([
                        'objective_uuid' => $objective->uuid,
                        'changed_by_uuid' => Auth::id(),
                        'field' => $field,
                        'old_value' => (string) $oldVal,
                        'new_value' => (string) $newVal,
                        'phase' => 'midterm',
                    ]);
                }
            }
        }

        $objective->update([
            'objective_category_uuid' => $request->objective_category_uuid,
            'title' => $request->title,
            'description' => $request->description,
            'weight' => $request->weight ?? $objective->weight,
            'status' => $campaign->status === 'midterm_in_progress' ? $objective->status : 'pending',
            'rejection_reason' => $campaign->status === 'midterm_in_progress' ? $objective->rejection_reason : null,
        ]);

        $objective->load(['category', 'comments.user']);

        return response()->json([
            'success' => true,
            'message' => 'Objectif modifié avec succès.',
            'objective' => $objective,
        ]);
    }

    public function destroy(Objective $objective)
    {
        // Vérifier que l'objectif appartient à l'utilisateur connecté
        if ($objective->userCampaign->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer cet objectif.',
            ], 403);
        }

        // Vérifier que l'employé peut encore modifier
        $userCampaign = $objective->userCampaign;
        $campaign = $userCampaign->campaign;
        $isMidterm = $campaign->status === 'midterm_in_progress';
        
        // Pendant la phase mi-parcours, permettre la suppression sans restriction
        if (!$isMidterm) {
            if (!in_array($userCampaign->objective_status, ['draft', 'returned'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez plus supprimer vos objectifs.',
                ], 403);
            }

            if ($objective->status === 'validated') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet objectif a déjà été validé et ne peut plus être supprimé.',
                ], 403);
            }
        }

        // Track history if during midterm phase
        if ($isMidterm) {
            ObjectiveHistory::create([
                'objective_uuid' => $objective->uuid,
                'changed_by_uuid' => Auth::id(),
                'field' => 'deleted',
                'old_value' => $objective->title,
                'new_value' => null,
                'phase' => 'midterm',
            ]);
        }

        $objective->delete();

        return response()->json([
            'success' => true,
            'message' => 'Objectif supprimé avec succès.',
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'user_campaign_uuid' => 'required|exists:user_campaigns,uuid',
        ]);

        $userCampaign = UserCampaign::where('uuid', $request->user_campaign_uuid)
            ->where('user_uuid', Auth::id())
            ->firstOrFail();

        if (!in_array($userCampaign->objective_status, ['draft', 'returned'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas soumettre vos objectifs dans cet état.',
            ], 403);
        }

        // Vérifier qu'il y a au moins un objectif
        if ($userCampaign->objectives()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez avoir au moins un objectif avant de soumettre.',
            ], 422);
        }

        // Vérifier que le total des pondérations est 100%
        $totalWeight = (float)$userCampaign->objectives()->sum('weight');
        if ($totalWeight != 100) {
            return response()->json([
                'success' => false,
                'message' => 'Le total des pondérations doit être égal à 100% (actuellement ' . $totalWeight . '%).',
            ], 422);
        }

        // Remettre les objectifs rejetés en pending pour la nouvelle soumission
        $userCampaign->objectives()->where('status', 'rejected')->update([
            'status' => 'pending',
            'rejection_reason' => null,
        ]);

        $userCampaign->update(['objective_status' => 'submitted']);

        // Enregistrer la décision dans la timeline
        ObjectiveDecision::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'actor_uuid' => Auth::id(),
            'action' => 'submitted',
            'comment' => 'Objectifs soumis pour validation.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vos objectifs ont été soumis à votre supérieur.',
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $user = Auth::user();

        // Chercher la campagne en cours
        $campaign = Campaign::where('status', 'objective_in_progress')->first();

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune campagne en cours pour importer des objectifs.',
            ], 422);
        }

        // Vérifier si l'utilisateur est participant
        $userCampaign = UserCampaign::where('campaign_uuid', $campaign->uuid)
            ->where('user_uuid', $user->uuid)
            ->first();

        if (!$userCampaign) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas participant à cette campagne.',
            ], 403);
        }

        // Vérifier que l'utilisateur peut encore modifier ses objectifs
        if (!in_array($userCampaign->objective_status, ['draft', 'returned'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez plus modifier vos objectifs.',
            ], 403);
        }

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            $rows = [];

            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($file->getRealPath(), 'r');
                $header = null;
                while (($line = fgetcsv($handle, 0, ';')) !== false) {
                    if (!$header) {
                        $header = array_map(fn($h) => strtolower(trim($h)), $line);
                        continue;
                    }
                    if (count($line) === count($header)) {
                        $rows[] = array_combine($header, $line);
                    }
                }
                fclose($handle);
            } else {
                if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le format xlsx/xls nécessite le package PhpSpreadsheet. Veuillez utiliser un fichier CSV.',
                    ], 422);
                }
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, true, true, false);
                $rawHeader = array_shift($data);
                $header = array_map(function($h) {
                    $h = trim($h ?? '');
                    $h = mb_strtolower($h, 'UTF-8');
                    return $h;
                }, $rawHeader);
                foreach ($data as $line) {
                    $line = array_values($line);
                    if (count($line) >= count($header)) {
                        $rows[] = array_combine($header, array_slice($line, 0, count($header)));
                    }
                }
            }

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier est vide ou le format est incorrect.',
                ], 422);
            }

            // Normalize header keys for flexible matching
            $headerKeys = array_keys($rows[0]);
            $normalizeKey = function(string $s): string {
                $s = mb_strtolower(trim($s), 'UTF-8');
                // Remove accents
                $map = ['à'=>'a','â'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','œ'=>'oe','æ'=>'ae'];
                return strtr($s, $map);
            };
            $normalizedHeaders = [];
            foreach ($headerKeys as $key) {
                $normalizedHeaders[$normalizeKey($key)] = $key;
            }

            // Map flexible column names to actual keys found in the file
            $colMap = [
                'categorie' => null,
                'intitule'  => null,
                'poids'     => null,
                'description' => null,
            ];
            foreach ($normalizedHeaders as $normalized => $original) {
                if (str_contains($normalized, 'categorie')) $colMap['categorie'] = $original;
                elseif (str_contains($normalized, 'intitule') || str_contains($normalized, 'intitul')) $colMap['intitule'] = $original;
                elseif (str_contains($normalized, 'ponderation') || str_contains($normalized, 'poids') || str_contains($normalized, 'ponderee')) $colMap['poids'] = $original;
                elseif (str_contains($normalized, 'description')) $colMap['description'] = $original;
            }

            $missingCols = [];
            if (!$colMap['categorie']) $missingCols[] = 'Catégorie Objectif';
            if (!$colMap['intitule'])  $missingCols[] = 'Intitulé Objectif';
            if (!$colMap['poids'])     $missingCols[] = 'Pondération';
            if (!empty($missingCols)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Colonne(s) manquante(s) : ' . implode(', ', $missingCols) . '. Colonnes attendues : Catégorie Objectif, Intitulé Objectif, Pondération, Description.',
                ], 422);
            }

            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $i => $row) {
                $categoryRaw = trim($row[$colMap['categorie']] ?? '');
                $title = trim($row[$colMap['intitule']] ?? '');
                $weight = trim($row[$colMap['poids']] ?? '');
                $description = $colMap['description'] ? trim($row[$colMap['description']] ?? '') : '';

                if (empty($categoryRaw) || empty($title)) {
                    $skipped++;
                    continue;
                }

                // Strip numeric prefix like '1- ', '2- ', '3- ' to get plain category name
                $categoryName = preg_replace('/^\d+\s*[-–]\s*/u', '', $categoryRaw);
                $categoryName = trim($categoryName);

                // Find category by name (exact first, then LIKE)
                $category = ObjectiveCategory::where('name', $categoryName)->first()
                    ?? ObjectiveCategory::where('name', 'LIKE', '%' . $categoryName . '%')->first();
                
                if (!$category) {
                    $errors[] = 'Ligne ' . ($i + 2) . ' : catégorie introuvable (' . $categoryName . ')';
                    $skipped++;
                    continue;
                }

                // Validate weight
                if (!empty($weight) && (!is_numeric($weight) || $weight < 0 || $weight > 100)) {
                    $errors[] = 'Ligne ' . ($i + 2) . ' : pondération invalide (' . $weight . ')';
                    $skipped++;
                    continue;
                }

                Objective::create([
                    'user_campaign_uuid' => $userCampaign->uuid,
                    'objective_category_uuid' => $category->uuid,
                    'title' => $title,
                    'description' => $description,
                    'weight' => !empty($weight) ? (int)$weight : 0,
                    'status' => 'pending',
                ]);

                $created++;
            }

            $message = $created . ' objectif(s) importé(s) avec succès.';
            if ($skipped > 0) {
                $message .= ' ' . $skipped . ' ligne(s) ignorée(s).';
            }
            if (!empty($errors)) {
                $message .= ' Erreurs : ' . implode(', ', array_slice($errors, 0, 5));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'urlback' => route('objectives.index'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function importExcelForParticipant(Request $request, UserCampaign $userCampaign)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            $rows = [];

            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($file->getRealPath(), 'r');
                $header = null;
                while (($line = fgetcsv($handle, 0, ';')) !== false) {
                    if (!$header) {
                        $header = array_map(fn($h) => mb_strtolower(trim($h), 'UTF-8'), $line);
                        continue;
                    }
                    if (count($line) === count($header)) {
                        $rows[] = array_combine($header, $line);
                    }
                }
                fclose($handle);
            } else {
                if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le format xlsx/xls nécessite le package PhpSpreadsheet.',
                    ], 422);
                }
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, true, true, false);
                $rawHeader = array_shift($data);
                $header = array_map(fn($h) => mb_strtolower(trim($h ?? ''), 'UTF-8'), $rawHeader);
                foreach ($data as $line) {
                    $line = array_values($line);
                    if (count($line) >= count($header)) {
                        $rows[] = array_combine($header, array_slice($line, 0, count($header)));
                    }
                }
            }

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier est vide ou le format est incorrect.',
                ], 422);
            }

            // Normalize header keys
            $headerKeys = array_keys($rows[0]);
            $normalizeKey = function(string $s): string {
                $s = mb_strtolower(trim($s), 'UTF-8');
                $map = ['à'=>'a','â'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','œ'=>'oe','æ'=>'ae'];
                return strtr($s, $map);
            };
            $normalizedHeaders = [];
            foreach ($headerKeys as $key) {
                $normalizedHeaders[$normalizeKey($key)] = $key;
            }
            $colMap = ['categorie' => null, 'intitule' => null, 'poids' => null, 'description' => null];
            foreach ($normalizedHeaders as $normalized => $original) {
                if (str_contains($normalized, 'categorie')) $colMap['categorie'] = $original;
                elseif (str_contains($normalized, 'intitule') || str_contains($normalized, 'intitul')) $colMap['intitule'] = $original;
                elseif (str_contains($normalized, 'ponderation') || str_contains($normalized, 'poids')) $colMap['poids'] = $original;
                elseif (str_contains($normalized, 'description')) $colMap['description'] = $original;
            }

            $missingCols = [];
            if (!$colMap['categorie']) $missingCols[] = 'Catégorie Objectif';
            if (!$colMap['intitule'])  $missingCols[] = 'Intitulé Objectif';
            if (!$colMap['poids'])     $missingCols[] = 'Pondération';
            if (!empty($missingCols)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Colonne(s) manquante(s) : ' . implode(', ', $missingCols),
                ], 422);
            }

            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $i => $row) {
                $categoryRaw = trim($row[$colMap['categorie']] ?? '');
                $title = trim($row[$colMap['intitule']] ?? '');
                $weight = trim($row[$colMap['poids']] ?? '');
                $description = $colMap['description'] ? trim($row[$colMap['description']] ?? '') : '';

                if (empty($categoryRaw) || empty($title)) { $skipped++; continue; }

                $categoryName = trim(preg_replace('/^\d+\s*[-–]\s*/u', '', $categoryRaw));
                $category = ObjectiveCategory::where('name', $categoryName)->first()
                    ?? ObjectiveCategory::where('name', 'LIKE', '%' . $categoryName . '%')->first();

                if (!$category) {
                    $errors[] = 'Ligne ' . ($i + 2) . ' : catégorie introuvable (' . $categoryName . ')';
                    $skipped++;
                    continue;
                }

                if (!empty($weight) && (!is_numeric($weight) || $weight < 0 || $weight > 100)) {
                    $errors[] = 'Ligne ' . ($i + 2) . ' : pondération invalide (' . $weight . ')';
                    $skipped++;
                    continue;
                }

                Objective::create([
                    'user_campaign_uuid' => $userCampaign->uuid,
                    'objective_category_uuid' => $category->uuid,
                    'title' => $title,
                    'description' => $description,
                    'weight' => !empty($weight) ? (int)$weight : 0,
                    'status' => 'validated',
                ]);
                $created++;
            }

            // Vérifier si la pondération totale atteint 100 pour compléter la phase
            $totalWeight = $userCampaign->objectives()->where('status', 'validated')->sum('weight');
            if ($totalWeight >= 100) {
                $userCampaign->update(['objective_status' => 'completed']);
            }

            $message = $created . ' objectif(s) importé(s) avec succès.';
            if ($skipped > 0) $message .= ' ' . $skipped . ' ligne(s) ignorée(s).';
            if (!empty($errors)) $message .= ' Erreurs : ' . implode(', ', array_slice($errors, 0, 5));

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('assets/documents/modele_import_objectif_axial.xlsx');
        
        if (!file_exists($filePath)) {
            abort(404, 'Fichier modèle introuvable.');
        }

        return response()->download($filePath, 'modele_import_objectif_axial.xlsx');
    }
}
