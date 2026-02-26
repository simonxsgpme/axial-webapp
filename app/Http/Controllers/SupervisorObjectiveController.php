<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Objective;
use App\Models\ObjectiveCategory;
use App\Models\ObjectiveDecision;
use App\Models\UserCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorObjectiveController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Chercher la campagne en cours
        $campaign = Campaign::whereIn('status', ['objective_in_progress', 'evaluation_in_progress'])->first();

        if (!$campaign) {
            return view('supervisor.objectives', [
                'campaign' => null,
                'subordinates' => collect(),
                'categories' => collect(),
            ]);
        }

        // Récupérer les user_campaigns où l'utilisateur connecté est superviseur
        $subordinates = UserCampaign::where('campaign_uuid', $campaign->uuid)
            ->where('supervisor_uuid', $user->uuid)
            ->with(['user', 'objectives.category', 'objectives.comments.user'])
            ->get();

        $categories = ObjectiveCategory::all();

        return view('supervisor.objectives', [
            'campaign' => $campaign,
            'subordinates' => $subordinates,
            'categories' => $categories,
        ]);
    }

    public function showSubordinate(UserCampaign $userCampaign)
    {
        // Vérifier que l'utilisateur connecté est bien le superviseur
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas le supérieur de cet utilisateur.',
            ], 403);
        }

        $userCampaign->load(['user', 'objectives.category', 'objectives.comments.user', 'decisions.actor']);

        return response()->json([
            'success' => true,
            'userCampaign' => $userCampaign,
        ]);
    }

    public function validateObjective(Request $request, Objective $objective)
    {
        $userCampaign = $objective->userCampaign;

        // Vérifier que l'utilisateur connecté est le superviseur
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à valider cet objectif.',
            ], 403);
        }

        // Vérifier que les objectifs sont soumis
        if ($userCampaign->objective_status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Les objectifs ne sont pas en attente de validation.',
            ], 403);
        }

        $request->validate([
            'weight' => 'required|integer|min:0|max:100',
            'comment' => 'nullable|string',
        ]);

        // Vérifier que la somme des pondérations de la catégorie ne dépasse pas le % de la catégorie
        $category = $objective->category;
        $currentCategoryWeight = $userCampaign->objectives()
            ->where('objective_category_uuid', $objective->objective_category_uuid)
            ->where('uuid', '!=', $objective->uuid)
            ->where('status', 'validated')
            ->sum('weight');

        if (($currentCategoryWeight + $request->weight) > $category->percentage) {
            $remaining = $category->percentage - $currentCategoryWeight;
            return response()->json([
                'success' => false,
                'message' => 'La somme des pondérations pour la catégorie "' . $category->name . '" dépasserait ' . $category->percentage . '%. Restant disponible : ' . $remaining . '%.',
            ], 422);
        }

        $objective->update([
            'status' => 'validated',
            'weight' => $request->weight,
        ]);

        // Vérifier si tous les objectifs sont validés
        $allValidated = $userCampaign->objectives()->where('status', '!=', 'validated')->count() === 0;
        if ($allValidated) {
            $userCampaign->update(['objective_status' => 'completed']);

            ObjectiveDecision::create([
                'user_campaign_uuid' => $userCampaign->uuid,
                'actor_uuid' => Auth::id(),
                'action' => 'completed',
                'comment' => 'Tous les objectifs ont été validés. Phase objectifs terminée.',
            ]);
        }

        $objective->load(['category', 'comments.user']);

        return response()->json([
            'success' => true,
            'message' => 'Objectif validé avec succès.',
            'objective' => $objective,
            'all_validated' => $allValidated,
        ]);
    }

    public function rejectObjective(Request $request, Objective $objective)
    {
        $userCampaign = $objective->userCampaign;

        // Vérifier que l'utilisateur connecté est le superviseur
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à refuser cet objectif.',
            ], 403);
        }

        if ($userCampaign->objective_status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Les objectifs ne sont pas en attente de validation.',
            ], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $objective->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $objective->load(['category', 'comments.user']);

        return response()->json([
            'success' => true,
            'message' => 'Objectif refusé.',
            'objective' => $objective,
        ]);
    }

    public function returnObjectives(Request $request, UserCampaign $userCampaign)
    {
        // Vérifier que l'utilisateur connecté est le superviseur
        if ($userCampaign->supervisor_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à retourner ces objectifs.',
            ], 403);
        }

        if ($userCampaign->objective_status !== 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Les objectifs ne sont pas en attente de validation.',
            ], 403);
        }

        // Tous les objectifs sans décision (pending) sont automatiquement refusés
        $userCampaign->objectives()->where('status', 'pending')->update([
            'status' => 'rejected',
            'rejection_reason' => 'Aucune décision prise — refusé automatiquement.',
        ]);

        $userCampaign->update(['objective_status' => 'returned']);

        // Enregistrer la décision dans la timeline
        ObjectiveDecision::create([
            'user_campaign_uuid' => $userCampaign->uuid,
            'actor_uuid' => Auth::id(),
            'action' => 'returned',
            'comment' => $request->comment ?? 'Objectifs retournés à l\'employé pour correction.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Les objectifs ont été retournés à l\'employé.',
        ]);
    }
}
