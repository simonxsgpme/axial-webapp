<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\EvaluationComment;
use App\Models\EvaluationDecision;
use App\Models\Objective;
use App\Models\ObjectiveCategory;
use App\Models\UserCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $campaign = Campaign::whereIn('status', ['evaluation_in_progress', 'evaluation_completed'])->first();

        if (!$campaign) {
            return view('evaluations.index', [
                'campaign' => null,
                'userCampaign' => null,
                'categories' => collect(),
                'objectives' => collect(),
                'decisions' => collect(),
                'phase' => null,
            ]);
        }

        $userCampaign = UserCampaign::where('campaign_uuid', $campaign->uuid)
            ->where('user_uuid', $user->uuid)
            ->first();

        if (!$userCampaign) {
            return view('evaluations.index', [
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
            ->with(['category', 'evaluationComments.user'])
            ->get();
        $decisions = $userCampaign->evaluationDecisions()->with('actor')->get();

        return view('evaluations.index', [
            'campaign' => $campaign,
            'userCampaign' => $userCampaign,
            'categories' => $categories,
            'objectives' => $objectives,
            'decisions' => $decisions,
            'phase' => $campaign->status,
        ]);
    }

    public function addComment(Request $request, Objective $objective)
    {
        $userCampaign = $objective->userCampaign;

        if ($userCampaign->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        if (!in_array($userCampaign->evaluation_status, ['submitted_to_employee'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas commenter dans cet état.',
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

    public function deleteComment(EvaluationComment $comment)
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

    public function returnToSupervisor(Request $request, UserCampaign $userCampaign)
    {
        if ($userCampaign->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        if ($userCampaign->evaluation_status !== 'submitted_to_employee') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas retourner l\'évaluation dans cet état.',
            ], 403);
        }

        $userCampaign->update(['evaluation_status' => 'returned_to_supervisor']);

        EvaluationDecision::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'actor_uuid' => Auth::id(),
            'action' => 'returned_to_supervisor',
            'comment' => $request->comment ?? 'Évaluation retournée au supérieur.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Évaluation retournée à votre supérieur.',
        ]);
    }

    public function validateEvaluation(Request $request, UserCampaign $userCampaign)
    {
        if ($userCampaign->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé.',
            ], 403);
        }

        if ($userCampaign->evaluation_status !== 'submitted_to_employee') {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas valider l\'évaluation dans cet état.',
            ], 403);
        }

        $userCampaign->update(['evaluation_status' => 'validated']);

        EvaluationDecision::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'actor_uuid' => Auth::id(),
            'action' => 'validated',
            'comment' => $request->comment ?? 'Évaluation validée par l\'employé. Note globale : ' . $userCampaign->rating . '%.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Évaluation validée avec succès. Votre campagne est terminée.',
        ]);
    }

    public function showObjective(Objective $objective)
    {
        $objective->load(['category', 'evaluationComments.user']);

        return response()->json([
            'success' => true,
            'objective' => $objective,
        ]);
    }
}
