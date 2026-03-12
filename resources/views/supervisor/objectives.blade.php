@extends('layouts.app')

@section('title', 'Validation Objectifs')

@section('styles')
<style>
    .subordinate-card { cursor: pointer; transition: all 0.2s; }
    .subordinate-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 3px solid var(--bs-primary); }
    .subordinate-card.active { background-color: rgba(var(--bs-primary-rgb), 0.03); border-left: 3px solid var(--bs-primary); }
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
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle waves-effect" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fi fi-rr-download me-1"></i> Télécharger
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" id="downloadObjectivesWord"><i class="fi fi-rr-file-word me-2"></i> Fiche Objectifs (Word)</a></li>
                                                <li><a class="dropdown-item" href="#" id="downloadMidtermWord"><i class="fi fi-rr-file-word me-2"></i> Fiche Mi-Parcours (Word)</a></li>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm waves-effect waves-light" id="validateSelectedBtn" style="display:none;" onclick="validateSelected()">
                                            <i class="fi fi-rr-check me-1"></i> Valider la sélection (<span id="selectedCount">0</span>)
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" id="btnReturn" style="display:none;" onclick="returnObjectives()">
                                            <i class="fi fi-rr-undo me-1"></i> Retourner les objectifs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Onglets --}}
                        <ul class="nav nav-tabs mb-3" role="tablist" id="categoryTabs">
                            {{-- Les onglets de catégories seront générés dynamiquement --}}
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tabTimeline" role="tab">
                                    <i class="fi fi-rr-time-past me-1"></i> Historique
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content" id="categoryTabContent">
                            {{-- Les contenus des onglets de catégories seront générés dynamiquement --}}

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

{{-- Modal modification objectif --}}
<div class="modal fade" id="editObjectiveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fi fi-rr-pencil me-1"></i> Modifier l'objectif</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editObjectiveForm" onsubmit="submitEditObjective(event)">
                    <input type="hidden" id="editObjUuid" value="">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="editObjTitle" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editObjTitle" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="editObjCategory" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="editObjCategory" name="objective_category_uuid" required>
                                <option value="">Sélectionner...</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->uuid }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="editObjWeight" class="form-label">Pondération (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editObjWeight" name="weight" min="0" max="100" value="0" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="editObjDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editObjDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitEditObjective(event)">
                    <i class="fi fi-rr-check me-1"></i> Enregistrer
                </button>
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
    let currentActiveTab = null;

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

    // Gestionnaires pour les téléchargements Word
    $(document).on('click', '#downloadObjectivesWord', function(e) {
        e.preventDefault();
        if (currentUcData) {
            let campaignUuid = '{{ $campaign->uuid ?? "" }}';
            window.location.href = '/campaigns/' + campaignUuid + '/participants/' + currentUcData.uuid + '/word-objectives';
        }
    });

    $(document).on('click', '#downloadMidtermWord', function(e) {
        e.preventDefault();
        if (currentUcData) {
            let campaignUuid = '{{ $campaign->uuid ?? "" }}';
            window.location.href = '/campaigns/' + campaignUuid + '/participants/' + currentUcData.uuid + '/word-midterm';
        }
    });

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

        renderCategoryTabs(uc.objectives || [], uc.objective_status);
        renderTimeline(uc.decisions || []);
    }

    function renderCategoryTabs(objectives, ucStatus) {
        // Sauvegarder l'onglet actif avant de nettoyer
        let categoryTabsEl = document.getElementById('categoryTabs');
        let categoryTabContentEl = document.getElementById('categoryTabContent');
        let activeTab = categoryTabsEl.querySelector('.nav-link.active');
        if (activeTab && activeTab.getAttribute('href') !== '#tabTimeline') {
            currentActiveTab = activeTab.getAttribute('href');
        }
        
        // Supprimer tous les onglets sauf Historique (en collectant d'abord les éléments à supprimer)
        let tabsToRemove = [];
        categoryTabsEl.querySelectorAll('li').forEach(li => {
            let link = li.querySelector('a');
            if (link && link.getAttribute('href') !== '#tabTimeline') {
                tabsToRemove.push(li);
            }
        });
        tabsToRemove.forEach(li => li.remove());
        
        // Supprimer tous les contenus sauf Historique
        categoryTabContentEl.querySelectorAll('.tab-pane').forEach(pane => {
            if (pane.id !== 'tabTimeline') {
                pane.remove();
            }
        });

        // Regrouper les objectifs par catégorie
        let categoriesData = {};
        objectives.forEach(obj => {
            let catUuid = obj.objective_category_uuid;
            if (!categoriesData[catUuid]) {
                categoriesData[catUuid] = {
                    category: obj.category,
                    objectives: []
                };
            }
            categoriesData[catUuid].objectives.push(obj);
        });

        // Générer les onglets
        let tabsHtml = '';
        let contentHtml = '';
        let isFirst = true;

        Object.keys(categoriesData).forEach(catUuid => {
            let catData = categoriesData[catUuid];
            let catName = catData.category ? catData.category.name : 'Sans catégorie';
            let catDisplayName = catData.category ? catData.category.description : 'Sans catégorie';
            let catPercentage = catData.category ? catData.category.percentage : 0;
            let objCount = catData.objectives.length;
            let tabId = 'tabCat-' + catUuid;

            // Calculer le restant disponible pour la catégorie
            let catMax = catPercentage;
            let catUsed = 0;
            objectives.forEach(o => {
                if (o.objective_category_uuid === catUuid && o.status === 'validated') {
                    catUsed += (o.weight || 0);
                }
            });
            let catRemaining = catMax - catUsed;

            // Onglet
            tabsHtml += `
                <li class="nav-item">
                    <a class="nav-link ${isFirst ? 'active' : ''}" data-bs-toggle="tab" href="#${tabId}" role="tab">
                        <i class="fi fi-rr-bullseye me-1"></i> ${catName} <span class="badge bg-primary ms-1">${objCount}</span>
                    </a>
                </li>`;

            // Contenu
            contentHtml += `
                <div class="tab-pane fade ${isFirst ? 'show active' : ''}" id="${tabId}" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">${catDisplayName}</h6>
                            
                        </div>
                    </div>
                    ${renderObjectivesForCategory(catData.objectives, ucStatus, catUuid, catPercentage, objectives)}
                </div>`;

            isFirst = false;
        });

        // Si aucun objectif
        if (objectives.length === 0) {
            tabsHtml = `
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tabEmpty" role="tab">
                        <i class="fi fi-rr-bullseye me-1"></i> Objectifs <span class="badge bg-primary ms-1">0</span>
                    </a>
                </li>`;
            contentHtml = `
                <div class="tab-pane fade show active" id="tabEmpty" role="tabpanel">
                    <div class="card"><div class="card-body text-center py-4 text-muted">Aucun objectif défini.</div></div>
                </div>`;
        }

        // Insérer les onglets avant l'onglet Historique
        let timelineTab = categoryTabsEl.querySelector('li');
        timelineTab.insertAdjacentHTML('beforebegin', tabsHtml);

        // Insérer le contenu avant l'onglet Historique
        let timelineContent = categoryTabContentEl.querySelector('#tabTimeline');
        timelineContent.insertAdjacentHTML('beforebegin', contentHtml);
        
        // Restaurer l'onglet actif si possible
        if (currentActiveTab) {
            let tabToActivate = categoryTabsEl.querySelector('a[href="' + currentActiveTab + '"]');
            if (tabToActivate) {
                let tab = new bootstrap.Tab(tabToActivate);
                tab.show();
            }
        }
    }

    function renderObjectivesForCategory(objectives, ucStatus, catUuid, catPercentage, allObjectives) {
        let html = '';
        if (objectives.length === 0) {
            html = '<div class="card"><div class="card-body text-center py-4 text-muted">Aucun objectif dans cette catégorie.</div></div>';
        } else {
            let canReview = ucStatus === 'submitted';

            // Calculer le restant disponible pour la catégorie
            let catMax = catPercentage;
            let catUsed = 0;
            allObjectives.forEach(o => {
                if (o.objective_category_uuid === catUuid && o.status === 'validated' && objectives.findIndex(obj => obj.uuid === o.uuid) === -1) {
                    catUsed += (o.weight || 0);
                }
            });
            let catRemaining = catMax - catUsed;

            objectives.forEach(obj => {
                let statusColors = { pending: 'secondary', validated: 'success', rejected: 'dark' };
                let statusLabels = { pending: 'En attente', validated: 'Validé', rejected: 'Retourné' };

                let actionsHtml = '';
                // Recalculer le restant pour cet objectif spécifique
                let objCatRemaining = catRemaining;
                if (obj.status === 'validated') {
                    objCatRemaining += (obj.weight || 0);
                }

                if (canReview && obj.status === 'pending') {
                    actionsHtml = `
                        <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input objective-checkbox" type="checkbox" value="${obj.uuid}" id="check-${obj.uuid}" data-category="${catUuid}" data-remaining="${objCatRemaining}">
                                <label class="form-check-label" for="check-${obj.uuid}"></label>
                            </div>
                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                <span class="input-group-text">Poids</span>
                                <input type="number" class="form-control" id="weight-${obj.uuid}" min="0" max="${objCatRemaining}" value="${obj.weight || 0}" placeholder="%">
                            </div>
                            
                            <button type="button" class="btn btn-dark btn-sm waves-effect waves-light" onclick="rejectObj('${obj.uuid}')">
                                <i class="fi fi-rr-undo me-1"></i> A corriger
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm waves-effect" onclick="openEditModal('${obj.uuid}')">
                                <i class="fi fi-rr-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="openComments('${obj.uuid}', '${obj.title.replace(/'/g, "\\'")}')">
                                <i class="fi fi-rr-comment-alt me-1"></i> ${obj.comments ? obj.comments.length : 0}
                            </button>
                        </div>`;
                } else {
                    actionsHtml = `
                        <div class="d-flex gap-2 mt-2">
                            ${obj.weight > 0 ? `<small class="text-muted mt-2">Poids: ${obj.weight}%</small>` : ''}
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
                                ${obj.description ? `<p class="mb-0 mt-1" style="font-size: 13px;">${obj.description}</p>` : ''}
                                ${obj.rejection_reason ? `<div class="alert alert-danger py-1 px-2 mt-2 mb-0" style="font-size: 12px;"><i class="fi fi-rr-cross-circle me-1"></i>${obj.rejection_reason}</div>` : ''}
                            </div>
                        </div>
                        ${actionsHtml}
                    </div>`;
            });
        }
        return html;
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
                                // Sauvegarder l'onglet actif
                                let activeTabLink = document.querySelector('#categoryTabs .nav-link.active');
                                if (activeTabLink) {
                                    currentActiveTab = activeTabLink.getAttribute('href');
                                }
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
            icon: 'warning', title: 'Retourner pour correction ?',
            showDenyButton: true, confirmButtonText: 'Retourner', denyButtonText: 'Annuler',
            // confirmButtonColor: '#dc3545',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/supervisor/objectives/' + objUuid + '/reject', type: 'POST',
                    data: { rejection_reason: '' },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Objectif retourné pour correction', text: data.message, showConfirmButton: false, timer: 1500 });
                            // Sauvegarder l'onglet actif
                            let activeTabLink = document.querySelector('#categoryTabs .nav-link.active');
                            if (activeTabLink) {
                                currentActiveTab = activeTabLink.getAttribute('href');
                            }
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
            text: 'Les objectifs sans décision seront automatiquement retournés.',
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

    // Fonctions pour la modal de modification d'objectif
    function openEditModal(objUuid) {
        loader();
        $.ajax({
            url: '/objectives/' + objUuid,
            type: 'GET',
            success: function(data) {
                loader('hide');
                if (data.success) {
                    let obj = data.objective;
                    document.getElementById('editObjUuid').value = obj.uuid;
                    document.getElementById('editObjTitle').value = obj.title;
                    document.getElementById('editObjCategory').value = obj.objective_category_uuid;
                    document.getElementById('editObjWeight').value = obj.weight || 0;
                    document.getElementById('editObjDescription').value = obj.description || '';
                    
                    let modal = new bootstrap.Modal(document.getElementById('editObjectiveModal'));
                    modal.show();
                }
            },
            error: function(data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    }

    function submitEditObjective(e) {
        e.preventDefault();
        let objUuid = document.getElementById('editObjUuid').value;
        let form = document.getElementById('editObjectiveForm');
        let formData = new FormData(form);
        formData.append('_method', 'PUT');

        loader();
        $.ajax({
            url: '/objectives/' + objUuid,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                loader('hide');
                if (data.success) {
                    // Fermer la modal
                    let modal = bootstrap.Modal.getInstance(document.getElementById('editObjectiveModal'));
                    modal.hide();
                    
                    // Sauvegarder l'onglet actif
                    let activeTabLink = document.querySelector('#categoryTabs .nav-link.active');
                    if (activeTabLink) {
                        currentActiveTab = activeTabLink.getAttribute('href');
                    }
                    
                    // Recharger uniquement les données du collaborateur actuel
                    loadSubordinate(currentUcUuid);
                    
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Succès', 
                        text: 'L\'objectif a été modifié avec succès.', 
                        showConfirmButton: false, 
                        timer: 1500 
                    });
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

    // Gestion de la sélection des checkboxes
    $(document).on('change', '.objective-checkbox', function() {
        updateSelectedCount();
    });

    function updateSelectedCount() {
        let selectedCount = $('.objective-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        if (selectedCount > 0) {
            $('#validateSelectedBtn').show();
        } else {
            $('#validateSelectedBtn').hide();
        }
    }

    function validateSelected() {
        let selectedCheckboxes = $('.objective-checkbox:checked');
        if (selectedCheckboxes.length === 0) {
            SendError('Veuillez sélectionner au moins un objectif.');
            return;
        }

        let objectives = [];
        let hasError = false;
        let errorMessage = '';

        selectedCheckboxes.each(function() {
            let objUuid = $(this).val();
            let weight = $('#weight-' + objUuid).val();
            let remaining = $(this).data('remaining');

            if (!weight || weight < 0 || weight > 100) {
                hasError = true;
                errorMessage = 'Veuillez saisir une pondération valide (0-100) pour tous les objectifs sélectionnés.';
                return false;
            }

            if (parseInt(weight) > remaining) {
                hasError = true;
                errorMessage = 'La pondération de l\'un des objectifs dépasse le restant disponible pour sa catégorie.';
                return false;
            }

            objectives.push({
                uuid: objUuid,
                weight: parseInt(weight)
            });
        });

        if (hasError) {
            SendError(errorMessage);
            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Valider ' + objectives.length + ' objectif(s) ?',
            text: 'Vous êtes sur le point de valider ' + objectives.length + ' objectif(s) en une seule fois.',
            showDenyButton: true,
            confirmButtonText: 'Valider',
            denyButtonText: 'Annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/supervisor/objectives/validate-multiple',
                    type: 'POST',
                    data: { objectives: objectives },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Succès', 
                                text: data.message, 
                                showConfirmButton: false, 
                                timer: 1500 
                            });
                            if (data.all_validated) {
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                // Sauvegarder l'onglet actif
                                let activeTabLink = document.querySelector('#categoryTabs .nav-link.active');
                                if (activeTabLink) {
                                    currentActiveTab = activeTabLink.getAttribute('href');
                                }
                                loadSubordinate(currentUcUuid);
                            }
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
        });
    }
</script>
@endpush
