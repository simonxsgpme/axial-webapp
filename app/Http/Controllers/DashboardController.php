<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use App\Models\UserCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function data()
    {
        $user = Auth::user();

        // ===== MINI CARDS STATS =====
        $activeCampaigns = Campaign::whereIn('status', ['objective_in_progress', 'objective_completed', 'evaluation_in_progress'])->count();
        $totalCampaigns = Campaign::count();
        $totalUsers = User::where('is_active', true)->count();

        // Mes objectifs (campagne active)
        $myActiveCampaign = UserCampaign::where('user_uuid', $user->uuid)
            ->whereHas('campaign', fn($q) => $q->whereIn('status', ['objective_in_progress', 'objective_completed', 'evaluation_in_progress', 'evaluation_completed']))
            ->with('campaign')
            ->latest()
            ->first();

        $myObjectivesCount = 0;
        $myRating = null;
        $myEvalStatus = null;
        $myEvalStatusLabel = null;
        $myEvalStatusColor = null;
        $myCampaignName = null;
        if ($myActiveCampaign) {
            $myObjectivesCount = $myActiveCampaign->objectives()->count();
            $myRating = $myActiveCampaign->rating;
            $myEvalStatus = $myActiveCampaign->evaluation_status;
            $myEvalStatusLabel = $myActiveCampaign->evaluation_status_label;
            $myEvalStatusColor = $myActiveCampaign->evaluation_status_color;
            $myCampaignName = $myActiveCampaign->campaign->name;
        }

        // Collaborateurs à évaluer (superviseur)
        $subordinatesToEvaluate = UserCampaign::where('supervisor_uuid', $user->uuid)
            ->whereHas('campaign', fn($q) => $q->whereIn('status', ['evaluation_in_progress']))
            ->where('evaluation_status', '!=', 'validated')
            ->count();

        $totalSubordinates = UserCampaign::where('supervisor_uuid', $user->uuid)
            ->whereHas('campaign', fn($q) => $q->whereIn('status', ['evaluation_in_progress', 'evaluation_completed']))
            ->count();

        // ===== CAMPAGNE EN COURS =====
        $currentCampaign = Campaign::whereNotIn('status', ['draft', 'archived'])
            ->with(['userCampaigns'])
            ->latest()
            ->first();

        $campaignData = null;
        if ($currentCampaign) {
            $uc = $currentCampaign->userCampaigns;
            $objectivesCompleted = $uc->where('objective_status', 'completed')->count();
            $evalsValidated = $uc->where('evaluation_status', 'validated')->count();
            $campaignData = [
                'uuid' => $currentCampaign->uuid,
                'name' => $currentCampaign->name,
                'year' => $currentCampaign->year,
                'status' => $currentCampaign->status,
                'status_label' => $currentCampaign->status_label,
                'status_color' => $currentCampaign->status_color,
                'total_participants' => $uc->count(),
                'objectives_completed' => $objectivesCompleted,
                'evals_validated' => $evalsValidated,
                'objective_starts_at' => $currentCampaign->objective_starts_at?->format('d/m/Y'),
                'objective_stops_at' => $currentCampaign->objective_stops_at?->format('d/m/Y'),
                'evaluation_starts_at' => $currentCampaign->evaluation_starts_at?->format('d/m/Y'),
                'evaluation_stops_at' => $currentCampaign->evaluation_stops_at?->format('d/m/Y'),
            ];
        }

        // ===== COLLABORATEURS DU SUPERVISEUR =====
        $subordinates = UserCampaign::where('supervisor_uuid', $user->uuid)
            ->whereHas('campaign', fn($q) => $q->whereNotIn('status', ['draft', 'archived']))
            ->with(['user.entity', 'campaign'])
            ->latest()
            ->get()
            ->map(fn($uc) => [
                'full_name' => $uc->user->full_name,
                'position' => $uc->user->position,
                'initials' => strtoupper(substr($uc->user->first_name, 0, 1) . substr($uc->user->last_name, 0, 1)),
                'entity' => $uc->user->entity?->name,
                'campaign' => $uc->campaign->name,
                'objective_status_label' => $uc->objective_status_label,
                'objective_status_color' => $uc->objective_status_color,
                'evaluation_status_label' => $uc->evaluation_status_label,
                'evaluation_status_color' => $uc->evaluation_status_color,
                'rating' => $uc->rating,
                'rating_color' => $uc->rating_color,
                'rating_level' => $uc->rating_level,
            ]);

        // ===== RÉPARTITION DES NOTES (graphique) =====
        $ratingDistribution = ['Insuffisant' => 0, 'Passable' => 0, 'Satisfaisant' => 0, 'Bien' => 0, 'Excellent' => 0];
        if ($currentCampaign) {
            $rated = $currentCampaign->userCampaigns->whereNotNull('rating');
            foreach ($rated as $uc) {
                $level = $uc->rating_level;
                if ($level && isset($ratingDistribution[$level])) {
                    $ratingDistribution[$level]++;
                }
            }
        }

        // ===== DERNIÈRES CAMPAGNES =====
        $recentCampaigns = Campaign::latest()->take(5)->get()->map(fn($c) => [
            'uuid' => $c->uuid,
            'name' => $c->name,
            'year' => $c->year,
            'status_label' => $c->status_label,
            'status_color' => $c->status_color,
        ]);

        return response()->json([
            'user' => [
                'first_name' => $user->first_name,
                'full_name' => $user->full_name,
                'position' => $user->position,
            ],
            'stats' => [
                'active_campaigns' => $activeCampaigns,
                'total_campaigns' => $totalCampaigns,
                'total_users' => $totalUsers,
                'my_objectives' => $myObjectivesCount,
                'my_rating' => $myRating,
                'my_eval_status' => $myEvalStatus,
                'my_eval_status_label' => $myEvalStatusLabel,
                'my_eval_status_color' => $myEvalStatusColor,
                'my_campaign_name' => $myCampaignName,
                'subordinates_to_evaluate' => $subordinatesToEvaluate,
                'total_subordinates' => $totalSubordinates,
            ],
            'current_campaign' => $campaignData,
            'subordinates' => $subordinates,
            'rating_distribution' => $ratingDistribution,
            'recent_campaigns' => $recentCampaigns,
        ]);
    }
}
