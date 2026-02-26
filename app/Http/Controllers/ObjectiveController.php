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
        if ($objective->userCampaign->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à modifier cet objectif.',
            ], 403);
        }

        // Vérifier que l'employé peut encore modifier (draft ou returned) et objectif non validé
        $userCampaign = $objective->userCampaign;
        if (!in_array($userCampaign->objective_status, ['draft', 'returned'])) {
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
        $totalWeight = $userCampaign->objectives()->sum('weight');
        if ($totalWeight !== 100) {
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
}
