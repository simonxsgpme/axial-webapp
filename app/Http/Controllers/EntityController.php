<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    public function index()
    {
        $entities = Entity::latest()->get();
        return view('entities.index', compact('entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:direction,service,departement',
        ]);

        Entity::create([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        return response()->json(['success' => true, 'message' => 'Entité créée avec succès.', 'urlback' => 'back']);
    }

    public function update(Request $request, Entity $entity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:direction,service,departement',
        ]);

        $entity->update([
            'name' => $request->name,
            'category' => $request->category,
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
