@extends('layouts.app')

@section('title', 'Objectifs de ' . $userCampaign->user->full_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <a href="{{ route('campaigns.show', $campaign->uuid) }}" class="btn btn-outline-secondary btn-sm waves-effect me-2">
                    <i class="fi fi-rr-arrow-left me-1"></i> Retour
                </a>
                <span class="fw-bold fs-5">Objectifs de {{ $userCampaign->user->full_name }}</span>
                <small class="text-muted ms-2">{{ $campaign->name }} ({{ $campaign->year }})</small>
            </div>
            <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="showAddForm()">
                <i class="fi fi-rr-plus me-1"></i> Nouvel objectif
            </button>
        </div>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fi fi-rr-list me-1"></i> Catégories</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="category-item active" data-category="all" onclick="filterCategory('all', this)">
                            <span><i class="fi fi-rr-apps me-2"></i>Tous</span>
                            <span class="badge rounded-pill bg-primary" id="count-all">{{ $userCampaign->objectives->count() }}</span>
                        </div>
                        @foreach($categories as $cat)
                        <div class="category-item" data-category="{{ $cat->uuid }}" onclick="filterCategory('{{ $cat->uuid }}', this)">
                            <span><i class="fi fi-rr-bullseye-arrow me-2"></i>{{ $cat->name }}</span>
                            <span class="badge rounded-pill bg-primary-subtle text-primary" id="count-{{ $cat->uuid }}">{{ $userCampaign->objectives->where('objective_category_uuid', $cat->uuid)->count() }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div id="listPanel">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0" id="categoryTitle"><i class="fi fi-rr-bullseye me-1"></i> Tous les objectifs</h6>
                        </div>
                        <div class="card-body" id="objectivesList">
                            @if($userCampaign->objectives->count() > 0)
                                @foreach($userCampaign->objectives as $obj)
                                <div class="card mb-2 p-3 objective-card" data-uuid="{{ $obj->uuid }}" data-category="{{ $obj->objective_category_uuid }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $obj->title }}</h6>
                                            <div class="d-flex gap-2 align-items-center">
                                                <span class="badge bg-primary-subtle text-primary">{{ $obj->category->name ?? '' }}</span>
                                                @if($obj->weight > 0)
                                                <small class="text-muted">Pondération: {{ $obj->weight }}%</small>
                                                @endif
                                                @if($obj->description)
                                                <small class="text-muted">{{ Str::limit($obj->description, 60) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle waves-effect" onclick="showEditForm('{{ $obj->uuid }}')" title="Modifier">
                                                <i class="fi fi-rr-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect" onclick="deleteObjective('{{ $obj->uuid }}')" title="Supprimer">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4" id="emptyState">
                                    <i class="fi fi-rr-bullseye fs-1 d-block mb-3"></i>
                                    <h6>Aucun objectif</h6>
                                    <p class="mb-0">Ajoutez des objectifs pour ce participant.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="formPanel" style="display:none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0" id="formTitle"><i class="fi fi-rr-plus me-1"></i> Nouvel objectif</h6>
                            <button type="button" class="btn btn-outline-secondary btn-sm waves-effect" onclick="hideForm()">
                                <i class="fi fi-rr-arrow-left me-1"></i> Retour
                            </button>
                        </div>
                        <div class="card-body">
                            <form id="objectiveForm" onsubmit="submitObjective(event)">
                                <input type="hidden" id="formObjectiveUuid" value="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Titre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="formTitleInput" name="title" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                                        <select class="form-select" id="formCategory" name="objective_category_uuid" required>
                                            <option value="">Sélectionner...</option>
                                            @foreach($categories as $cat)
                                            <option value="{{ $cat->uuid }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Pondération (%)</label>
                                        <input type="number" class="form-control" id="formWeight" name="weight" min="0" max="100" value="0">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Description</label>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .category-item { cursor: pointer; padding: 8px 12px; border-radius: var(--bs-border-radius); transition: all 0.2s; display: flex; align-items: center; justify-content: space-between; }
    .category-item:hover, .category-item.active { background-color: rgba(var(--bs-primary-rgb), 0.08); color: var(--bs-primary); }
    .category-item.active { font-weight: 600; }
    .objective-card { transition: all 0.2s; border-left: 3px solid transparent; }
    .objective-card:hover { border-left-color: var(--bs-primary); }
</style>
@endsection

@push('scripts')
<script>
    let baseUrl = '/campaigns/{{ $campaign->uuid }}/participants/{{ $userCampaign->uuid }}/objectives';
    let categoriesMap = @json($categories->keyBy('uuid'));

    function filterCategory(catUuid, el) {
        document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        document.querySelectorAll('.objective-card').forEach(c => {
            c.style.display = (catUuid === 'all' || c.dataset.category === catUuid) ? '' : 'none';
        });
    }

    function showAddForm() {
        document.getElementById('listPanel').style.display = 'none';
        document.getElementById('formPanel').style.display = '';
        document.getElementById('formTitle').innerHTML = '<i class="fi fi-rr-plus me-1"></i> Nouvel objectif';
        document.getElementById('formSubmitLabel').textContent = 'Enregistrer';
        document.getElementById('formObjectiveUuid').value = '';
        document.getElementById('objectiveForm').reset();
    }

    function showEditForm(uuid) {
        let card = document.querySelector('.objective-card[data-uuid="' + uuid + '"]');
        if (!card) return;
        document.getElementById('listPanel').style.display = 'none';
        document.getElementById('formPanel').style.display = '';
        document.getElementById('formTitle').innerHTML = '<i class="fi fi-rr-pencil me-1"></i> Modifier l\'objectif';
        document.getElementById('formSubmitLabel').textContent = 'Mettre à jour';
        document.getElementById('formObjectiveUuid').value = uuid;
        // Load data via AJAX
        loader();
        $.get('/objectives/' + uuid, function(data) {
            loader('hide');
            if (data.success) {
                let obj = data.objective;
                document.getElementById('formTitleInput').value = obj.title;
                document.getElementById('formCategory').value = obj.objective_category_uuid;
                document.getElementById('formWeight').value = obj.weight || 0;
                document.getElementById('formDescription').value = obj.description || '';
            }
        }).fail(function() { loader('hide'); });
    }

    function hideForm() {
        document.getElementById('formPanel').style.display = 'none';
        document.getElementById('listPanel').style.display = '';
    }

    function submitObjective(e) {
        e.preventDefault();
        let uuid = document.getElementById('formObjectiveUuid').value;
        let formData = new FormData(document.getElementById('objectiveForm'));
        let url = uuid ? (baseUrl + '/' + uuid) : baseUrl;
        if (uuid) formData.append('_method', 'PUT');
        loader();
        $.ajax({
            url: url, type: 'POST', data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false, contentType: false, processData: false,
            success: function(data) {
                loader('hide');
                if (data.success) { sendSuccess(data.message, 'back'); }
                else { SendError(data.message); }
            },
            error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Erreur'); }
        });
    }

    function deleteObjective(uuid) {
        Swal.fire({
            icon: 'warning', title: 'Confirmation',
            text: 'Supprimer cet objectif ?',
            showDenyButton: true, confirmButtonText: 'Oui', denyButtonText: 'Non',
        }).then((r) => {
            if (r.isConfirmed) {
                loader();
                $.ajax({
                    url: baseUrl + '/' + uuid, type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) { loader('hide'); if (data.success) sendSuccess(data.message, 'back'); else SendError(data.message); },
                    error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Erreur'); }
                });
            }
        });
    }
</script>
@endpush
