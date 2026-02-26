@extends('layouts.app')

@section('title', 'Validation Objectifs')

@section('styles')
<style>
    .subordinate-card { cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
    .subordinate-card:hover { border-left-color: var(--bs-primary); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .subordinate-card.active { border-left-color: var(--bs-primary); background-color: rgba(var(--bs-primary-rgb), 0.03); }
    .obj-review-card { border: 1px solid var(--bs-border-color); border-radius: var(--bs-border-radius); padding: 15px; margin-bottom: 12px; transition: all 0.2s; }
    .obj-review-card.status-validated { border-left: 3px solid var(--bs-success); }
    .obj-review-card.status-rejected { border-left: 3px solid var(--bs-danger); }
    .obj-review-card.status-pending { border-left: 3px solid var(--bs-secondary); }
    .decision-timeline { position: relative; padding-left: 28px; }
    .decision-timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background-color: var(--bs-border-color); }
    .decision-timeline-item { position: relative; padding-bottom: 16px; }
    .decision-timeline-item:last-child { padding-bottom: 0; }
    .decision-timeline-item .timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px var(--bs-border-color); }
    .decision-timeline-item .timeline-dot.bg-primary { box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-success { box-shadow: 0 0 0 2px rgba(var(--bs-success-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-danger { box-shadow: 0 0 0 2px rgba(var(--bs-danger-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-warning { box-shadow: 0 0 0 2px rgba(var(--bs-warning-rgb), 0.3); }
    .comment-item { padding: 8px 0; border-bottom: 1px solid var(--bs-border-color); }
    .comment-item:last-child { border-bottom: 0; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="fw-bold fs-5">Validation des Objectifs</span>
                @if($campaign)
                    <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }} ms-2">{{ $campaign->status_label }}</span>
                    <small class="text-muted ms-2">{{ $campaign->name }} ({{ $campaign->year }})</small>
                @endif
            </div>
        </div>

        @if(!$campaign)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-folder fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Aucune campagne lancée pour le moment</h5>
                </div>
            </div>
        @elseif($subordinates->count() === 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-users fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Aucun collaborateur à superviser</h5>
                    <p class="text-muted mb-0">Vous n'avez aucun collaborateur assigné dans cette campagne.</p>
                </div>
            </div>
        @else
            <div class="row g-3">
                {{-- Liste des subordonnés --}}
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fi fi-rr-users me-1"></i> Collaborateurs ({{ $subordinates->count() }})</h6>
                        </div>
                        <div class="card-body p-2">
                            @foreach($subordinates as $uc)
                            <div class="subordinate-card card mb-2 p-3" data-uuid="{{ $uc->uuid }}" onclick="loadSubordinate('{{ $uc->uuid }}')">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold">{{ strtoupper(substr($uc->user->first_name, 0, 1) . substr($uc->user->last_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block" style="font-size: 13px;">{{ $uc->user->full_name }}</span>
                                        <small class="text-muted">{{ $uc->user->position ?? '' }}</small>
                                    </div>
                                    <span class="badge bg-{{ $uc->objective_status_color }}-subtle text-{{ $uc->objective_status_color }}">{{ $uc->objective_status_label }}</span>
                                </div>
                                @php
                                    $ucTotal = $uc->objectives->count();
                                    $ucValidated = $uc->objectives->where('status', 'validated')->count();
                                @endphp
                                @if($ucTotal > 0)
                                <div class="mt-2">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: {{ $ucTotal > 0 ? ($ucValidated / $ucTotal * 100) : 0 }}%"></div>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">{{ $ucValidated }}/{{ $ucTotal }} validés</small>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Zone de détail --}}
                <div class="col-md-8">
                    <div id="emptyDetail">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fi fi-rr-cursor-finger fs-1 d-block mb-3 text-muted"></i>
                                <h6 class="text-muted">Sélectionnez un collaborateur</h6>
                                <p class="text-muted mb-0">Cliquez sur un collaborateur pour voir et valider ses objectifs.</p>
                            </div>
                        </div>
                    </div>

                    <div id="subordinateDetail" style="display: none;">
                        {{-- Header collaborateur --}}
                        <div class="card mb-3">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md">
                                            <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold" id="subInitials"></span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold" id="subName"></h6>
                                            <small class="text-muted" id="subPosition"></small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge" id="subStatus"></span>
                                        <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" id="btnReturn" style="display:none;" onclick="returnObjectives()">
                                            <i class="fi fi-rr-undo me-1"></i> Retourner les objectifs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Onglets --}}
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tabObjectives" role="tab">
                                    <i class="fi fi-rr-bullseye me-1"></i> Objectifs <span class="badge bg-primary ms-1" id="tabObjCount">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabTimeline" role="tab">
                                    <i class="fi fi-rr-time-past me-1"></i> Historique
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            {{-- Tab Objectifs --}}
                            <div class="tab-pane fade show active" id="tabObjectives" role="tabpanel">
                                <div id="objectivesReviewList"></div>
                            </div>

                            {{-- Tab Timeline --}}
                            <div class="tab-pane fade" id="tabTimeline" role="tabpanel">
                                <div class="card">
                                    <div class="card-body" id="timelineContent">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal commentaire objectif --}}
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="commentModalTitle">Commentaires</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalCommentsList"></div>
                <hr>
                <form id="modalCommentForm" onsubmit="addModalComment(event)">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" id="modalCommentInput" placeholder="Ajouter un commentaire..." required>
                        <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">
                            <i class="fi fi-rr-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentUcUuid = null;
    let currentUcData = null;
    let categoriesMap = @json($categories->keyBy('uuid') ?? []);

    function loadSubordinate(ucUuid) {
        currentUcUuid = ucUuid;
        document.querySelectorAll('.subordinate-card').forEach(c => c.classList.remove('active'));
        let card = document.querySelector('.subordinate-card[data-uuid="' + ucUuid + '"]');
        if (card) card.classList.add('active');

        loader();
        $.ajax({
            url: '/supervisor/objectives/' + ucUuid,
            type: 'GET',
            success: function(data) {
                loader('hide');
                if (data.success) {
                    currentUcData = data.userCampaign;
                    renderSubordinateDetail(data.userCampaign);
                }
            },
            error: function(data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    }

    function renderSubordinateDetail(uc) {
        document.getElementById('emptyDetail').style.display = 'none';
        document.getElementById('subordinateDetail').style.display = 'block';

        let user = uc.user;
        let initials = (user.first_name ? user.first_name[0] : '') + (user.last_name ? user.last_name[0] : '');
        document.getElementById('subInitials').textContent = initials.toUpperCase();
        document.getElementById('subName').textContent = user.full_name;
        document.getElementById('subPosition').textContent = user.position || '';

        let statusColors = { draft: 'secondary', submitted: 'warning', returned: 'info', completed: 'success' };
        let statusLabels = { draft: 'Brouillon', submitted: 'Soumis', returned: 'Retourné', completed: 'Terminé' };
        let statusEl = document.getElementById('subStatus');
        statusEl.className = 'badge bg-' + (statusColors[uc.objective_status] || 'secondary') + '-subtle text-' + (statusColors[uc.objective_status] || 'secondary');
        statusEl.textContent = statusLabels[uc.objective_status] || uc.objective_status;

        // Bouton retourner visible uniquement si soumis
        document.getElementById('btnReturn').style.display = uc.objective_status === 'submitted' ? '' : 'none';

        document.getElementById('tabObjCount').textContent = uc.objectives ? uc.objectives.length : 0;

        renderObjectivesReview(uc.objectives || [], uc.objective_status);
        renderTimeline(uc.decisions || []);
    }

    function renderObjectivesReview(objectives, ucStatus) {
        let html = '';
        if (objectives.length === 0) {
            html = '<div class="card"><div class="card-body text-center py-4 text-muted">Aucun objectif défini.</div></div>';
        } else {
            let canReview = ucStatus === 'submitted';
            objectives.forEach(obj => {
                let statusColors = { pending: 'secondary', validated: 'success', rejected: 'danger' };
                let statusLabels = { pending: 'En attente', validated: 'Validé', rejected: 'Refusé' };
                let catName = obj.category ? obj.category.name : '';
                let catPercentage = obj.category ? obj.category.percentage : 0;

                let actionsHtml = '';
                // Calculer le restant disponible pour la catégorie
                let catUuid = obj.objective_category_uuid;
                let catMax = catPercentage;
                let catUsed = 0;
                objectives.forEach(o => {
                    if (o.objective_category_uuid === catUuid && o.status === 'validated' && o.uuid !== obj.uuid) {
                        catUsed += (o.weight || 0);
                    }
                });
                let catRemaining = catMax - catUsed;

                if (canReview && obj.status === 'pending') {
                    actionsHtml = `
                        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                <span class="input-group-text">Pondération</span>
                                <input type="number" class="form-control" id="weight-${obj.uuid}" min="0" max="${catRemaining}" value="${obj.weight || 0}" placeholder="%">
                                <span class="input-group-text">/ ${catRemaining}% dispo.</span>
                            </div>
                            <button type="button" class="btn btn-success btn-sm waves-effect waves-light" onclick="validateObj('${obj.uuid}', ${catRemaining})">
                                <i class="fi fi-rr-check me-1"></i> Valider
                            </button>
                            <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="rejectObj('${obj.uuid}')">
                                <i class="fi fi-rr-cross me-1"></i> Refuser
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="openComments('${obj.uuid}', '${obj.title.replace(/'/g, "\\'")}')">
                                <i class="fi fi-rr-comment-alt me-1"></i> ${obj.comments ? obj.comments.length : 0}
                            </button>
                        </div>`;
                } else {
                    actionsHtml = `
                        <div class="d-flex gap-2 mt-2">
                            ${obj.weight > 0 ? `<small class="text-muted">Pondération: ${obj.weight}%</small>` : ''}
                            <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="openComments('${obj.uuid}', '${obj.title.replace(/'/g, "\\'")}')">
                                <i class="fi fi-rr-comment-alt me-1"></i> ${obj.comments ? obj.comments.length : 0}
                            </button>
                        </div>`;
                }

                html += `
                    <div class="obj-review-card status-${obj.status}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="mb-0 fw-semibold">${obj.title}</h6>
                                    <span class="badge bg-${statusColors[obj.status] || 'secondary'}-subtle text-${statusColors[obj.status] || 'secondary'}">${statusLabels[obj.status] || obj.status}</span>
                                </div>
                                <small class="text-muted">${catName} (${catPercentage}%)</small>
                                ${obj.description ? `<p class="mb-0 mt-1" style="font-size: 13px;">${obj.description}</p>` : ''}
                                ${obj.rejection_reason ? `<div class="alert alert-danger py-1 px-2 mt-2 mb-0" style="font-size: 12px;"><i class="fi fi-rr-cross-circle me-1"></i>${obj.rejection_reason}</div>` : ''}
                            </div>
                        </div>
                        ${actionsHtml}
                    </div>`;
            });
        }
        document.getElementById('objectivesReviewList').innerHTML = html;
    }

    function renderTimeline(decisions) {
        if (!decisions || decisions.length === 0) {
            document.getElementById('timelineContent').innerHTML = '<p class="text-muted text-center py-3">Aucun historique.</p>';
            return;
        }
        let html = '<div class="decision-timeline">';
        decisions.forEach(d => {
            let colors = { submitted: 'primary', returned: 'warning', completed: 'success' };
            let labels = { submitted: 'Objectifs soumis', returned: 'Objectifs retournés', completed: 'Objectifs validés' };
            html += `
                <div class="decision-timeline-item">
                    <div class="timeline-dot bg-${colors[d.action] || 'secondary'}"></div>
                    <div>
                        <span class="fw-semibold d-block" style="font-size: 13px;">${labels[d.action] || d.action}</span>
                        <small class="text-muted d-block">${d.actor ? d.actor.full_name : ''}</small>
                        ${d.comment ? `<small class="d-block mt-1" style="font-size: 12px;">${d.comment}</small>` : ''}
                        <small class="text-muted" style="font-size: 11px;">${new Date(d.created_at).toLocaleDateString('fr-FR', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'})}</small>
                    </div>
                </div>`;
        });
        html += '</div>';
        document.getElementById('timelineContent').innerHTML = html;
    }

    function validateObj(objUuid, catRemaining) {
        let weight = document.getElementById('weight-' + objUuid).value;
        if (!weight || weight < 0 || weight > 100) {
            SendError('Veuillez saisir une pondération valide (0-100).');
            return;
        }
        if (parseInt(weight) > catRemaining) {
            SendError('La pondération dépasse le restant disponible pour cette catégorie (' + catRemaining + '%).');
            return;
        }

        Swal.fire({
            icon: 'question', title: 'Valider cet objectif ?',
            text: 'Pondération : ' + weight + '%',
            input: 'text', inputLabel: 'Commentaire (optionnel)', inputPlaceholder: 'Ajouter un commentaire...',
            showDenyButton: true, confirmButtonText: 'Valider', denyButtonText: 'Annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/supervisor/objectives/' + objUuid + '/validate', type: 'POST',
                    data: { weight: weight, comment: result.value || '' },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Succès', text: data.message, showConfirmButton: false, timer: 1500 });
                            if (data.all_validated) {
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                loadSubordinate(currentUcUuid);
                            }
                        } else { SendError(data.message); }
                    },
                    error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }

    function rejectObj(objUuid) {
        Swal.fire({
            icon: 'warning', title: 'Refuser cet objectif ?',
            input: 'textarea', inputLabel: 'Motif du refus (obligatoire)', inputPlaceholder: 'Expliquez pourquoi cet objectif est refusé...',
            inputValidator: (value) => { if (!value) return 'Le motif du refus est obligatoire.'; },
            showDenyButton: true, confirmButtonText: 'Refuser', denyButtonText: 'Annuler',
            confirmButtonColor: '#dc3545',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/supervisor/objectives/' + objUuid + '/reject', type: 'POST',
                    data: { rejection_reason: result.value },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Objectif refusé', text: data.message, showConfirmButton: false, timer: 1500 });
                            loadSubordinate(currentUcUuid);
                        } else { SendError(data.message); }
                    },
                    error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }

    function returnObjectives() {
        Swal.fire({
            icon: 'warning', title: 'Retourner les objectifs ?',
            text: 'Les objectifs sans décision seront automatiquement refusés.',
            input: 'textarea', inputLabel: 'Commentaire (optionnel)', inputPlaceholder: 'Ajouter un commentaire...',
            showDenyButton: true, confirmButtonText: 'Retourner', denyButtonText: 'Annuler',
            confirmButtonColor: '#ffc107',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/supervisor/objectives/' + currentUcUuid + '/return', type: 'POST',
                    data: { comment: result.value || '' },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Succès', text: data.message, showConfirmButton: false, timer: 1500 });
                            setTimeout(() => location.reload(), 1500);
                        } else { SendError(data.message); }
                    },
                    error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }

    // Commentaires modal
    let currentModalObjUuid = null;

    function openComments(objUuid, objTitle) {
        currentModalObjUuid = objUuid;
        document.getElementById('commentModalTitle').textContent = 'Commentaires - ' + objTitle;
        loadModalComments(objUuid);
        new bootstrap.Modal(document.getElementById('commentModal')).show();
    }

    function loadModalComments(objUuid) {
        $.ajax({
            url: '/objectives/' + objUuid, type: 'GET',
            success: function(data) {
                if (data.success) {
                    let comments = data.objective.comments || [];
                    let authUuid = '{{ Auth::id() }}';
                    if (comments.length === 0) {
                        document.getElementById('modalCommentsList').innerHTML = '<p class="text-muted text-center py-2">Aucun commentaire.</p>';
                        return;
                    }
                    let html = '';
                    comments.forEach(c => {
                        let initials = (c.user.first_name ? c.user.first_name[0] : '') + (c.user.last_name ? c.user.last_name[0] : '');
                        let deleteBtn = c.user_uuid === authUuid ? `<button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" onclick="deleteModalComment('${c.uuid}')"><i class="fi fi-rr-trash" style="font-size:11px"></i></button>` : '';
                        html += `
                            <div class="comment-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-2 align-items-start">
                                        <div class="avatar avatar-xs">
                                            <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold" style="font-size:11px">${initials.toUpperCase()}</span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block" style="font-size:13px">${c.user.full_name}</span>
                                            <p class="mb-0" style="font-size:13px">${c.content}</p>
                                            <small class="text-muted">${new Date(c.created_at).toLocaleDateString('fr-FR', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'})}</small>
                                        </div>
                                    </div>
                                    ${deleteBtn}
                                </div>
                            </div>`;
                    });
                    document.getElementById('modalCommentsList').innerHTML = html;
                }
            }
        });
    }

    function addModalComment(e) {
        e.preventDefault();
        let content = document.getElementById('modalCommentInput').value;
        if (!content || !currentModalObjUuid) return;
        $.ajax({
            url: '/objectives/' + currentModalObjUuid + '/comments', type: 'POST',
            data: { content: content },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                if (data.success) {
                    document.getElementById('modalCommentInput').value = '';
                    loadModalComments(currentModalObjUuid);
                } else { SendError(data.message); }
            },
            error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }

    function deleteModalComment(commentUuid) {
        $.ajax({
            url: '/objective-comments/' + commentUuid, type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                if (data.success) { loadModalComments(currentModalObjUuid); }
                else { SendError(data.message); }
            },
            error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }
</script>
@endpush
