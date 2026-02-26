<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Entity;
use App\Models\User;
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
            'evaluation_starts_at' => 'nullable|date',
            'evaluation_stops_at' => 'nullable|date|after_or_equal:evaluation_starts_at',
        ]);

        Campaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'year' => $request->year,
            'objective_starts_at' => $request->objective_starts_at,
            'objective_stops_at' => $request->objective_stops_at,
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
            'evaluation_starts_at' => 'nullable|date',
            'evaluation_stops_at' => 'nullable|date|after_or_equal:evaluation_starts_at',
        ]);

        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'year' => $request->year,
            'objective_starts_at' => $request->objective_starts_at,
            'objective_stops_at' => $request->objective_stops_at,
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
            'action' => 'required|in:start-objectives,complete-objectives,start-evaluations,complete-evaluations,archive',
        ]);

        $transitions = [
            'start-objectives' => ['from' => 'draft', 'to' => 'objective_in_progress'],
            'complete-objectives' => ['from' => 'objective_in_progress', 'to' => 'objective_completed'],
            'start-evaluations' => ['from' => 'objective_completed', 'to' => 'evaluation_in_progress'],
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
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de terminer la phase objectifs : ' . $remaining . ' participant(s) sur ' . $totalParticipants . ' n\'ont pas encore terminé la validation de leurs objectifs.',
                ], 422);
            }
        }

        // Vérifier que tous les participants ont validé leur évaluation avant de clôturer la phase évaluations
        if ($request->action === 'complete-evaluations') {
            $totalParticipants = $campaign->userCampaigns()->count();
            $validatedParticipants = $campaign->userCampaigns()->where('evaluation_status', 'validated')->count();

            if ($totalParticipants === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun participant dans cette campagne.',
                ], 422);
            }

            if ($validatedParticipants < $totalParticipants) {
                $remaining = $totalParticipants - $validatedParticipants;
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de terminer la phase évaluations : ' . $remaining . ' participant(s) sur ' . $totalParticipants . ' n\'ont pas encore validé leur évaluation.',
                ], 422);
            }
        }

        $campaign->update(['status' => $transition['to']]);

        $labels = [
            'objective_in_progress' => 'Phase objectifs démarrée.',
            'objective_completed' => 'Phase objectifs terminée.',
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

        // Grouper par entité
        $entities = Entity::orderBy('name')->get();
        $entitiesWithParticipants = [];
        foreach ($entities as $entity) {
            $members = $participants->filter(function ($uc) use ($entity) {
                return $uc->user && $uc->user->entity_uuid === $entity->uuid;
            })->sortByDesc('rating')->values();
            if ($members->count() > 0) {
                $entitiesWithParticipants[] = [
                    'entity' => $entity,
                    'members' => $members,
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
                'entity' => (object) ['name' => 'Sans entité', 'uuid' => null],
                'members' => $noEntity,
                'avg_rating' => round($noEntity->whereNotNull('rating')->avg('rating'), 1),
            ];
        }

        return view('campaigns.results', compact(
            'campaign', 'globalScore', 'totalParticipants', 'validatedCount',
            'podium', 'entitiesWithParticipants'
        ));
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
