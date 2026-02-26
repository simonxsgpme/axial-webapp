<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use App\Models\ObjectiveComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ObjectiveCommentController extends Controller
{
    public function store(Request $request, Objective $objective)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = ObjectiveComment::create([
            'objective_uuid' => $objective->uuid,
            'user_uuid' => Auth::id(),
            'content' => $request->content,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté avec succès.',
            'comment' => $comment,
        ]);
    }

    public function destroy(ObjectiveComment $comment)
    {
        if ($comment->user_uuid !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé avec succès.',
        ]);
    }
}
