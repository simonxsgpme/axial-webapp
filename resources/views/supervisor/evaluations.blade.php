@extends('layouts.app')

@section('title', 'Évaluer Collaborateurs')

@section('styles')
<style>
    .subordinate-card { cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
    .subordinate-card:hover { border-left-color: var(--bs-primary); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .subordinate-card.active { border-left-color: var(--bs-primary); background-color: rgba(var(--bs-primary-rgb), 0.03); }
    .obj-eval-card { border: 1px solid var(--bs-border-color); border-radius: var(--bs-border-radius); padding: 15px; margin-bottom: 12px; transition: all 0.2s; }
    .obj-eval-card.scored { border-left: 3px solid var(--bs-success); }
    .obj-eval-card.unscored { border-left: 3px solid var(--bs-secondary); }
    .decision-timeline { position: relative; padding-left: 28px; }
    .decision-timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background-color: var(--bs-border-color); }
    .decision-timeline-item { position: relative; padding-bottom: 16px; }
    .decision-timeline-item:last-child { padding-bottom: 0; }
    .decision-timeline-item .timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px var(--bs-border-color); }
    .decision-timeline-item .timeline-dot.bg-primary { box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-success { box-shadow: 0 0 0 2px rgba(var(--bs-success-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-warning { box-shadow: 0 0 0 2px rgba(var(--bs-warning-rgb), 0.3); }
    .comment-item { padding: 8px 0; border-bottom: 1px solid var(--bs-border-color); }
    .comment-item:last-child { border-bottom: 0; }
    .score-display { font-size: 1.8rem; font-weight: 700; }
    .rating-gauge { height: 8px; border-radius: 4px; background-color: var(--bs-border-color); overflow: hidden; }
    .rating-gauge .gauge-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="fw-bold fs-5">Évaluer les Collaborateurs</span>
                @if($campaign)
                    <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }} ms-2">{{ $campaign->status_label }}</span>
                    <small class="text-muted ms-2">{{ $campaign->name }} ({{ $campaign->year }})</small>
                @endif
            </div>
        </div>

        @if(!$campaign)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-chart-histogram fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Aucune campagne en phase d'évaluation</h5>
                    <p class="text-muted mb-0">La phase d'évaluation n'a pas encore été démarrée.</p>
                </div>
            </div>
        @elseif($subordinates->count() === 0)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-users fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Aucun collaborateur à évaluer</h5>
                    <p class="text-muted mb-0">Vous n'avez aucun collaborateur avec des objectifs validés dans cette campagne.</p>
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
                                    <span class="badge bg-{{ $uc->evaluation_status_color }}-subtle text-{{ $uc->evaluation_status_color }}">{{ $uc->evaluation_status_label }}</span>
                                </div>
                                @if($uc->rating !== null)
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted" style="font-size: 11px;">Note globale</small>
                                        <small class="fw-bold text-{{ $uc->rating_color }}" style="font-size: 11px;">{{ number_format($uc->rating, 1) }}%</small>
                                    </div>
                                    <div class="rating-gauge">
                                        <div class="gauge-fill bg-{{ $uc->rating_color }}" style="width: {{ $uc->rating }}%"></div>
                                    </div>
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
                                <p class="text-muted mb-0">Cliquez sur un collaborateur pour évaluer ses objectifs.</p>
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
                                        <span class="badge" id="subEvalStatus"></span>
                                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btnSubmitEval" style="display:none;" onclick="submitEvaluation()">
                                            <i class="fi fi-rr-paper-plane me-1"></i> Soumettre à l'employé
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Note globale --}}
                        <div class="card mb-3" id="ratingCard" style="display:none;">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="text-center">
                                        <div class="score-display" id="globalRating">-</div>
                                        <small class="text-muted">Note globale</small>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-semibold" id="ratingLevel">-</span>
                                            <span class="fw-bold" id="ratingPercent">-</span>
                                        </div>
                                        <div class="rating-gauge" style="height: 12px;">
                                            <div class="gauge-fill" id="ratingGauge" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Onglets --}}
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tabEvalObjectives" role="tab">
                                    <i class="fi fi-rr-bullseye me-1"></i> Objectifs <span class="badge bg-primary ms-1" id="tabEvalObjCount">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabEvalTimeline" role="tab">
                                    <i class="fi fi-rr-time-past me-1"></i> Historique
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tabEvalObjectives" role="tabpanel">
                                <div id="evalObjectivesList"></div>
                            </div>
                            <div class="tab-pane fade" id="tabEvalTimeline" role="tabpanel">
                                <div class="card">
                                    <div class="card-body" id="evalTimelineContent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal commentaires évaluation --}}
<div class="modal fade" id="evalCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="evalCommentModalTitle">Commentaires d'évaluation</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="evalModalCommentsList"></div>
                <hr>
                <form id="evalModalCommentForm" onsubmit="addEvalComment(event)">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" id="evalModalCommentInput" placeholder="Ajouter un commentaire d'évaluation..." required>
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

    function loadSubordinate(ucUuid) {
        currentUcUuid = ucUuid;
        document.querySelectorAll('.subordinate-card').forEach(c => c.classList.remove('active'));
        let card = document.querySelector('.subordinate-card[data-uuid="' + ucUuid + '"]');
        if (card) card.classList.add('active');

        loader();
        $.ajax({
            url: '/supervisor/evaluations/' + ucUuid,
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

        let statusColors = { pending: 'secondary', supervisor_draft: 'primary', submitted_to_employee: 'warning', returned_to_supervisor: 'info', validated: 'success' };
        let statusLabels = { pending: 'En attente', supervisor_draft: 'En cours', submitted_to_employee: 'Soumis', returned_to_supervisor: 'Retourné', validated: 'Validé' };
        let statusEl = document.getElementById('subEvalStatus');
        statusEl.className = 'badge bg-' + (statusColors[uc.evaluation_status] || 'secondary') + '-subtle text-' + (statusColors[uc.evaluation_status] || 'secondary');
        statusEl.textContent = statusLabels[uc.evaluation_status] || uc.evaluation_status;

        // Bouton soumettre visible si supervisor_draft ou returned_to_supervisor
        let canSubmit = ['supervisor_draft', 'returned_to_supervisor'].includes(uc.evaluation_status);
        document.getElementById('btnSubmitEval').style.display = canSubmit ? '' : 'none';

        document.getElementById('tabEvalObjCount').textContent = uc.objectives ? uc.objectives.length : 0;

        updateRatingDisplay(uc.rating);
        renderEvalObjectives(uc.objectives || [], uc.evaluation_status);
        renderEvalTimeline(uc.evaluation_decisions || []);
    }

    function updateRatingDisplay(rating) {
        let card = document.getElementById('ratingCard');
        if (rating === null || rating === undefined) {
            card.style.display = 'none';
            return;
        }
        card.style.display = '';
        let ratingVal = parseFloat(rating);
        document.getElementById('globalRating').textContent = ratingVal.toFixed(1) + '/100';
        document.getElementById('ratingPercent').textContent = ratingVal.toFixed(1) + '/100';

        let level, color;
        if (ratingVal < 20) { level = 'Insuffisant'; color = 'danger'; }
        else if (ratingVal < 40) { level = 'Passable'; color = 'warning'; }
        else if (ratingVal < 60) { level = 'Satisfaisant'; color = 'info'; }
        else if (ratingVal < 80) { level = 'Bien'; color = 'primary'; }
        else { level = 'Excellent'; color = 'success'; }

        document.getElementById('ratingLevel').textContent = level;
        document.getElementById('ratingLevel').className = 'fw-semibold text-' + color;
        let gauge = document.getElementById('ratingGauge');
        gauge.style.width = ratingVal + '%';
        gauge.className = 'gauge-fill bg-' + color;
        document.getElementById('globalRating').className = 'score-display text-' + color;
    }

    function renderEvalObjectives(objectives, evalStatus) {
        let html = '';
        if (objectives.length === 0) {
            html = '<div class="card"><div class="card-body text-center py-4 text-muted">Aucun objectif.</div></div>';
        } else {
            let canScore = ['pending', 'supervisor_draft', 'returned_to_supervisor'].includes(evalStatus);
            objectives.forEach(obj => {
                let hasScore = obj.score !== null && obj.score !== undefined;
                let scoreClass = hasScore ? 'scored' : 'unscored';
                let commentsCount = obj.evaluation_comments ? obj.evaluation_comments.length : 0;
                let noteObtenue = hasScore ? obj.score : '-';

                let scoreHtml = '';
                if (canScore) {
                    scoreHtml = `
                        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                            <div class="input-group input-group-sm" style="max-width: 320px;">
                                <span class="input-group-text">Note obtenue</span>
                                <input type="number" class="form-control" id="score-${obj.uuid}" min="0" max="${obj.weight}" step="0.5" value="${hasScore ? obj.score : ''}" placeholder="0-${obj.weight}">
                                <span class="input-group-text">/ ${obj.weight}</span>
                                <button type="button" class="btn btn-primary waves-effect" onclick="saveScore('${obj.uuid}')">
                                    <i class="fi fi-rr-check"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="openEvalComments('${obj.uuid}', '${obj.title.replace(/'/g, "\\'")}')">
                                <i class="fi fi-rr-comment-alt me-1"></i> ${commentsCount}
                            </button>
                        </div>`;
                } else {
                    scoreHtml = `
                        <div class="d-flex gap-2 mt-2 align-items-center">
                            ${hasScore ? `<span class="badge bg-primary-subtle text-primary">Note: ${obj.score} / ${obj.weight}</span>` : '<span class="badge bg-secondary-subtle text-secondary">Non noté</span>'}
                            <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="openEvalComments('${obj.uuid}', '${obj.title.replace(/'/g, "\\'")}')">
                                <i class="fi fi-rr-comment-alt me-1"></i> ${commentsCount}
                            </button>
                        </div>`;
                }

                html += `
                    <div class="obj-eval-card ${scoreClass}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold">${obj.title}</h6>
                                ${obj.description ? `<p class="mb-0" style="font-size: 13px; color: var(--bs-secondary-color);">${obj.description}</p>` : ''}
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 11px;">Note obtenue</small>
                                <span class="fw-bold">${noteObtenue}%</span>
                            </div>
                        </div>
                        ${scoreHtml}
                    </div>`;
            });
        }
        document.getElementById('evalObjectivesList').innerHTML = html;
    }

    function renderEvalTimeline(decisions) {
        if (!decisions || decisions.length === 0) {
            document.getElementById('evalTimelineContent').innerHTML = '<p class="text-muted text-center py-3">Aucun historique.</p>';
            return;
        }
        let html = '<div class="decision-timeline">';
        decisions.forEach(d => {
            let colors = { submitted_to_employee: 'primary', returned_to_supervisor: 'warning', validated: 'success' };
            let labels = { submitted_to_employee: 'Soumis à l\'employé', returned_to_supervisor: 'Retourné au supérieur', validated: 'Évaluation validée' };
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
        document.getElementById('evalTimelineContent').innerHTML = html;
    }

    function saveScore(objUuid) {
        let scoreInput = document.getElementById('score-' + objUuid);
        let score = scoreInput.value;
        if (score === '' || score < 0 || score > 100) {
            SendError('Veuillez saisir une note valide (0-100).');
            return;
        }

        loader();
        $.ajax({
            url: '/supervisor/evaluations/' + objUuid + '/score',
            type: 'POST',
            data: { score: score },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                loader('hide');
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Note enregistrée', showConfirmButton: false, timer: 1000 });
                    updateRatingDisplay(data.rating);
                    loadSubordinate(currentUcUuid);
                } else { SendError(data.message); }
            },
            error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }

    function submitEvaluation() {
        Swal.fire({
            icon: 'question', title: 'Soumettre l\'évaluation ?',
            text: 'L\'employé pourra consulter ses notes et ajouter des commentaires.',
            input: 'textarea', inputLabel: 'Commentaire (optionnel)', inputPlaceholder: 'Ajouter un commentaire...',
            showDenyButton: true, confirmButtonText: 'Soumettre', denyButtonText: 'Annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/supervisor/evaluations/' + currentUcUuid + '/submit',
                    type: 'POST',
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

    // Commentaires évaluation modal
    let currentEvalObjUuid = null;

    function openEvalComments(objUuid, objTitle) {
        currentEvalObjUuid = objUuid;
        document.getElementById('evalCommentModalTitle').textContent = 'Commentaires - ' + objTitle;
        loadEvalComments(objUuid);
        new bootstrap.Modal(document.getElementById('evalCommentModal')).show();
    }

    function loadEvalComments(objUuid) {
        let obj = null;
        if (currentUcData && currentUcData.objectives) {
            obj = currentUcData.objectives.find(o => o.uuid === objUuid);
        }
        let comments = obj ? (obj.evaluation_comments || []) : [];
        renderEvalCommentsList(comments);
    }

    function renderEvalCommentsList(comments) {
        let authUuid = '{{ Auth::id() }}';
        if (comments.length === 0) {
            document.getElementById('evalModalCommentsList').innerHTML = '<p class="text-muted text-center py-2">Aucun commentaire.</p>';
            return;
        }
        let html = '';
        comments.forEach(c => {
            let initials = (c.user.first_name ? c.user.first_name[0] : '') + (c.user.last_name ? c.user.last_name[0] : '');
            let deleteBtn = c.user_uuid === authUuid ? `<button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" onclick="deleteEvalComment('${c.uuid}')"><i class="fi fi-rr-trash" style="font-size:11px"></i></button>` : '';
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
        document.getElementById('evalModalCommentsList').innerHTML = html;
    }

    function addEvalComment(e) {
        e.preventDefault();
        let content = document.getElementById('evalModalCommentInput').value;
        if (!content || !currentEvalObjUuid) return;
        $.ajax({
            url: '/supervisor/evaluations/' + currentEvalObjUuid + '/comments',
            type: 'POST',
            data: { content: content },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                if (data.success) {
                    document.getElementById('evalModalCommentInput').value = '';
                    // Reload to get fresh comments
                    loadSubordinate(currentUcUuid);
                    // Also update the modal
                    setTimeout(() => loadEvalComments(currentEvalObjUuid), 500);
                } else { SendError(data.message); }
            },
            error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }

    function deleteEvalComment(commentUuid) {
        $.ajax({
            url: '/supervisor/evaluation-comments/' + commentUuid,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                if (data.success) {
                    loadSubordinate(currentUcUuid);
                    setTimeout(() => loadEvalComments(currentEvalObjUuid), 500);
                } else { SendError(data.message); }
            },
            error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }
</script>
@endpush
