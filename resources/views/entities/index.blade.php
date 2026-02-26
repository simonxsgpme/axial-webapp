@extends('layouts.app')

@section('title', 'Gestion des Entités')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Liste des Entités</h5>
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#entityModal" onclick="openCreateModal()">
                    <i class="fi fi-rr-plus me-1"></i> Ajouter une entité
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Utilisateurs</th>
                                <th>Date de création</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entities as $index => $entity)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="fw-semibold">{{ $entity->name }}</span></td>
                                <td>
                                    @if($entity->category === 'direction')
                                        <span class="badge bg-primary-subtle text-primary">Direction</span>
                                    @elseif($entity->category === 'service')
                                        <span class="badge bg-success-subtle text-success">Service</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info">Département</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $entity->users->count() }}</span></td>
                                <td>{{ $entity->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle waves-effect waves-light" onclick="openEditModal('{{ $entity->uuid }}', '{{ $entity->name }}', '{{ $entity->category }}')" data-bs-toggle="tooltip" title="Modifier">
                                        <i class="fi fi-rr-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect waves-light" onclick="deleteEntity('{{ $entity->uuid }}')" data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fi fi-rr-building fs-3 d-block mb-2"></i>
                                    Aucune entité trouvée.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout / Modification -->
<div class="modal fade" id="entityModal" tabindex="-1" aria-labelledby="entityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="entityForm" class="sendForm" method="POST" action="{{ route('settings.entities.store') }}">
                @csrf
                <input type="hidden" name="_method" id="entityMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="entityModalLabel">Ajouter une entité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="entityName" class="form-label">Nom de l'entité <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="entityName" name="name" placeholder="Ex: Direction Générale" required>
                    </div>
                    <div class="mb-3">
                        <label for="entityCategory" class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select class="form-select" id="entityCategory" name="category" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="direction">Direction</option>
                            <option value="service">Service</option>
                            <option value="departement">Département</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-check me-1"></i> <span id="entitySubmitText">Enregistrer</span>
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
        document.getElementById('entityModalLabel').textContent = 'Ajouter une entité';
        document.getElementById('entitySubmitText').textContent = 'Enregistrer';
        document.getElementById('entityForm').action = "{{ route('settings.entities.store') }}";
        document.getElementById('entityMethod').value = 'POST';
        document.getElementById('entityName').value = '';
        document.getElementById('entityCategory').value = '';
    }

    function openEditModal(uuid, name, category) {
        document.getElementById('entityModalLabel').textContent = 'Modifier l\'entité';
        document.getElementById('entitySubmitText').textContent = 'Mettre à jour';
        document.getElementById('entityForm').action = "/settings/entities/" + uuid;
        document.getElementById('entityMethod').value = 'PUT';
        document.getElementById('entityName').value = name;
        document.getElementById('entityCategory').value = category;

        var modal = new bootstrap.Modal(document.getElementById('entityModal'));
        modal.show();
    }

    function deleteEntity(uuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer cette entité ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, supprimer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/settings/entities/' + uuid,
                    type: 'DELETE',
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
