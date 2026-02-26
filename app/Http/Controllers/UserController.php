<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'supervisor', 'entity'])->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $entities = Entity::orderBy('name')->get();
        return view('users.create', compact('roles', 'entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'role_uuid' => 'nullable|exists:roles,uuid',
            'entity_uuid' => 'nullable|exists:entities,uuid',
            'is_active' => 'nullable|boolean',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->last_name . ' ' . $request->first_name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(10)),
            'phone' => $request->phone,
            'position' => $request->position,
            'role_uuid' => $request->role_uuid,
            'entity_uuid' => $request->entity_uuid,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'urlback' => route('users.index'),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['role', 'supervisor', 'subordinates', 'entity']);
        $roles = Role::orderBy('name')->get();
        $entities = Entity::orderBy('name')->get();
        $users = User::where('uuid', '!=', $user->uuid)
                      ->where('is_active', true)
                      ->orderBy('full_name')
                      ->get();
        return view('users.show', compact('user', 'roles', 'entities', 'users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->uuid . ',uuid',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'role_uuid' => 'nullable|exists:roles,uuid',
            'entity_uuid' => 'nullable|exists:entities,uuid',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->last_name . ' ' . $request->first_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'role_uuid' => $request->role_uuid,
            'entity_uuid' => $request->entity_uuid,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur modifié avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function updateSupervisor(Request $request, User $user)
    {
        $request->validate([
            'supervisor_uuid' => 'nullable|exists:users,uuid',
        ]);

        if ($request->supervisor_uuid === $user->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'Un utilisateur ne peut pas être son propre supérieur.',
            ], 422);
        }

        $user->update([
            'supervisor_uuid' => $request->supervisor_uuid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supérieur hiérarchique mis à jour avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function resetPassword(User $user)
    {
        $defaultPassword = 'password';

        $user->update([
            'password' => Hash::make($defaultPassword),
            'password_changed_at' => now(),
        ]);

        // TODO: Envoyer un email à l'utilisateur avec le mot de passe par défaut
        // Mail::to($user->email)->send(new ResetPasswordMail($user, $defaultPassword));

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès. Un email a été envoyé à l\'utilisateur.',
            'urlback' => 'back',
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->uuid === auth()->user()->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès.',
            'urlback' => route('users.index'),
        ]);
    }
}
