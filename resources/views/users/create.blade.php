@extends('layouts.app')

@section('title', 'Ajouter un Utilisateur')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm waves-effect me-2">
                    <i class="fi fi-rr-arrow-left me-1"></i> Retour
                </a>
                <span class="fw-bold fs-5">Ajouter un utilisateur</span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form class="sendForm" method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Ex: DUPONT" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Ex: Jean" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ex: jean.dupont@axial.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Ex: +225 07 00 00 00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Poste</label>
                            <input type="text" class="form-control" id="position" name="position" placeholder="Ex: Directeur RH">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role_uuid" class="form-label">Rôle</label>
                            <select class="form-select" id="role_uuid" name="role_uuid">
                                <option value="">-- Sélectionner un rôle --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->uuid }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="entity_uuid" class="form-label">Entité</label>
                            <select class="form-select" id="entity_uuid" name="entity_uuid">
                                <option value="">-- Sélectionner une entité --</option>
                                @foreach($entities as $entity)
                                    <option value="{{ $entity->uuid }}">{{ $entity->name }} ({{ ucfirst($entity->category) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Compte actif</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary waves-effect me-2">Annuler</a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="fi fi-rr-check me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
