@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Liste des Permissions</h5>
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#permissionModal" onclick="openCreateModal()">
                    <i class="fi fi-rr-plus me-1"></i> Ajouter une permission
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Slug</th>
                                <th>Date de création</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $index => $permission)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="fw-semibold">{{ $permission->name }}</span></td>
                                <td><span class="badge bg-info-subtle text-info">{{ $permission->slug }}</span></td>
                                <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle waves-effect waves-light" onclick="openEditModal('{{ $permission->uuid }}', '{{ $permission->name }}')" data-bs-toggle="tooltip" title="Modifier">
                                        <i class="fi fi-rr-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect waves-light" onclick="deletePermission('{{ $permission->uuid }}')" data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fi fi-rr-key fs-3 d-block mb-2"></i>
                                    Aucune permission trouvée.
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
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="permissionForm" class="sendForm" method="POST" action="{{ route('settings.permissions.store') }}">
                @csrf
                <input type="hidden" name="_method" id="permissionMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="permissionModalLabel">Ajouter une permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">Nom de la permission <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="permissionName" name="name" placeholder="Ex: Gérer les utilisateurs" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-check me-1"></i> <span id="permissionSubmitText">Enregistrer</span>
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
        document.getElementById('permissionModalLabel').textContent = 'Ajouter une permission';
        document.getElementById('permissionSubmitText').textContent = 'Enregistrer';
        document.getElementById('permissionForm').action = "{{ route('settings.permissions.store') }}";
        document.getElementById('permissionMethod').value = 'POST';
        document.getElementById('permissionName').value = '';
    }

    function openEditModal(uuid, name) {
        document.getElementById('permissionModalLabel').textContent = 'Modifier la permission';
        document.getElementById('permissionSubmitText').textContent = 'Mettre à jour';
        document.getElementById('permissionForm').action = "/settings/permissions/" + uuid;
        document.getElementById('permissionMethod').value = 'PUT';
        document.getElementById('permissionName').value = name;

        var modal = new bootstrap.Modal(document.getElementById('permissionModal'));
        modal.show();
    }

    function deletePermission(uuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer cette permission ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, supprimer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/settings/permissions/' + uuid,
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
