@extends('layouts.app')

@section('title', $user->full_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm waves-effect me-2">
                    <i class="fi fi-rr-arrow-left me-1"></i> Retour
                </a>
                <span class="fw-bold fs-5">{{ $user->full_name }}</span>
                @if($user->is_active)
                    <span class="badge bg-success-subtle text-success ms-2">Actif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger ms-2">Inactif</span>
                @endif
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab" aria-controls="info-pane" aria-selected="true">
                    <i class="fi fi-rr-user me-1"></i> Informations
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit-pane" type="button" role="tab" aria-controls="edit-pane" aria-selected="false">
                    <i class="fi fi-rr-pencil me-1"></i> Modification
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="eval-tab" data-bs-toggle="tab" data-bs-target="#eval-pane" type="button" role="tab" aria-controls="eval-pane" aria-selected="false">
                    <i class="fi fi-rr-chart-histogram me-1"></i> Résultat Évaluation
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content mt-3" id="userTabsContent">

            <!-- Tab 1: Informations -->
            <div class="tab-pane fade show active" id="info-pane" role="tabpanel" aria-labelledby="info-tab">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                <div class="avatar avatar-xxl mx-auto mb-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="rounded-circle">
                                    @else
                                        <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold fs-1">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <h5 class="mb-1">{{ $user->full_name }}</h5>
                                <p class="text-muted mb-0">{{ $user->position ?? 'Poste non défini' }}</p>
                                @if($user->role)
                                    <span class="badge bg-primary-subtle text-primary mt-2">{{ $user->role->name }}</span>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted fw-semibold" style="width: 200px;">Nom</td>
                                                <td>{{ $user->last_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Prénom</td>
                                                <td>{{ $user->first_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Email</td>
                                                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Téléphone</td>
                                                <td>{{ $user->phone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Poste</td>
                                                <td>{{ $user->position ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Rôle</td>
                                                <td>
                                                    @if($user->role)
                                                        <span class="badge bg-primary-subtle text-primary">{{ $user->role->name }}</span>
                                                    @else
                                                        <span class="text-muted">Non défini</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Entité</td>
                                                <td>
                                                    @if($user->entity)
                                                        <span class="badge bg-info-subtle text-info">{{ $user->entity->name }} ({{ ucfirst($user->entity->category) }})</span>
                                                    @else
                                                        <span class="text-muted">Non définie</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Supérieur hiérarchique</td>
                                                <td>
                                                    @if($user->supervisor)
                                                        <a href="{{ route('users.show', $user->supervisor->uuid) }}">{{ $user->supervisor->full_name }}</a>
                                                    @else
                                                        <span class="text-muted">Non défini</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Statut</td>
                                                <td>
                                                    @if($user->is_active)
                                                        <span class="badge bg-success-subtle text-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">Inactif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Dernière connexion</td>
                                                <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold">Date de création</td>
                                                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <button type="button" class="btn btn-outline-warning waves-effect waves-light" onclick="resetPassword('{{ $user->uuid }}')">
                            <i class="fi fi-rr-lock me-1"></i> Réinitialiser le mot de passe
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Modification -->
            <div class="tab-pane fade" id="edit-pane" role="tabpanel" aria-labelledby="edit-tab">
                <!-- Card Modification des informations -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fi fi-rr-pencil me-1"></i> Modifier les informations</h5>
                    </div>
                    <div class="card-body">
                        <form class="sendForm" method="POST" action="{{ route('users.update', $user->uuid) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_last_name" name="last_name" value="{{ $user->last_name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_first_name" name="first_name" value="{{ $user->first_name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="edit_email" name="email" value="{{ $user->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_phone" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" id="edit_phone" name="phone" value="{{ $user->phone }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_position" class="form-label">Poste</label>
                                    <input type="text" class="form-control" id="edit_position" name="position" value="{{ $user->position }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_role_uuid" class="form-label">Rôle</label>
                                    <select class="form-select" id="edit_role_uuid" name="role_uuid">
                                        <option value="">-- Sélectionner un rôle --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->uuid }}" {{ $user->role_uuid === $role->uuid ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_entity_uuid" class="form-label">Entité</label>
                                    <select class="form-select" id="edit_entity_uuid" name="entity_uuid">
                                        <option value="">-- Sélectionner une entité --</option>
                                        @foreach($entities as $entity)
                                            <option value="{{ $entity->uuid }}" {{ $user->entity_uuid === $entity->uuid ? 'selected' : '' }}>{{ $entity->name }} ({{ ucfirst($entity->category) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_is_active">Compte actif</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <i class="fi fi-rr-check me-1"></i> Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card Supérieur hiérarchique -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fi fi-rr-users me-1"></i> Supérieur hiérarchique</h5>
                    </div>
                    <div class="card-body">
                        <form class="sendForm" method="POST" action="{{ route('users.update-supervisor', $user->uuid) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <div class="row align-items-end">
                                <div class="col-md-8 mb-3">
                                    <label for="supervisor_uuid" class="form-label">Sélectionner le supérieur hiérarchique</label>
                                    <select class="form-select" id="supervisor_uuid" name="supervisor_uuid">
                                        <option value="">-- Aucun supérieur --</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->uuid }}" {{ $user->supervisor_uuid === $u->uuid ? 'selected' : '' }}>{{ $u->full_name }} {{ $u->position ? '(' . $u->position . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light w-100">
                                        <i class="fi fi-rr-check me-1"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                            @if($user->supervisor)
                            <div class="mt-2 p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Supérieur actuel :</small>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-text rounded-circle bg-info-subtle text-info fw-bold">{{ strtoupper(substr($user->supervisor->first_name, 0, 1) . substr($user->supervisor->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $user->supervisor->full_name }}</span>
                                        <small class="text-muted d-block">{{ $user->supervisor->position ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Résultat Évaluation -->
            <div class="tab-pane fade" id="eval-pane" role="tabpanel" aria-labelledby="eval-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fi fi-rr-chart-histogram me-1"></i> Résultat Évaluation</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-muted py-5">
                            <i class="fi fi-rr-chart-histogram fs-1 d-block mb-3"></i>
                            <h6>Aucune évaluation disponible</h6>
                            <p class="mb-0">Les résultats d'évaluation apparaîtront ici une fois le module activé.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function resetPassword(uuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Réinitialisation du mot de passe',
            text: 'Le mot de passe sera réinitialisé et un email sera envoyé à l\'utilisateur. Continuer ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, réinitialiser',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + uuid + '/reset-password',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        loader();
                    },
                    success: function (data) {
                        loader('hide');
                        if (data.success) {
                            sendSuccess(data.message, 'back');
                        } else {
                            SendError(data.message);
                        }
                    },
                    error: function (data) {
                        loader('hide');
                        SendError(data.responseJSON.message ?? 'Une erreur est survenue');
                    }
                });
            }
        });
    }
</script>
@endpush
