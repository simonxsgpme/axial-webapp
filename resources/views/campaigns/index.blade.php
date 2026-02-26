@extends('layouts.app')

@section('title', 'Campagnes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="fw-bold mb-0">Campagnes</h5>
            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#campaignModal" onclick="openCreateModal()">
                <i class="fi fi-rr-plus me-1"></i> Nouvelle campagne
            </button>
        </div>

        <div class="row g-3">
            @forelse($campaigns as $campaign)
            <div class="col-lg-4 col-md-6">
                <div class="card border shadow-sm h-100 campaign-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('assets/images/folder.svg') }}" alt="folder" width="40">
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $campaign->name }}</h6>
                                    <small class="text-muted">{{ $campaign->year }}</small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-icon btn-action-gray rounded-circle waves-effect waves-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fi fi-br-menu-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('campaigns.show', $campaign->uuid) }}">
                                            <i class="fi fi-rr-eye me-2"></i> Voir
                                        </a>
                                    </li>
                                    @if($campaign->status !== 'archived')
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="openEditModal('{{ $campaign->uuid }}', {{ json_encode($campaign->only(['name', 'description', 'year', 'objective_starts_at', 'objective_stops_at', 'midterm_starts_at', 'midterm_stops_at', 'evaluation_starts_at', 'evaluation_stops_at'])) }})">
                                            <i class="fi fi-rr-pencil me-2"></i> Modifier
                                        </a>
                                    </li>
                                    @endif
                                    @if($campaign->next_action)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-{{ $campaign->next_action['color'] }}" href="javascript:void(0);" onclick="updateStatus('{{ $campaign->uuid }}', '{{ $campaign->next_action['action'] }}', '{{ $campaign->next_action['label'] }}')">
                                            <i class="fi {{ $campaign->next_action['icon'] }} me-2"></i> {{ $campaign->next_action['label'] }}
                                        </a>
                                    </li>
                                    @endif
                                    @if(in_array($campaign->status, ['draft', 'archived']))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteCampaign('{{ $campaign->uuid }}')">
                                            <i class="fi fi-rr-trash me-2"></i> Supprimer
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        @if($campaign->description)
                        <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $campaign->description }}</p>
                        @endif
                        <div class="mb-2">
                            <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }}">{{ $campaign->status_label }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fi fi-rr-calendar me-1"></i>
                                @if($campaign->objective_starts_at)
                                    {{ $campaign->objective_starts_at->format('d/m/Y') }}
                                @else
                                    Non planifié
                                @endif
                            </small>
                            <a href="{{ route('campaigns.show', $campaign->uuid) }}" class="btn btn-sm btn-outline-primary waves-effect">
                                Ouvrir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-5">
                        <img src="{{ asset('assets/images/folder.svg') }}" alt="folder" width="64" class="mb-3 opacity-50">
                        <h6>Aucune campagne</h6>
                        <p class="mb-0">Créez votre première campagne d'évaluation pour commencer.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Créer / Modifier Campagne -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-labelledby="campaignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="campaignForm" class="sendForm" method="POST" action="{{ route('campaigns.store') }}">
                @csrf
                <input type="hidden" name="_method" id="campaignMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="campaignModalLabel">Nouvelle campagne</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="campaignName" class="form-label">Nom de la campagne <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="campaignName" name="name" placeholder="Ex: Campagne d'évaluation 2025" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="campaignYear" class="form-label">Année <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="campaignYear" name="year" placeholder="2025" min="2020" max="2099" value="{{ date('Y') }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="campaignDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="campaignDescription" name="description" rows="2" placeholder="Description de la campagne..."></textarea>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-semibold mb-3"><i class="fi fi-rr-bullseye me-1"></i> Phase Objectifs</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="objectiveStartsAt" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="objectiveStartsAt" name="objective_starts_at">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="objectiveStopsAt" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="objectiveStopsAt" name="objective_stops_at">
                        </div>
                    </div>
                    <h6 class="fw-semibold mb-3"><i class="fi fi-rr-time-half-past me-1"></i> Phase Mi-parcours</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="midtermStartsAt" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="midtermStartsAt" name="midterm_starts_at">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="midtermStopsAt" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="midtermStopsAt" name="midterm_stops_at">
                        </div>
                    </div>
                    <h6 class="fw-semibold mb-3"><i class="fi fi-rr-chart-histogram me-1"></i> Phase Évaluation</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="evaluationStartsAt" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="evaluationStartsAt" name="evaluation_starts_at">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="evaluationStopsAt" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="evaluationStopsAt" name="evaluation_stops_at">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-check me-1"></i> <span id="campaignSubmitText">Enregistrer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('campaignModalLabel').textContent = 'Nouvelle campagne';
        document.getElementById('campaignSubmitText').textContent = 'Enregistrer';
        document.getElementById('campaignForm').action = "{{ route('campaigns.store') }}";
        document.getElementById('campaignMethod').value = 'POST';
        document.getElementById('campaignName').value = '';
        document.getElementById('campaignYear').value = '{{ date("Y") }}';
        document.getElementById('campaignDescription').value = '';
        document.getElementById('objectiveStartsAt').value = '';
        document.getElementById('objectiveStopsAt').value = '';
        document.getElementById('midtermStartsAt').value = '';
        document.getElementById('midtermStopsAt').value = '';
        document.getElementById('evaluationStartsAt').value = '';
        document.getElementById('evaluationStopsAt').value = '';
    }

    function openEditModal(uuid, data) {
        document.getElementById('campaignModalLabel').textContent = 'Modifier la campagne';
        document.getElementById('campaignSubmitText').textContent = 'Mettre à jour';
        document.getElementById('campaignForm').action = '/campaigns/' + uuid;
        document.getElementById('campaignMethod').value = 'PUT';
        document.getElementById('campaignName').value = data.name || '';
        document.getElementById('campaignYear').value = data.year || '';
        document.getElementById('campaignDescription').value = data.description || '';
        document.getElementById('objectiveStartsAt').value = data.objective_starts_at ? data.objective_starts_at.substring(0, 10) : '';
        document.getElementById('objectiveStopsAt').value = data.objective_stops_at ? data.objective_stops_at.substring(0, 10) : '';
        document.getElementById('midtermStartsAt').value = data.midterm_starts_at ? data.midterm_starts_at.substring(0, 10) : '';
        document.getElementById('midtermStopsAt').value = data.midterm_stops_at ? data.midterm_stops_at.substring(0, 10) : '';
        document.getElementById('evaluationStartsAt').value = data.evaluation_starts_at ? data.evaluation_starts_at.substring(0, 10) : '';
        document.getElementById('evaluationStopsAt').value = data.evaluation_stops_at ? data.evaluation_stops_at.substring(0, 10) : '';

        var modal = new bootstrap.Modal(document.getElementById('campaignModal'));
        modal.show();
    }

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

    function deleteCampaign(uuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer cette campagne ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, supprimer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/campaigns/' + uuid,
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
</script>
@endpush
