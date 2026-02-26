@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Liste des Utilisateurs</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fi fi-rr-file-import me-1"></i> Importer Excel
                    </button>
                    <a href="{{ route('users.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-plus me-1"></i> Ajouter un utilisateur
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom complet</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="rounded-circle">
                                            @else
                                                <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block">{{ $user->full_name }}</span>
                                            <small class="text-muted">{{ $user->position ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    @if($user->role)
                                        <span class="badge bg-primary-subtle text-primary">{{ $user->role->name }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Non défini</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success-subtle text-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('users.show', $user->uuid) }}" class="btn btn-sm btn-icon btn-outline-info rounded-circle waves-effect waves-light" data-bs-toggle="tooltip" title="Voir">
                                        <i class="fi fi-rr-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle waves-effect waves-light" onclick="deleteUser('{{ $user->uuid }}')" data-bs-toggle="tooltip" title="Supprimer">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fi fi-rr-users fs-3 d-block mb-2"></i>
                                    Aucun utilisateur trouvé.
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
<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel"><i class="fi fi-rr-file-import me-1"></i> Importer des utilisateurs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 mb-3">
                        <small><i class="fi fi-rr-info me-1"></i> Le fichier doit contenir les colonnes : <strong>Nom, Prenom, Email</strong> (obligatoires), Telephone, Poste, Sigle_Entite (optionnelles). Format Excel (.xlsx) ou CSV (séparateur: point-virgule).</small>
                    </div>
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Fichier Excel ou CSV <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".csv,.txt,.xlsx,.xls" required>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('users.import.template') }}" class="btn btn-sm btn-outline-primary waves-effect">
                            <i class="fi fi-rr-download me-1"></i> Télécharger le modèle Excel
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary waves-effect" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success waves-effect waves-light">
                        <i class="fi fi-rr-upload me-1"></i> Importer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '{{ route("users.import") }}',
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() { loader(); },
            success: function(data) {
                loader('hide');
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                    sendSuccess(data.message, data.urlback || 'back');
                } else {
                    SendError(data.message);
                }
            },
            error: function(data) {
                loader('hide');
                SendError(data.responseJSON?.message ?? 'Une erreur est survenue');
            }
        });
    });

    function deleteUser(uuid) {
        Swal.fire({
            icon: 'warning',
            title: 'Confirmation',
            text: 'Voulez-vous vraiment supprimer cet utilisateur ?',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Oui, supprimer',
            denyButtonText: 'Non, annuler',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + uuid,
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
