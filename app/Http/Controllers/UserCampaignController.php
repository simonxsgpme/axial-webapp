<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use App\Models\UserCampaign;
use Illuminate\Http\Request;

class UserCampaignController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        $request->validate([
            'user_uuids' => 'required|array|min:1',
            'user_uuids.*' => 'exists:users,uuid',
        ]);

        $added = 0;
        $skipped = 0;

        foreach ($request->user_uuids as $userUuid) {
            $exists = UserCampaign::where('user_uuid', $userUuid)
                ->where('campaign_uuid', $campaign->uuid)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $user = User::find($userUuid);

            UserCampaign::create([
                'user_uuid' => $userUuid,
                'campaign_uuid' => $campaign->uuid,
                'supervisor_uuid' => $user->supervisor_uuid,
                'objective_status' => 'draft',
                'evaluation_status' => 'pending',
            ]);

            $added++;
        }

        $message = $added . ' participant(s) ajouté(s).';
        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' déjà présent(s).';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'urlback' => 'back',
        ]);
    }

    public function destroy(Campaign $campaign, UserCampaign $userCampaign)
    {
        if ($userCampaign->campaign_uuid !== $campaign->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'Ce participant n\'appartient pas à cette campagne.',
            ], 422);
        }

        if (!in_array($campaign->status, ['draft', 'objective_in_progress'])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de retirer un participant à ce stade de la campagne.',
            ], 422);
        }

        $userCampaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Participant retiré avec succès.',
            'urlback' => 'back',
        ]);
    }
}
