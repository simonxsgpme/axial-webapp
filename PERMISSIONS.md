# Système de Permissions - AXIAL SGPME

## Vue d'ensemble

Le système de permissions permet de contrôler l'accès aux fonctionnalités de l'application en fonction des rôles et permissions assignés aux utilisateurs.

## Rôles disponibles

### 1. **Administrateur** (`administrateur`)
- Accès complet à toutes les fonctionnalités
- Gestion des utilisateurs, rôles, permissions, entités
- Gestion des campagnes et catégories d'objectifs
- Accès à toutes les statistiques

### 2. **Manager** (`manager`)
- Gestion de ses propres objectifs
- Validation des objectifs de ses collaborateurs
- Évaluation de ses collaborateurs
- Accès aux fiches objectifs et mi-parcours
- Tableau de bord avec statistiques de son équipe

### 3. **Collaborateur** (`employe`)
- Gestion de ses propres objectifs
- Modification de ses objectifs pendant la phase mi-parcours
- Consultation de son évaluation
- Tableau de bord personnel

## Catégories de permissions

### Utilisateurs
- `voir-utilisateurs` - Voir la liste des utilisateurs
- `creer-utilisateurs` - Créer de nouveaux utilisateurs
- `modifier-utilisateurs` - Modifier les informations des utilisateurs
- `supprimer-utilisateurs` - Supprimer des utilisateurs
- `importer-utilisateurs` - Importer des utilisateurs via Excel

### Rôles & Permissions
- `voir-roles` - Voir les rôles
- `gerer-roles` - Créer/modifier/supprimer des rôles
- `gerer-permissions` - Assigner des permissions aux rôles

### Entités
- `voir-entites` - Voir les entités
- `gerer-entites` - Gérer les entités (directions, services, départements)

### Campagnes
- `voir-campagnes` - Voir les campagnes
- `creer-campagnes` - Créer des campagnes
- `modifier-campagnes` - Modifier des campagnes
- `supprimer-campagnes` - Supprimer des campagnes
- `gerer-phases-campagnes` - Gérer les phases de campagne

### Objectifs
- `voir-mes-objectifs` - Voir ses propres objectifs
- `creer-mes-objectifs` - Créer ses objectifs
- `modifier-mes-objectifs` - Modifier ses objectifs
- `supprimer-mes-objectifs` - Supprimer ses objectifs
- `soumettre-mes-objectifs` - Soumettre ses objectifs pour validation

### Validation
- `voir-objectifs-collaborateurs` - Voir les objectifs des collaborateurs
- `valider-objectifs` - Valider les objectifs
- `rejeter-objectifs` - Rejeter les objectifs
- `telecharger-fiche-objectifs` - Télécharger la fiche objectifs

### Mi-parcours
- `modifier-objectifs-midterm` - Modifier les objectifs pendant la phase mi-parcours
- `voir-evaluations-midterm` - Voir les évaluations mi-parcours
- `telecharger-fiche-midterm` - Télécharger la fiche mi-parcours
- `importer-fiche-midterm` - Importer la fiche mi-parcours (PDF)

### Évaluation
- `voir-evaluations` - Voir les évaluations
- `evaluer-collaborateurs` - Évaluer les collaborateurs
- `valider-evaluations` - Valider les évaluations
- `voir-mon-evaluation` - Voir sa propre évaluation

### Catégories
- `voir-categories-objectifs` - Voir les catégories d'objectifs
- `gerer-categories-objectifs` - Gérer les catégories d'objectifs

### Dashboard
- `voir-tableau-de-bord` - Accéder au tableau de bord
- `voir-statistiques-globales` - Voir les statistiques globales

## Utilisation dans le code

### 1. Dans les contrôleurs

```php
// Vérifier une permission
if (!auth()->user()->hasPermission('voir-utilisateurs')) {
    abort(403, 'Accès non autorisé');
}

// Vérifier plusieurs permissions (au moins une)
if (!auth()->user()->hasAnyPermission(['voir-utilisateurs', 'gerer-utilisateurs'])) {
    abort(403, 'Accès non autorisé');
}

// Vérifier plusieurs permissions (toutes requises)
if (!auth()->user()->hasAllPermissions(['voir-utilisateurs', 'modifier-utilisateurs'])) {
    abort(403, 'Accès non autorisé');
}
```

### 2. Dans les routes (web.php)

```php
// Route avec une permission
Route::get('/users', [UserController::class, 'index'])
    ->middleware(['auth', 'permission:voir-utilisateurs']);

// Route avec plusieurs permissions
Route::post('/users', [UserController::class, 'store'])
    ->middleware(['auth', 'permission:creer-utilisateurs']);

// Groupe de routes avec permission
Route::middleware(['auth', 'permission:gerer-campagnes'])->group(function () {
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
});
```

### 3. Dans les vues Blade

```blade
{{-- Afficher un élément si l'utilisateur a la permission --}}
@hasPermission('creer-utilisateurs')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        Ajouter un utilisateur
    </a>
@endhasPermission

{{-- Afficher si l'utilisateur a au moins une des permissions --}}
@hasAnyPermission('voir-utilisateurs', 'gerer-utilisateurs')
    <li class="nav-item">
        <a href="{{ route('users.index') }}">Utilisateurs</a>
    </li>
@endhasAnyPermission

{{-- Afficher si l'utilisateur a toutes les permissions --}}
@hasAllPermissions('voir-campagnes', 'modifier-campagnes')
    <button class="btn btn-warning">Modifier la campagne</button>
@endhasAllPermissions
```

## Configuration des permissions

### Assigner des permissions à un rôle

Les permissions sont assignées automatiquement lors du seeding via `PermissionSeeder.php`.

Pour modifier les permissions d'un rôle :

1. Accéder à la page de gestion des rôles (si vous avez la permission `gerer-permissions`)
2. Sélectionner le rôle à modifier
3. Cocher/décocher les permissions souhaitées
4. Enregistrer

### Permissions par défaut

**Administrateur** : Toutes les permissions

**Manager** :
- Gestion de ses objectifs
- Validation des objectifs des collaborateurs
- Évaluation des collaborateurs
- Accès aux fiches et statistiques

**Collaborateur** :
- Gestion de ses objectifs
- Modification pendant la phase mi-parcours
- Consultation de son évaluation
- Tableau de bord personnel

## Seeding

Pour initialiser les permissions et rôles :

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=PersonnelSeeder
```

Ou tout réinitialiser :

```bash
php artisan migrate:fresh --seed
```

## Bonnes pratiques

1. **Toujours vérifier les permissions** dans les contrôleurs pour la sécurité backend
2. **Utiliser les directives Blade** pour masquer les éléments UI inaccessibles
3. **Appliquer le middleware** sur les routes sensibles
4. **Principe du moindre privilège** : donner uniquement les permissions nécessaires
5. **Tester les permissions** après chaque modification de rôle

## Exemples d'implémentation

### Exemple 1 : Restreindre l'accès à la liste des utilisateurs

**Route** :
```php
Route::get('/users', [UserController::class, 'index'])
    ->middleware(['auth', 'permission:voir-utilisateurs']);
```

**Contrôleur** :
```php
public function index()
{
    if (!auth()->user()->hasPermission('voir-utilisateurs')) {
        abort(403);
    }
    
    $users = User::all();
    return view('users.index', compact('users'));
}
```

**Vue** :
```blade
@hasPermission('voir-utilisateurs')
    <h1>Liste des utilisateurs</h1>
    {{-- Contenu --}}
@else
    <p>Vous n'avez pas accès à cette page.</p>
@endhasPermission
```

### Exemple 2 : Bouton conditionnel

```blade
<div class="actions">
    @hasPermission('modifier-utilisateurs')
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Modifier</a>
    @endhasPermission
    
    @hasPermission('supprimer-utilisateurs')
        <button onclick="deleteUser('{{ $user->uuid }}')" class="btn btn-danger">Supprimer</button>
    @endhasPermission
</div>
```

## Dépannage

### L'utilisateur ne peut pas accéder à une fonctionnalité

1. Vérifier que l'utilisateur a un rôle assigné
2. Vérifier que le rôle a la permission activée dans `role_permissions`
3. Vider le cache : `php artisan cache:clear`
4. Vérifier les logs Laravel pour les erreurs 403

### Erreur "Permission not found"

1. Vérifier que la permission existe dans la table `permissions`
2. Re-exécuter le seeder : `php artisan db:seed --class=PermissionSeeder`

### Les permissions ne se mettent pas à jour

1. Vider le cache de l'application
2. Se déconnecter et se reconnecter
3. Vérifier la table `role_permissions` pour le statut de la permission
