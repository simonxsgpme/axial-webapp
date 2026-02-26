<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Objective;
use App\Models\EvaluationDecision;
use App\Models\ObjectiveCategory;
use App\Models\UserCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorEvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $campaign = Campaign::whereIn('status', ['evaluation_in_progress'])->first();

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
            'score' => 'required|numeric|min:0|max:' . $objective->weight,
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

        $userCampaign->update(['evaluation_status' => 'submitted_to_employee']);

        EvaluationDecision::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'actor_uuid' => Auth::id(),
            'action' => 'submitted_to_employee',
            'comment' => $request->comment ?? 'Évaluation soumise à l\'employé pour consultation.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Évaluation soumise à l\'employé.',
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

    private function recalculateRating(UserCampaign $userCampaign): void
    {
        $objectives = $userCampaign->objectives()->whereNotNull('score')->get();

        if ($objectives->count() > 0) {
            $rating = $objectives->sum('score');
            $userCampaign->update(['rating' => round($rating, 2)]);
        }
    }
}
