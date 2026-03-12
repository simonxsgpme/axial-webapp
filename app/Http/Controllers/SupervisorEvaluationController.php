<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Objective;
use App\Models\EvaluationDecision;
use App\Models\ObjectiveCategory;
use App\Models\UserCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupervisorEvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $campaign = Campaign::whereIn('status', ['objective_in_progress', 'midterm_in_progress', 'evaluation_in_progress'])->first();

        if (!$campaign) {
            return view('supervisor.evaluations', [
                'campaign' => null,
                'subordinates' => collect(),
                'categories' => collect(),
            ]);
        }

        $subordinates = UserCampaign::where('campaign_uuid', $campaign->uuid)
            ->where('supervisor_uuid', $user->uuid)
            ->where('objective_status', 'completed')
            ->with(['user', 'objectives.category'])
            ->get();

        $categories = ObjectiveCategory::all();

        return view('supervisor.evaluations', [
            'campaign' => $campaign,
            'subordinates' => $subordinates,
            'categories' => $categories,
        ]);
    }

    public function showSubordinate(UserCampaign $userCampaign)
    {
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas le supérieur de cet utilisateur.',
            ], 403);
        }

        $userCampaign->load([
            'user',
            'objectives.category',
            'objectives.evaluationComments.user',
            'evaluationDecisions.actor',
        ]);

        return response()->json([
            'success' => true,
            'userCampaign' => $userCampaign,
        ]);
    }

    public function scoreObjective(Request $request, Objective $objective)
    {
        $userCampaign = $objective->userCampaign;

        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à noter cet objectif.',
            ], 403);
        }

        if (!in_array($userCampaign->evaluation_status, ['pending', 'supervisor_draft', 'returned_to_supervisor'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas noter les objectifs dans cet état.',
            ], 403);
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $objective->update([
            'score' => $request->score,
        ]);

        // Passer en supervisor_draft si c'était pending
        if ($userCampaign->evaluation_status === 'pending') {
            $userCampaign->update(['evaluation_status' => 'supervisor_draft']);
        }

        // Recalculer la note globale
        $this->recalculateRating($userCampaign);

        $objective->load(['category', 'evaluationComments.user']);

        return response()->json([
            'success' => true,
            'message' => 'Note enregistrée.',
            'objective' => $objective,
            'rating' => $userCampaign->fresh()->rating,
        ]);
    }

    public function submitToEmployee(Request $request, UserCampaign $userCampaign)
    {
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        if (!in_array($userCampaign->evaluation_status, ['supervisor_draft', 'returned_to_supervisor'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas soumettre l\'évaluation dans cet état.',
            ], 403);
        }

        // Vérifier que tous les objectifs ont une note
        $unscored = $userCampaign->objectives()->whereNull('score')->count();
        if ($unscored > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tous les objectifs doivent avoir une note avant de soumettre. ' . $unscored . ' objectif(s) sans note.',
            ], 422);
        }

        $this->recalculateRating($userCampaign);

        // Save global supervisor comment if provided
        if ($request->filled('supervisor_comment')) {
            $userCampaign->update([
                'evaluation_status' => 'submitted_to_employee',
                'supervisor_comment' => $request->supervisor_comment,
            ]);
        } else {
            $userCampaign->update(['evaluation_status' => 'submitted_to_employee']);
        }

        EvaluationDecision::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'actor_uuid' => Auth::id(),
            'action' => 'submitted_to_employee',
            'comment' => $request->comment ?? 'Évaluation soumise pour consultation.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Évaluation soumise.',
        ]);
    }

    public function addComment(Request $request, Objective $objective)
    {
        $userCampaign = $objective->userCampaign;

        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $objective->evaluationComments()->create([
            'user_uuid' => Auth::id(),
            'content' => $request->content,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté.',
            'comment' => $comment,
        ]);
    }

    public function deleteComment(\App\Models\EvaluationComment $comment)
    {
        if ($comment->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez supprimer que vos propres commentaires.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé.',
        ]);
    }

    public function saveGlobalComment(Request $request, UserCampaign $userCampaign)
    {
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        $request->validate([
            'supervisor_comment' => 'nullable|string',
        ]);

        $userCampaign->update([
            'supervisor_comment' => $request->supervisor_comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire global enregistré.',
        ]);
    }

    public function uploadMidtermFile(Request $request, UserCampaign $userCampaign)
    {
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        $request->validate([
            'midterm_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Supprimer l'ancien fichier s'il existe
        if ($userCampaign->midterm_file && Storage::disk('public')->exists($userCampaign->midterm_file)) {
            Storage::disk('public')->delete($userCampaign->midterm_file);
        }

        // Stocker le nouveau fichier
        $path = $request->file('midterm_file')->store('midterm_files', 'public');

        $userCampaign->update([
            'midterm_file' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fichier mi-parcours importé avec succès.',
            'file_path' => $path,
        ]);
    }

    public function downloadMidtermFile(UserCampaign $userCampaign)
    {
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            abort(403, 'Non autorisé');
        }

        // Si un fichier PDF a été uploadé, le télécharger
        if ($userCampaign->midterm_file && Storage::disk('public')->exists($userCampaign->midterm_file)) {
            $filePath = storage_path('app/public/' . $userCampaign->midterm_file);
            $fileName = 'Fiche_MiParcours_' . str_replace(' ', '_', $userCampaign->user->full_name) . '.pdf';
            return response()->download($filePath, $fileName);
        }

        // Sinon, générer et télécharger le fichier Word
        $campaign = $userCampaign->campaign;
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

    private function recalculateRating(UserCampaign $userCampaign): void
    {
        $objectives = $userCampaign->objectives()->whereNotNull('score')->get();

        if ($objectives->count() > 0) {
            // Calcul : somme de (score × poids / 100)
            // Exemple : Objectif 1 (20%) : 75% → 75 × 20 / 100 = 15
            //           Objectif 2 (30%) : 83% → 83 × 30 / 100 = 25
            //           Note globale = 15 + 25 = 40%
            $rating = $objectives->sum(function ($obj) {
                return ($obj->score * $obj->weight) / 100;
            });

            $userCampaign->update(['rating' => round($rating, 2)]);
        }
    }
}
