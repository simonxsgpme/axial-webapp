<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index()
    {
        $entities = Entity::with(['parent', 'children'])->latest()->get();
        return view('entities.index', compact('entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'nullable|string|max:20',
            'category' => 'required|in:direction,service,departement',
            'parent_uuid' => 'nullable|exists:entities,uuid',
        ]);

        Entity::create([
            'name' => $request->name,
            'acronym' => $request->acronym,
            'category' => $request->category,
            'parent_uuid' => $request->parent_uuid ?: null,
        ]);

        return response()->json(['success' => true, 'message' => 'Entité créée avec succès.', 'urlback' => 'back']);
    }

    public function update(Request $request, Entity $entity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'acronym' => 'nullable|string|max:20',
            'category' => 'required|in:direction,service,departement',
            'parent_uuid' => 'nullable|exists:entities,uuid',
        ]);

        // Prevent setting self as parent
        if ($request->parent_uuid === $entity->uuid) {
            return response()->json(['success' => false, 'message' => 'Une entité ne peut pas être son propre parent.'], 422);
        }

        $entity->update([
            'name' => $request->name,
            'acronym' => $request->acronym,
            'category' => $request->category,
            'parent_uuid' => $request->parent_uuid ?: null,
        ]);

        return response()->json(['success' => true, 'message' => 'Entité modifiée avec succès.', 'urlback' => 'back']);
    }

    public function destroy(Entity $entity)
    {
        if ($entity->users()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cette entité est attribuée à des utilisateurs et ne peut pas être supprimée.'], 422);
        }

        $entity->delete();

        return response()->json(['success' => true, 'message' => 'Entité supprimée avec succès.', 'urlback' => 'back']);
    }
}
