@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Liste des Rôles</h5>
                <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openCreateModal()">
                    <i class="fi fi-rr-plus me-1"></i> Ajouter un rôle
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
                                <th>Utilisateurs</th>
                                <th>Date de création</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="fw-semibold">{{ $role->name }}</span></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $role->slug }}</span></td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $role->users->count() }}</span></td>
                                <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('settings.roles.permissions', $role->uuid) }}" class="btn btn-sm btn-icon btn-outline-success rounded-circle waves-effect waves-light" data-bs-toggle="tooltip" title="Permissions">
                                        <i class="fi fi-rr-key"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary rounded-circle waves-effect waves-light" onclick="openEditModal('{{ $role->uuid }}', '{{ $role->name }}')" data-bs-toggle="tooltip" title="Modifier">
                                        <i class="fi fi-rr-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect waves-light" onclick="deleteRole('{{ $role->uuid }}')" data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fi fi-rr-shield fs-3 d-block mb-2"></i>
                                    Aucun rôle trouvé.
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
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="roleForm" class="sendForm" method="POST" action="{{ route('settings.roles.store') }}">
                @csrf
                <input type="hidden" name="_method" id="roleMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Ajouter un rôle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="roleName" name="name" placeholder="Ex: Administrateur" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-check me-1"></i> <span id="roleSubmitText">Enregistrer</span>
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
        document.getElementById('roleModalLabel').textContent = 'Ajouter un rôle';
        document.getElementById('roleSubmitText').textContent = 'Enregistrer';
        document.getElementById('roleForm').action = "{{ route('settings.roles.store') }}";
        document.getElementById('roleMethod').value = 'POST';
        document.getElementById('roleName').value = '';
    }

    function openEditModal(uuid, name) {
        document.getElementById('roleModalLabel').textContent = 'Modifier le rôle';
        document.getElementById('roleSubmitText').textContent = 'Mettre à jour';
        document.getElementById('roleForm').action = "/settings/roles/" + uuid;
        document.getElementById('roleMethod').value = 'PUT';
        document.getElementById('roleName').value = name;

        var modal = new bootstrap.Modal(document.getElementById('roleModal'));
        modal.show();
    }

    function deleteRole(uuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer ce rôle ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, supprimer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/settings/roles/' + uuid,
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
