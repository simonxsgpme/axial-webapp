@extends('layouts.app')

@section('title', 'Mes Objectifs')

@section('styles')
<style>
    .objective-sidebar { min-height: calc(100vh - 250px); }
    .category-item { cursor: pointer; padding: 10px 15px; border-radius: var(--bs-border-radius); transition: all 0.2s; display: flex; align-items: center; justify-content: space-between; }
    .category-item:hover, .category-item.active { background-color: rgba(var(--bs-primary-rgb), 0.08); color: var(--bs-primary); }
    .category-item.active { font-weight: 600; }
    .category-item .badge { min-width: 24px; }
    .objective-card { cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
    .objective-card:hover { border-left-color: var(--bs-primary); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .objective-card.active { border-left-color: var(--bs-primary); background-color: rgba(var(--bs-primary-rgb), 0.03); }
    .detail-panel { display: none; }
    .detail-panel.show { display: block; }
    .comment-item { padding: 10px 0; border-bottom: 1px solid var(--bs-border-color); }
    .comment-item:last-child { border-bottom: 0; }
    .decision-timeline { position: relative; padding-left: 28px; }
    .decision-timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background-color: var(--bs-border-color); }
    .decision-timeline-item { position: relative; padding-bottom: 20px; }
    .decision-timeline-item:last-child { padding-bottom: 0; }
    .decision-timeline-item .timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px var(--bs-border-color); }
    .decision-timeline-item .timeline-dot.bg-primary { box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-success { box-shadow: 0 0 0 2px rgba(var(--bs-success-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-danger { box-shadow: 0 0 0 2px rgba(var(--bs-danger-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-warning { box-shadow: 0 0 0 2px rgba(var(--bs-warning-rgb), 0.3); }
    .locked-overlay { opacity: 0.7; pointer-events: none; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="fw-bold fs-5">Mes Objectifs</span>
                @if($campaign)
                    <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }} ms-2">{{ $campaign->status_label }}</span>
                    <small class="text-muted ms-2">{{ $campaign->name }} ({{ $campaign->year }})</small>
                @endif
                @if($userCampaign)
                    <span class="badge bg-{{ $userCampaign->objective_status_color }}-subtle text-{{ $userCampaign->objective_status_color }} ms-2">{{ $userCampaign->objective_status_label }}</span>
                @endif
            </div>
            @if($userCampaign && in_array($userCampaign->objective_status, ['draft', 'returned']))
            <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" onclick="submitToSupervisor()">
                <i class="fi fi-rr-paper-plane me-1"></i> Soumettre au supérieur
            </button>
            @endif
        </div>

        @if(!$campaign)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-folder fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Aucune campagne lancée pour le moment</h5>
                    <p class="text-muted mb-0">Vous serez notifié lorsqu'une campagne sera démarrée.</p>
                </div>
            </div>
        @elseif(!$userCampaign)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-user-slash fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Vous n'êtes pas participant à cette campagne</h5>
                    <p class="text-muted mb-0">Contactez votre administrateur si vous pensez qu'il s'agit d'une erreur.</p>
                </div>
            </div>
        @elseif($phase === 'evaluation_in_progress')
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-chart-histogram fs-1 d-block mb-3 text-warning"></i>
                    <h5>Phase d'évaluation en cours</h5>
                    <p class="text-muted mb-0">La phase d'évaluation est actuellement en cours. Votre évaluation sera disponible ici.</p>
                </div>
            </div>
        @else
            @php
                $isMidterm = in_array($phase, ['midterm_in_progress']);
                $canEdit = in_array($userCampaign->objective_status, ['draft', 'returned']) || $isMidterm;
                $isCompleted = $userCampaign->objective_status === 'completed';
                $isSubmitted = $userCampaign->objective_status === 'submitted';
                $totalWeight = $objectives->sum('weight');
                $validatedCount = $objectives->where('status', 'validated')->count();
                $rejectedCount = $objectives->where('status', 'rejected')->count();
                $totalCount = $objectives->count();
            @endphp

            {{-- Barre de progression --}}
            @if($totalCount > 0)
            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="fw-semibold">Progression : {{ $validatedCount }}/{{ $totalCount }} objectifs validés</small>
                        @if($rejectedCount > 0)
                        <small class="text-danger fw-semibold">{{ $rejectedCount }} refusé(s)</small>
                        @endif
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $totalCount > 0 ? ($validatedCount / $totalCount * 100) : 0 }}%"></div>
                        @if($rejectedCount > 0)
                        <div class="progress-bar bg-danger" style="width: {{ $totalCount > 0 ? ($rejectedCount / $totalCount * 100) : 0 }}%"></div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($isMidterm)
            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-time-half-past me-2"></i>
                <div><strong>Phase mi-parcours</strong> — Vous pouvez modifier vos objectifs et ajuster les pondérations. Les modifications seront tracées dans l'historique.</div>
            </div>
            @elseif($isSubmitted)
            <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-clock me-2"></i>
                <div>Vos objectifs sont en attente de validation par votre supérieur. Vous pouvez uniquement ajouter des commentaires.</div>
            </div>
            @elseif($isCompleted)
            <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-check-circle me-2"></i>
                <div>Tous vos objectifs ont été validés. La phase objectifs est terminée.</div>
            </div>
            @elseif($userCampaign->objective_status === 'returned')
            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-undo me-2"></i>
                <div>Vos objectifs ont été retournés par votre supérieur. Corrigez les objectifs refusés et soumettez à nouveau.</div>
            </div>
            @endif

            <div class="row g-3">
                {{-- Sidebar catégories --}}
                <div class="col-md-3">
                    <div class="card objective-sidebar">
                        <div class="card-header">
                            <h6 class="card-title mb-0"><i class="fi fi-rr-list me-1"></i> Catégories</h6>
                        </div>
                        <div class="card-body p-2">
                            <div class="category-item active" data-category="all" onclick="filterCategory('all', this)">
                                <span><i class="fi fi-rr-apps me-2"></i>Tous les objectifs</span>
                                <span class="badge rounded-pill bg-primary" id="count-all">{{ $objectives->count() }}</span>
                            </div>
                            @foreach($categories as $cat)
                            <div class="category-item" data-category="{{ $cat->uuid }}" onclick="filterCategory('{{ $cat->uuid }}', this)">
                                <span><i class="fi fi-rr-bullseye-arrow me-2"></i>{{ $cat->name }}</span>
                                <span class="badge rounded-pill bg-primary-subtle text-primary" id="count-{{ $cat->uuid }}">{{ $objectives->where('objective_category_uuid', $cat->uuid)->count() }}</span>
                            </div>
                            @endforeach
                        </div>
                        {{-- Onglet Décisions --}}
                        <div class="card-header border-top">
                            <h6 class="card-title mb-0"><i class="fi fi-rr-time-past me-1"></i> Décisions</h6>
                        </div>
                        <div class="card-body p-3">
                            @if($decisions->count() > 0)
                            <div class="decision-timeline">
                                @foreach($decisions as $decision)
                                <div class="decision-timeline-item">
                                    <div class="timeline-dot bg-{{ $decision->action_color }}"></div>
                                    <div>
                                        <span class="fw-semibold d-block" style="font-size: 12px;">{{ $decision->action_label }}</span>
                                        <small class="text-muted d-block">{{ $decision->actor->full_name }}</small>
                                        @if($decision->comment)
                                        <small class="d-block mt-1" style="font-size: 12px;">{{ $decision->comment }}</small>
                                        @endif
                                        <small class="text-muted" style="font-size: 11px;">{{ $decision->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-muted text-center mb-0" style="font-size: 13px;">Aucune décision pour le moment.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Liste des objectifs --}}
                <div class="col-md-9">
                    {{-- Panneau liste --}}
                    <div id="listPanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0" id="categoryTitle"><i class="fi fi-rr-bullseye me-1"></i> Tous les objectifs</h6>
                                @if($canEdit)
                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="showAddForm()">
                                    <i class="fi fi-rr-plus me-1"></i> Nouvel objectif
                                </button>
                                @endif
                            </div>
                            <div class="card-body" id="objectivesList">
                                @if($objectives->count() > 0)
                                    @foreach($objectives as $obj)
                                    <div class="objective-card card mb-2 p-3" data-uuid="{{ $obj->uuid }}" data-category="{{ $obj->objective_category_uuid }}" onclick="showObjective('{{ $obj->uuid }}')">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <h6 class="mb-0 fw-semibold objective-title">{{ $obj->title }}</h6>
                                                    @if($obj->status !== 'pending')
                                                    <span class="badge bg-{{ $obj->status_color }}-subtle text-{{ $obj->status_color }}">{{ $obj->status_label }}</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2 align-items-center">
                                                    @if($obj->weight > 0)
                                                    <small class="text-muted">Pondération: {{ $obj->weight }}%</small>
                                                    @endif
                                                    @if($obj->rejection_reason)
                                                    <small class="text-danger"><i class="fi fi-rr-info me-1"></i>{{ Str::limit($obj->rejection_reason, 50) }}</small>
                                                    @endif
                                                    @if($obj->comments->count() > 0)
                                                    <small class="text-muted"><i class="fi fi-rr-comment-alt"></i> {{ $obj->comments->count() }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($canEdit && $obj->status !== 'validated')
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle waves-effect" onclick="event.stopPropagation(); showEditForm('{{ $obj->uuid }}')" data-bs-toggle="tooltip" title="Modifier">
                                                    <i class="fi fi-rr-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect" onclick="event.stopPropagation(); deleteObjective('{{ $obj->uuid }}')" data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center text-muted py-4" id="emptyState">
                                        <i class="fi fi-rr-bullseye fs-1 d-block mb-3"></i>
                                        <h6>Aucun objectif défini</h6>
                                        <p class="mb-0">Commencez par ajouter vos objectifs pour cette campagne.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Panneau détail objectif --}}
                    <div id="detailPanel" class="detail-panel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0"><i class="fi fi-rr-eye me-1"></i> Détail de l'objectif</h6>
                                <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="hideDetail()">
                                    <i class="fi fi-rr-arrow-left me-1"></i> Retour à la liste
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="detailContent"></div>
                                <hr>
                                <h6 class="fw-semibold mb-3"><i class="fi fi-rr-comment-alt me-1"></i> Commentaires</h6>
                                <div id="commentsList"></div>
                                <div class="mt-3">
                                    <form id="commentForm" onsubmit="submitComment(event)">
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control form-control-sm" id="commentInput" placeholder="Ajouter un commentaire..." required>
                                            <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">
                                                <i class="fi fi-rr-paper-plane"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Panneau formulaire ajout/modification --}}
                    @if($canEdit)
                    <div id="formPanel" class="detail-panel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0" id="formTitle"><i class="fi fi-rr-plus me-1"></i> Nouvel objectif</h6>
                                <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="hideForm()">
                                    <i class="fi fi-rr-arrow-left me-1"></i> Retour à la liste
                                </button>
                            </div>
                            <div class="card-body">
                                <form id="objectiveForm" onsubmit="submitObjective(event)">
                                    <input type="hidden" id="formObjectiveUuid" value="">
                                    <input type="hidden" name="user_campaign_uuid" value="{{ $userCampaign->uuid }}">
                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <label for="formTitle_input" class="form-label">Titre <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="formTitle_input" name="title" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="formCategory" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                            <select class="form-select" id="formCategory" name="objective_category_uuid" required>
                                                <option value="">Sélectionner...</option>
                                                @foreach($categories as $cat)
                                                <option value="{{ $cat->uuid }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="formWeight" class="form-label">Pondération (%) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="formWeight" name="weight" min="0" max="100" value="0" required>
                                            <small class="text-muted" id="weightHelper">Total actuel : {{ $totalWeight }}%</small>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="formDescription" class="form-label">Description</label>
                                            <textarea class="form-control" id="formDescription" name="description" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary waves-effect" onclick="hideForm()">Annuler</button>
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fi fi-rr-check me-1"></i> <span id="formSubmitLabel">Enregistrer</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentCategory = 'all';
    let currentObjectiveUuid = null;
    let canEdit = {{ $canEdit ?? false ? 'true' : 'false' }};
    let categoriesMap = @json($categories->keyBy('uuid') ?? []);

    function filterCategory(categoryUuid, el) {
        currentCategory = categoryUuid;
        document.querySelectorAll('.category-item').forEach(item => item.classList.remove('active'));
        el.classList.add('active');

        if (categoryUuid === 'all') {
            document.getElementById('categoryTitle').innerHTML = '<i class="fi fi-rr-bullseye me-1"></i> Tous les objectifs';
        } else {
            let catName = categoriesMap[categoryUuid] ? categoriesMap[categoryUuid].name : '';
            document.getElementById('categoryTitle').innerHTML = '<i class="fi fi-rr-bullseye me-1"></i> ' + catName;
        }

        document.querySelectorAll('.objective-card').forEach(card => {
            if (categoryUuid === 'all' || card.dataset.category === categoryUuid) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        hideDetail();
        hideForm();
    }

    function showAddForm() {
        if (!canEdit) return;
        document.getElementById('listPanel').style.display = 'none';
        document.getElementById('detailPanel').classList.remove('show');
        document.getElementById('formPanel').classList.add('show');
        document.getElementById('formTitle').innerHTML = '<i class="fi fi-rr-plus me-1"></i> Nouvel objectif';
        document.getElementById('formSubmitLabel').textContent = 'Enregistrer';
        document.getElementById('formObjectiveUuid').value = '';
        document.getElementById('objectiveForm').reset();
        document.getElementById('formWeight').value = 0;
        if (currentCategory !== 'all') {
            document.getElementById('formCategory').value = currentCategory;
        }
    }

    function showEditForm(uuid) {
        if (!canEdit) return;
        loader();
        $.ajax({
            url: '/objectives/' + uuid,
            type: 'GET',
            success: function(data) {
                loader('hide');
                if (data.success) {
                    let obj = data.objective;
                    if (obj.status === 'validated') {
                        SendError('Cet objectif a été validé et ne peut plus être modifié.');
                        return;
                    }
                    document.getElementById('listPanel').style.display = 'none';
                    document.getElementById('detailPanel').classList.remove('show');
                    document.getElementById('formPanel').classList.add('show');
                    document.getElementById('formTitle').innerHTML = '<i class="fi fi-rr-pencil me-1"></i> Modifier l\'objectif';
                    document.getElementById('formSubmitLabel').textContent = 'Mettre à jour';
                    document.getElementById('formObjectiveUuid').value = obj.uuid;
                    document.getElementById('formTitle_input').value = obj.title;
                    document.getElementById('formCategory').value = obj.objective_category_uuid;
                    document.getElementById('formWeight').value = obj.weight || 0;
                    document.getElementById('formDescription').value = obj.description || '';
                }
            },
            error: function(data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    }

    function hideForm() {
        let fp = document.getElementById('formPanel');
        if (fp) fp.classList.remove('show');
        document.getElementById('listPanel').style.display = '';
    }

    function buildObjectiveCard(obj) {
        let statusColors = { pending: 'secondary', validated: 'success', rejected: 'danger' };
        let statusLabels = { pending: 'En attente', validated: 'Validé', rejected: 'Refusé' };
        let statusColor = statusColors[obj.status] || 'secondary';
        let statusLabel = statusLabels[obj.status] || '';
        let statusBadge = (obj.status && obj.status !== 'pending') ? `<span class="badge bg-${statusColor}-subtle text-${statusColor}">${statusLabel}</span>` : '';
        let weightHtml = obj.weight > 0 ? `<small class="text-muted">Pondération: ${obj.weight}%</small>` : '';
        let rejectionHtml = obj.rejection_reason ? `<small class="text-danger"><i class="fi fi-rr-info me-1"></i>${obj.rejection_reason.substring(0, 50)}</small>` : '';
        let actionsHtml = '';
        if (canEdit && obj.status !== 'validated') {
            actionsHtml = `
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle waves-effect" onclick="event.stopPropagation(); showEditForm('${obj.uuid}')" data-bs-toggle="tooltip" title="Modifier">
                        <i class="fi fi-rr-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect" onclick="event.stopPropagation(); deleteObjective('${obj.uuid}')" data-bs-toggle="tooltip" title="Supprimer">
                        <i class="fi fi-rr-trash"></i>
                    </button>
                </div>`;
        }
        return `
            <div class="objective-card card mb-2 p-3" data-uuid="${obj.uuid}" data-category="${obj.objective_category_uuid}" onclick="showObjective('${obj.uuid}')">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="mb-0 fw-semibold objective-title">${obj.title}</h6>
                            ${statusBadge}
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            ${weightHtml} ${rejectionHtml}
                        </div>
                    </div>
                    ${actionsHtml}
                </div>
            </div>`;
    }

    function updateCounters() {
        let allCards = document.querySelectorAll('.objective-card');
        document.getElementById('count-all').textContent = allCards.length;
        Object.keys(categoriesMap).forEach(catUuid => {
            let countEl = document.getElementById('count-' + catUuid);
            if (countEl) {
                countEl.textContent = document.querySelectorAll('.objective-card[data-category="' + catUuid + '"]').length;
            }
        });
    }

    function submitObjective(e) {
        e.preventDefault();
        if (!canEdit) return;
        let uuid = document.getElementById('formObjectiveUuid').value;
        let form = document.getElementById('objectiveForm');
        let formData = new FormData(form);

        let url;
        if (uuid) {
            url = '/objectives/' + uuid;
            formData.append('_method', 'PUT');
        } else {
            url = '/objectives';
        }

        loader();
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                loader('hide');
                if (data.success) {
                    let obj = data.objective;
                    if (uuid) {
                        let existingCard = document.querySelector('.objective-card[data-uuid="' + uuid + '"]');
                        if (existingCard) existingCard.outerHTML = buildObjectiveCard(obj);
                    } else {
                        let emptyState = document.getElementById('emptyState');
                        if (emptyState) emptyState.remove();
                        document.getElementById('objectivesList').insertAdjacentHTML('beforeend', buildObjectiveCard(obj));
                    }
                    updateCounters();
                    hideForm();
                    Swal.fire({ icon: 'success', title: 'Succès', text: data.message, showConfirmButton: false, timer: 1500 });
                } else {
                    SendError(data.message);
                }
            },
            error: function(data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    }

    function showObjective(uuid) {
        currentObjectiveUuid = uuid;
        loader();
        $.ajax({
            url: '/objectives/' + uuid,
            type: 'GET',
            success: function(data) {
                loader('hide');
                if (data.success) {
                    let obj = data.objective;
                    renderDetail(obj);
                    renderComments(obj.comments || []);
                    // Masquer le formulaire de commentaire si l'objectif est validé
                    let commentForm = document.getElementById('commentForm');
                    if (obj.status === 'validated') {
                        commentForm.style.display = 'none';
                    } else {
                        commentForm.style.display = '';
                    }
                    document.getElementById('listPanel').style.display = 'none';
                    let fp = document.getElementById('formPanel');
                    if (fp) fp.classList.remove('show');
                    document.getElementById('detailPanel').classList.add('show');
                    document.querySelectorAll('.objective-card').forEach(c => c.classList.remove('active'));
                    let card = document.querySelector('.objective-card[data-uuid="' + uuid + '"]');
                    if (card) card.classList.add('active');
                }
            },
            error: function(data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    }

    function renderDetail(obj) {
        let statusColors = { pending: 'secondary', validated: 'success', rejected: 'danger' };
        let statusLabels = { pending: 'En attente', validated: 'Validé', rejected: 'Refusé' };
        let statusColor = statusColors[obj.status] || 'secondary';
        let statusLabel = statusLabels[obj.status] || '';
        let statusBadge = (obj.status && obj.status !== 'pending') ? `<span class="badge bg-${statusColor}-subtle text-${statusColor}">${statusLabel}</span>` : '';

        let actionsHtml = '';
        if (canEdit && obj.status !== 'validated') {
            actionsHtml = `
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-primary waves-effect" onclick="showEditForm('${obj.uuid}')">
                        <i class="fi fi-rr-pencil me-1"></i> Modifier
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger waves-effect" onclick="deleteObjective('${obj.uuid}')">
                        <i class="fi fi-rr-trash me-1"></i> Supprimer
                    </button>
                </div>`;
        }

        let html = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="fw-bold mb-1">${obj.title}</h5>
                    ${statusBadge}
                    <span class="badge bg-primary-subtle text-primary ms-1">${obj.category ? obj.category.name : ''}</span>
                </div>
                ${actionsHtml}
            </div>
            ${obj.rejection_reason ? `<div class="alert alert-danger py-2 mb-3"><i class="fi fi-rr-cross-circle me-1"></i> <strong>Motif du refus :</strong> ${obj.rejection_reason}</div>` : ''}
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tbody>
                        ${obj.description ? `<tr><td class="text-muted fw-semibold" style="width:140px">Description</td><td>${obj.description}</td></tr>` : ''}
                        <tr><td class="text-muted fw-semibold" style="width:140px">Catégorie</td><td>${obj.category ? obj.category.name + ' (' + obj.category.percentage + '%)' : ''}</td></tr>
                        ${obj.weight > 0 ? `<tr><td class="text-muted fw-semibold" style="width:140px">Pondération</td><td>${obj.weight}%</td></tr>` : ''}
                        <tr><td class="text-muted fw-semibold">Créé le</td><td>${new Date(obj.created_at).toLocaleDateString('fr-FR', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'})}</td></tr>
                    </tbody>
                </table>
            </div>`;
        document.getElementById('detailContent').innerHTML = html;
    }

    function renderComments(comments) {
        let authUuid = '{{ Auth::id() }}';
        if (comments.length === 0) {
            document.getElementById('commentsList').innerHTML = '<p class="text-muted text-center py-2">Aucun commentaire pour le moment.</p>';
            return;
        }
        let html = '';
        comments.forEach(c => {
            let initials = (c.user.first_name ? c.user.first_name[0] : '') + (c.user.last_name ? c.user.last_name[0] : '');
            let deleteBtn = c.user_uuid === authUuid ? `<button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" onclick="deleteComment('${c.uuid}')" data-bs-toggle="tooltip" title="Supprimer"><i class="fi fi-rr-trash" style="font-size:11px"></i></button>` : '';
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
        document.getElementById('commentsList').innerHTML = html;
    }

    function hideDetail() {
        document.getElementById('detailPanel').classList.remove('show');
        document.getElementById('listPanel').style.display = '';
        document.querySelectorAll('.objective-card').forEach(c => c.classList.remove('active'));
        currentObjectiveUuid = null;
    }

    function deleteObjective(uuid) {
        if (!canEdit) return;
        Swal.fire({
            icon: 'warning', title: 'Confirmation', text: 'Voulez-vous vraiment supprimer cet objectif ?',
            showDenyButton: true, showCancelButton: false, confirmButtonText: 'Oui, supprimer', denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/objectives/' + uuid, type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            let card = document.querySelector('.objective-card[data-uuid="' + uuid + '"]');
                            if (card) card.remove();
                            updateCounters();
                            hideDetail();
                            if (document.querySelectorAll('.objective-card').length === 0) {
                                document.getElementById('objectivesList').innerHTML = `
                                    <div class="text-center text-muted py-4" id="emptyState">
                                        <i class="fi fi-rr-bullseye fs-1 d-block mb-3"></i>
                                        <h6>Aucun objectif défini</h6>
                                        <p class="mb-0">Commencez par ajouter vos objectifs pour cette campagne.</p>
                                    </div>`;
                            }
                            Swal.fire({ icon: 'success', title: 'Succès', text: data.message, showConfirmButton: false, timer: 1500 });
                        } else { SendError(data.message); }
                    },
                    error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }

    function submitComment(e) {
        e.preventDefault();
        let content = document.getElementById('commentInput').value;
        if (!content || !currentObjectiveUuid) return;
        $.ajax({
            url: '/objectives/' + currentObjectiveUuid + '/comments', type: 'POST',
            data: { content: content },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                if (data.success) { document.getElementById('commentInput').value = ''; showObjective(currentObjectiveUuid); }
                else { SendError(data.message); }
            },
            error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }

    function deleteComment(commentUuid) {
        Swal.fire({
            icon: 'warning', title: 'Confirmation', text: 'Voulez-vous vraiment supprimer ce commentaire ?',
            showDenyButton: true, showCancelButton: false, confirmButtonText: 'Oui, supprimer', denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/objective-comments/' + commentUuid, type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) { if (data.success) { showObjective(currentObjectiveUuid); } else { SendError(data.message); } },
                    error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }

    function submitToSupervisor() {
        if (document.querySelectorAll('.objective-card').length === 0) {
            SendError('Vous devez avoir au moins un objectif avant de soumettre.');
            return;
        }
        Swal.fire({
            icon: 'question', title: 'Soumettre les objectifs',
            text: 'Êtes-vous sûr de vouloir soumettre vos objectifs à votre supérieur ? Vous ne pourrez plus les modifier jusqu\'à son retour.',
            showDenyButton: true, showCancelButton: false, confirmButtonText: 'Oui, soumettre', denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/objectives/submit', type: 'POST',
                    data: { user_campaign_uuid: '{{ $userCampaign->uuid ?? '' }}' },
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
</script>
@endpush
