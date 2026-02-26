@extends('layouts.app')

@section('title', 'Permissions - ' . $role->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <a href="{{ route('settings.roles.index') }}" class="btn btn-outline-secondary btn-sm waves-effect me-2">
                    <i class="fi fi-rr-arrow-left me-1"></i> Retour
                </a>
                <span class="fw-bold fs-5">Permissions du rôle : <span class="text-primary">{{ $role->name }}</span></span>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fi fi-rr-key me-1"></i> Liste des permissions
                </h5>
            </div>
            <div class="card-body">
                @if($role->rolePermissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Permission</th>
                                <th>Slug</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->rolePermissions as $index => $rp)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="fw-semibold">{{ $rp->permission->name }}</span></td>
                                <td><span class="badge bg-info-subtle text-info">{{ $rp->permission->slug }}</span></td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input permission-toggle"
                                               type="checkbox"
                                               role="switch"
                                               data-uuid="{{ $rp->uuid }}"
                                               {{ $rp->status ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-muted py-5">
                    <i class="fi fi-rr-key fs-1 d-block mb-3"></i>
                    <h6>Aucune permission configurée</h6>
                    <p class="mb-0">Ajoutez d'abord des permissions dans <a href="{{ route('settings.permissions.index') }}">la gestion des permissions</a>.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('change', '.permission-toggle', function () {
        var uuid = $(this).data('uuid');
        var checkbox = $(this);

        $.ajax({
            url: '/settings/role-permissions/' + uuid + '/toggle',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                checkbox.prop('disabled', true);
            },
            success: function (data) {
                checkbox.prop('disabled', false);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                } else {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    SendError(data.message);
                }
            },
            error: function (data) {
                checkbox.prop('disabled', false);
                checkbox.prop('checked', !checkbox.prop('checked'));
                SendError(data.responseJSON.message ?? 'Une erreur est survenue');
            }
        });
    });
</script>
@endpush
