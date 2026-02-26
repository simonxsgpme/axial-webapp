<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\UserCampaignController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\ObjectiveCommentController;
use App\Http\Controllers\SupervisorObjectiveController;
use App\Http\Controllers\SupervisorEvaluationController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirection racine vers le dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes d'authentification (accessibles uniquement aux invités)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Déconnexion (accessible uniquement aux authentifiés)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Changement de mot de passe obligatoire (première connexion)
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/password/change', [ChangePasswordController::class, 'update'])->name('password.change.update');
});

// Routes protégées par authentification
Route::middleware(['auth', 'force.password.change'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Configuration (prefix settings)
    Route::prefix('settings')->name('settings.')->group(function () {
        // Rôles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // Permissions d'un rôle
        Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'show'])->name('roles.permissions');
        Route::post('/role-permissions/{rolePermission}/toggle', [RolePermissionController::class, 'toggle'])->name('role-permissions.toggle');

        // Permissions
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Entités
        Route::get('/entities', [EntityController::class, 'index'])->name('entities.index');
        Route::post('/entities', [EntityController::class, 'store'])->name('entities.store');
        Route::put('/entities/{entity}', [EntityController::class, 'update'])->name('entities.update');
        Route::delete('/entities/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy');
    });

    // Campagnes
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::post('/campaigns/{campaign}/status', [CampaignController::class, 'updateStatus'])->name('campaigns.update-status');
    Route::post('/campaigns/{campaign}/participants', [UserCampaignController::class, 'store'])->name('campaigns.participants.store');
    Route::delete('/campaigns/{campaign}/participants/{userCampaign}', [UserCampaignController::class, 'destroy'])->name('campaigns.participants.destroy');
    Route::get('/campaigns/{campaign}/results', [CampaignController::class, 'results'])->name('campaigns.results');
    Route::get('/campaigns/{campaign}/participants/{userCampaign}/objectives', [CampaignController::class, 'editParticipantObjectives'])->name('campaigns.participants.objectives');
    Route::post('/campaigns/{campaign}/participants/{userCampaign}/objectives', [CampaignController::class, 'storeParticipantObjective'])->name('campaigns.participants.objectives.store');
    Route::put('/campaigns/{campaign}/participants/{userCampaign}/objectives/{objective}', [CampaignController::class, 'updateParticipantObjective'])->name('campaigns.participants.objectives.update');
    Route::delete('/campaigns/{campaign}/participants/{userCampaign}/objectives/{objective}', [CampaignController::class, 'destroyParticipantObjective'])->name('campaigns.participants.objectives.destroy');
    Route::get('/campaigns/{campaign}/participants/{userCampaign}/pdf-objectives', [CampaignController::class, 'pdfObjectives'])->name('campaigns.participants.pdf-objectives');
    Route::post('/campaigns/{campaign}/participants/{userCampaign}/skip-phase', [CampaignController::class, 'skipPhase'])->name('campaigns.participants.skip-phase');
    Route::get('/campaigns/{campaign}/participants/{userCampaign}/midterm-report', [CampaignController::class, 'midtermReport'])->name('campaigns.participants.midterm-report');
    Route::post('/campaigns/{campaign}/participants/{userCampaign}/upload-midterm', [CampaignController::class, 'uploadMidterm'])->name('campaigns.participants.upload-midterm');
    Route::get('/campaigns/{campaign}/participants/{userCampaign}/download-midterm', [CampaignController::class, 'downloadMidterm'])->name('campaigns.participants.download-midterm');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    // Objectifs (vue employé)
    Route::get('/objectives', [ObjectiveController::class, 'index'])->name('objectives.index');
    Route::post('/objectives', [ObjectiveController::class, 'store'])->name('objectives.store');
    Route::get('/objectives/{objective}', [ObjectiveController::class, 'show'])->name('objectives.show');
    Route::put('/objectives/{objective}', [ObjectiveController::class, 'update'])->name('objectives.update');
    Route::delete('/objectives/{objective}', [ObjectiveController::class, 'destroy'])->name('objectives.destroy');

    Route::post('/objectives/submit', [ObjectiveController::class, 'submit'])->name('objectives.submit');

    // Commentaires d'objectifs
    Route::post('/objectives/{objective}/comments', [ObjectiveCommentController::class, 'store'])->name('objectives.comments.store');
    Route::delete('/objective-comments/{comment}', [ObjectiveCommentController::class, 'destroy'])->name('objectives.comments.destroy');

    // Validation objectifs (supérieur)
    Route::get('/supervisor/objectives', [SupervisorObjectiveController::class, 'index'])->name('supervisor.objectives.index');
    Route::get('/supervisor/objectives/{userCampaign}', [SupervisorObjectiveController::class, 'showSubordinate'])->name('supervisor.objectives.show');
    Route::post('/supervisor/objectives/{objective}/validate', [SupervisorObjectiveController::class, 'validateObjective'])->name('supervisor.objectives.validate');
    Route::post('/supervisor/objectives/{objective}/reject', [SupervisorObjectiveController::class, 'rejectObjective'])->name('supervisor.objectives.reject');
    Route::post('/supervisor/objectives/{userCampaign}/return', [SupervisorObjectiveController::class, 'returnObjectives'])->name('supervisor.objectives.return');

    // Évaluations (vue employé)
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/objective/{objective}', [EvaluationController::class, 'showObjective'])->name('evaluations.objective.show');
    Route::post('/evaluations/{objective}/comments', [EvaluationController::class, 'addComment'])->name('evaluations.comments.store');
    Route::delete('/evaluation-comments/{comment}', [EvaluationController::class, 'deleteComment'])->name('evaluations.comments.destroy');
    Route::post('/evaluations/{userCampaign}/return', [EvaluationController::class, 'returnToSupervisor'])->name('evaluations.return');
    Route::post('/evaluations/{userCampaign}/validate', [EvaluationController::class, 'validateEvaluation'])->name('evaluations.validate');

    // Évaluations (supérieur)
    Route::get('/supervisor/evaluations', [SupervisorEvaluationController::class, 'index'])->name('supervisor.evaluations.index');
    Route::get('/supervisor/evaluations/{userCampaign}', [SupervisorEvaluationController::class, 'showSubordinate'])->name('supervisor.evaluations.show');
    Route::post('/supervisor/evaluations/{objective}/score', [SupervisorEvaluationController::class, 'scoreObjective'])->name('supervisor.evaluations.score');
    Route::post('/supervisor/evaluations/{userCampaign}/submit', [SupervisorEvaluationController::class, 'submitToEmployee'])->name('supervisor.evaluations.submit');
    Route::post('/supervisor/evaluations/{objective}/comments', [SupervisorEvaluationController::class, 'addComment'])->name('supervisor.evaluations.comments.store');
    Route::delete('/supervisor/evaluation-comments/{comment}', [SupervisorEvaluationController::class, 'deleteComment'])->name('supervisor.evaluations.comments.destroy');
    Route::post('/supervisor/evaluations/{userCampaign}/global-comment', [SupervisorEvaluationController::class, 'saveGlobalComment'])->name('supervisor.evaluations.global-comment');

    // Utilisateurs
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/supervisor', [UserController::class, 'updateSupervisor'])->name('users.update-supervisor');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/import', [UserController::class, 'importExcel'])->name('users.import');
    Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
