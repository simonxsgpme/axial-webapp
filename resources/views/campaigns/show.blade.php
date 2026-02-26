@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary btn-sm waves-effect me-2">
                    <i class="fi fi-rr-arrow-left me-1"></i> Retour
                </a>
                <span class="fw-bold fs-5">{{ $campaign->name }}</span>
                {{-- <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }} ms-2">{{ $campaign->status_label }}</span> --}}
            </div>
            <div class="d-flex gap-2">
                @if(in_array($campaign->status, ['evaluation_completed', 'archived']))
                <a href="{{ route('campaigns.results', $campaign->uuid) }}" class="btn btn-success btn-sm waves-effect waves-light">
                    <i class="fi fi-rr-chart-pie me-1"></i> Résultats de la campagne
                </a>
                @elseif($campaign->status !== 'archived')
                <button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#editCampaignModal">
                    <i class="fi fi-rr-pencil me-1"></i> Modifier
                </button>
                @endif
                @if($campaign->next_action)
                <button type="button" class="btn btn-{{ $campaign->next_action['color'] }} btn-sm waves-effect waves-light" onclick="updateStatus('{{ $campaign->uuid }}', '{{ $campaign->next_action['action'] }}', '{{ $campaign->next_action['label'] }}')">
                    <i class="fi {{ $campaign->next_action['icon'] }} me-1"></i> {{ $campaign->next_action['label'] }}
                </button>
                @endif
            </div>
        </div>

        <!-- Informations générales -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fi fi-rr-info me-1"></i> Informations générales</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold" style="width: 160px;">Nom</td>
                                        <td>{{ $campaign->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Année</td>
                                        <td>{{ $campaign->year }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <td class="text-muted fw-semibold">Description</td>
                                        <td>{{ $campaign->description ?? '-' }}</td>
                                    </tr> --}}
                                    <tr>
                                        <td class="text-muted fw-semibold">Statut</td>
                                        <td><span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }}">{{ $campaign->status_label }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Date de création</td>
                                        <td>{{ $campaign->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row g-3">
                    <!-- Phase 1: Fixation des Objectifs -->
                    <div class="col-12">
                        <div class="card border-start border-primary border-3">
                            <div class="card-body py-2">
                                <h6 class="fw-semibold mb-2"><i class="fi fi-rr-bullseye me-1 text-primary"></i> 1. Fixation des Objectifs
                                    @if(in_array($campaign->status, ['objective_in_progress']))
                                    <span class="badge bg-primary-subtle text-primary ms-1">En cours</span>
                                    @elseif(!in_array($campaign->status, ['draft']))
                                    <span class="badge bg-success-subtle text-success ms-1"><i class="fi fi-rr-check"></i></span>
                                    @endif
                                </h6>
                                <div class="row">
                                    <div class="col-6"><small class="text-muted d-block">Début</small><span class="fw-semibold">{{ $campaign->objective_starts_at ? $campaign->objective_starts_at->format('d/m/Y') : 'Non planifié' }}</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Fin</small><span class="fw-semibold">{{ $campaign->objective_stops_at ? $campaign->objective_stops_at->format('d/m/Y') : 'Non planifié' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Phase 2: Évaluation Mi-parcours -->
                    <div class="col-12">
                        <div class="card border-start border-info border-3">
                            <div class="card-body py-2">
                                <h6 class="fw-semibold mb-2"><i class="fi fi-rr-refresh me-1 text-info"></i> 2. Évaluation Mi-parcours
                                    @if(in_array($campaign->status, ['midterm_in_progress']))
                                    <span class="badge bg-info-subtle text-info ms-1">En cours</span>
                                    @elseif(in_array($campaign->status, ['midterm_completed', 'evaluation_in_progress', 'evaluation_completed', 'archived']))
                                    <span class="badge bg-success-subtle text-success ms-1"><i class="fi fi-rr-check"></i></span>
                                    @endif
                                </h6>
                                <div class="row">
                                    <div class="col-6"><small class="text-muted d-block">Début</small><span class="fw-semibold">{{ $campaign->midterm_starts_at ? $campaign->midterm_starts_at->format('d/m/Y') : 'Non planifié' }}</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Fin</small><span class="fw-semibold">{{ $campaign->midterm_stops_at ? $campaign->midterm_stops_at->format('d/m/Y') : 'Non planifié' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Phase 3: Évaluation Finale -->
                    <div class="col-12">
                        <div class="card border-start border-warning border-3">
                            <div class="card-body py-2">
                                <h6 class="fw-semibold mb-2"><i class="fi fi-rr-chart-histogram me-1 text-warning"></i> 3. Évaluation Finale
                                    @if(in_array($campaign->status, ['evaluation_in_progress']))
                                    <span class="badge bg-warning-subtle text-warning ms-1">En cours</span>
                                    @elseif(in_array($campaign->status, ['evaluation_completed', 'archived']))
                                    <span class="badge bg-success-subtle text-success ms-1"><i class="fi fi-rr-check"></i></span>
                                    @endif
                                </h6>
                                <div class="row">
                                    <div class="col-6"><small class="text-muted d-block">Début</small><span class="fw-semibold">{{ $campaign->evaluation_starts_at ? $campaign->evaluation_starts_at->format('d/m/Y') : 'Non planifié' }}</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Fin</small><span class="fw-semibold">{{ $campaign->evaluation_stops_at ? $campaign->evaluation_stops_at->format('d/m/Y') : 'Non planifié' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Participants -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="fi fi-rr-users me-1"></i> Participants ({{ $campaign->userCampaigns->count() }})</h6>
                @if(in_array($campaign->status, ['draft', 'objective_in_progress']))
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addParticipantsModal">
                    <i class="fi fi-rr-plus me-1"></i> Ajouter des participants
                </button>
                @endif
            </div>
            <div class="card-body">
                @if($campaign->userCampaigns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Utilisateur</th>
                                <th>Supérieur évaluateur</th>
                                <th>Objectifs</th>
                                <th>Évaluation</th>
                                <th>Mi-parcours</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaign->userCampaigns as $index => $uc)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold">{{ strtoupper(substr($uc->user->first_name, 0, 1) . substr($uc->user->last_name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block">{{ $uc->user->full_name }}</span>
                                            <small class="text-muted">{{ $uc->user->position ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($uc->supervisor)
                                        <span>{{ $uc->supervisor->full_name }}</span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-{{ $uc->objective_status_color }}-subtle text-{{ $uc->objective_status_color }}">{{ $uc->objective_status_label }}</span></td>
                                <td><span class="badge bg-{{ $uc->evaluation_status_color }}-subtle text-{{ $uc->evaluation_status_color }}">{{ $uc->evaluation_status_label }}</span></td>
                                <td>
                                    @if($uc->midterm_file)
                                        <span class="badge bg-success-subtle text-success"><i class="fi fi-rr-check me-1"></i>Importé</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-outline-secondary rounded-circle waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fi fi-rr-menu-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('campaigns.participants.objectives', [$campaign->uuid, $uc->uuid]) }}">
                                                    <i class="fi fi-rr-bullseye me-2"></i> Compléter les objectifs
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('campaigns.participants.pdf-objectives', [$campaign->uuid, $uc->uuid]) }}" target="_blank">
                                                    <i class="fi fi-rr-file-pdf me-2"></i> Fiche des objectifs (PDF)
                                                </a>
                                            </li>
                                            @if(in_array($campaign->status, ['objective_in_progress', 'midterm_in_progress', 'evaluation_in_progress']))
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="skipPhase('{{ $uc->uuid }}')">
                                                    <i class="fi fi-rr-forward me-2"></i> Passer la phase active
                                                </a>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('campaigns.participants.midterm-report', [$campaign->uuid, $uc->uuid]) }}" target="_blank">
                                                    <i class="fi fi-rr-document me-2"></i> Fiche mi-parcours (Word)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);" onclick="openUploadMidterm('{{ $uc->uuid }}')">
                                                    <i class="fi fi-rr-upload me-2"></i> Importer fiche mi-parcours
                                                </a>
                                            </li>
                                            @if($uc->midterm_file)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('campaigns.participants.download-midterm', [$campaign->uuid, $uc->uuid]) }}">
                                                    <i class="fi fi-rr-download me-2"></i> Télécharger fiche mi-parcours
                                                </a>
                                            </li>
                                            @endif
                                            @if(in_array($campaign->status, ['draft', 'objective_in_progress']))
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="removeParticipant('{{ $uc->uuid }}')">
                                                    <i class="fi fi-rr-trash me-2"></i> Retirer le participant
                                                </a>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fi fi-rr-users fs-1 d-block mb-3"></i>
                    <h6>Aucun participant</h6>
                    <p class="mb-0">Ajoutez des utilisateurs à cette campagne pour commencer.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Modal Ajouter des participants -->
        @if(in_array($campaign->status, ['draft', 'objective_in_progress']))
        <div class="modal fade" id="addParticipantsModal" tabindex="-1" aria-labelledby="addParticipantsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form class="sendForm" method="POST" action="{{ route('campaigns.participants.store', $campaign->uuid) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addParticipantsModalLabel">Ajouter des participants</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            @if($availableUsers->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">Sélectionner les utilisateurs</label>
                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                                        <label class="form-check-label fw-semibold" for="selectAll">Tout sélectionner</label>
                                    </div>
                                    <hr class="my-2">
                                    @foreach($availableUsers as $availableUser)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input user-checkbox" type="checkbox" name="user_uuids[]" value="{{ $availableUser->uuid }}" id="user_{{ $availableUser->uuid }}">
                                        <label class="form-check-label" for="user_{{ $availableUser->uuid }}">
                                            {{ $availableUser->full_name }}
                                            @if($availableUser->position)
                                                <small class="text-muted">({{ $availableUser->position }})</small>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="text-center text-muted py-3">
                                <p class="mb-0">Tous les utilisateurs actifs sont déjà participants de cette campagne.</p>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                            @if($availableUsers->count() > 0)
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="fi fi-rr-check me-1"></i> Ajouter
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Modifier Campagne -->
<div class="modal fade" id="editCampaignModal" tabindex="-1" aria-labelledby="editCampaignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form class="sendForm" method="POST" action="{{ route('campaigns.update', $campaign->uuid) }}">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCampaignModalLabel">Modifier la campagne</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="editName" class="form-label">Nom de la campagne <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editName" name="name" value="{{ $campaign->name }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editYear" class="form-label">Année <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editYear" name="year" value="{{ $campaign->year }}" min="2020" max="2099" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="editDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="2">{{ $campaign->description }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-semibold mb-3"><i class="fi fi-rr-bullseye me-1"></i> Phase Objectifs</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editObjectiveStartsAt" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="editObjectiveStartsAt" name="objective_starts_at" value="{{ $campaign->objective_starts_at?->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editObjectiveStopsAt" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="editObjectiveStopsAt" name="objective_stops_at" value="{{ $campaign->objective_stops_at?->format('Y-m-d') }}">
                        </div>
                    </div>
                    <h6 class="fw-semibold mb-3"><i class="fi fi-rr-refresh me-1"></i> Phase Mi-parcours</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editMidtermStartsAt" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="editMidtermStartsAt" name="midterm_starts_at" value="{{ $campaign->midterm_starts_at?->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editMidtermStopsAt" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="editMidtermStopsAt" name="midterm_stops_at" value="{{ $campaign->midterm_stops_at?->format('Y-m-d') }}">
                        </div>
                    </div>
                    <h6 class="fw-semibold mb-3"><i class="fi fi-rr-chart-histogram me-1"></i> Phase Évaluation Finale</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editEvaluationStartsAt" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="editEvaluationStartsAt" name="evaluation_starts_at" value="{{ $campaign->evaluation_starts_at?->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editEvaluationStopsAt" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="editEvaluationStopsAt" name="evaluation_stops_at" value="{{ $campaign->evaluation_stops_at?->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-check me-1"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Import Fiche Mi-parcours -->
<div class="modal fade" id="uploadMidtermModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="uploadMidtermForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fi fi-rr-upload me-1"></i> Importer fiche mi-parcours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="uploadMidtermUcUuid" value="">
                    <div class="mb-3">
                        <label class="form-label">Fichier PDF <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="midterm_file" accept=".pdf" required>
                        <small class="text-muted">Format PDF uniquement. Max 10 Mo.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-check me-1"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleSelectAll(el) {
        document.querySelectorAll('.user-checkbox').forEach(function(cb) {
            cb.checked = el.checked;
        });
    }

    function removeParticipant(ucUuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment retirer ce participant ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, retirer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/campaigns/{{ $campaign->uuid }}/participants/' + ucUuid,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () { loader(); },
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

    function skipPhase(ucUuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment faire passer ce participant à la phase suivante ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, passer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/campaigns/{{ $campaign->uuid }}/participants/' + ucUuid + '/skip-phase',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    beforeSend: function () { loader(); },
                    success: function (data) {
                        loader('hide');
                        if (data.success) { sendSuccess(data.message, 'back'); }
                        else { SendError(data.message); }
                    },
                    error: function (data) {
                        loader('hide');
                        SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
                    }
                });
            }
        });
    }

    function openUploadMidterm(ucUuid) {
        document.getElementById('uploadMidtermUcUuid').value = ucUuid;
        new bootstrap.Modal(document.getElementById('uploadMidtermModal')).show();
    }

    document.getElementById('uploadMidtermForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let ucUuid = document.getElementById('uploadMidtermUcUuid').value;
        let formData = new FormData(this);
        loader();
        $.ajax({
            url: '/campaigns/{{ $campaign->uuid }}/participants/' + ucUuid + '/upload-midterm',
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                loader('hide');
                if (data.success) { sendSuccess(data.message, 'back'); }
                else { SendError(data.message); }
            },
            error: function (data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    });

    function updateStatus(uuid, action, label) {
        Swal.fire({
            icon: 'question',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment : ' + label + ' ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, confirmer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/campaigns/' + uuid + '/status',
                    type: 'POST',
                    data: { action: action },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () { loader(); },
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
