@extends('layouts.app')

@section('title', 'Mon Évaluation')

@section('styles')
<style>
    .obj-eval-card { border: 1px solid var(--bs-border-color); border-radius: var(--bs-border-radius); padding: 15px; margin-bottom: 12px; transition: all 0.2s; }
    .obj-eval-card.scored { border-left: 3px solid var(--bs-success); }
    .obj-eval-card.unscored { border-left: 3px solid var(--bs-secondary); }
    .decision-timeline { position: relative; padding-left: 28px; }
    .decision-timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background-color: var(--bs-border-color); }
    .decision-timeline-item { position: relative; padding-bottom: 20px; }
    .decision-timeline-item:last-child { padding-bottom: 0; }
    .decision-timeline-item .timeline-dot { position: absolute; left: -24px; top: 4px; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px var(--bs-border-color); }
    .decision-timeline-item .timeline-dot.bg-primary { box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-success { box-shadow: 0 0 0 2px rgba(var(--bs-success-rgb), 0.3); }
    .decision-timeline-item .timeline-dot.bg-warning { box-shadow: 0 0 0 2px rgba(var(--bs-warning-rgb), 0.3); }
    .comment-item { padding: 10px 0; border-bottom: 1px solid var(--bs-border-color); }
    .comment-item:last-child { border-bottom: 0; }
    .score-display { font-size: 2.2rem; font-weight: 700; }
    .rating-gauge { height: 8px; border-radius: 4px; background-color: var(--bs-border-color); overflow: hidden; }
    .rating-gauge .gauge-fill { height: 100%; border-radius: 4px; transition: width 0.5s ease; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="fw-bold fs-5">Mon Évaluation</span>
                @if($campaign)
                    <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }} ms-2">{{ $campaign->status_label }}</span>
                    <small class="text-muted ms-2">{{ $campaign->name }} ({{ $campaign->year }})</small>
                @endif
                @if($userCampaign)
                    <span class="badge bg-{{ $userCampaign->evaluation_status_color }}-subtle text-{{ $userCampaign->evaluation_status_color }} ms-2">{{ $userCampaign->evaluation_status_label }}</span>
                @endif
            </div>
            @if($userCampaign && $userCampaign->evaluation_status === 'submitted_to_employee')
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" onclick="returnToSupervisor()">
                    <i class="fi fi-rr-undo me-1"></i> Retourner au supérieur
                </button>
                <button type="button" class="btn btn-success btn-sm waves-effect waves-light" onclick="validateEvaluation()">
                    <i class="fi fi-rr-check-circle me-1"></i> Valider l'évaluation
                </button>
            </div>
            @endif
        </div>

        @if(!$campaign)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-chart-histogram fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Aucune campagne en phase d'évaluation</h5>
                    <p class="text-muted mb-0">Vous serez notifié lorsque la phase d'évaluation sera démarrée.</p>
                </div>
            </div>
        @elseif(!$userCampaign)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-user-slash fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="text-muted">Vous n'êtes pas participant à cette campagne</h5>
                </div>
            </div>
        @elseif($userCampaign->evaluation_status === 'pending')
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-clock fs-1 d-block mb-3 text-warning"></i>
                    <h5>Évaluation en attente</h5>
                    <p class="text-muted mb-0">Votre supérieur n'a pas encore soumis votre évaluation. Vous serez notifié lorsqu'elle sera disponible.</p>
                </div>
            </div>
        @elseif($userCampaign->evaluation_status === 'supervisor_draft')
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fi fi-rr-pencil fs-1 d-block mb-3 text-primary"></i>
                    <h5>Évaluation en cours de rédaction</h5>
                    <p class="text-muted mb-0">Votre supérieur est en train de préparer votre évaluation.</p>
                </div>
            </div>
        @elseif($userCampaign->evaluation_status === 'returned_to_supervisor')
            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-undo me-2"></i>
                <div>Vous avez retourné l'évaluation à votre supérieur. En attente de sa réponse.</div>
            </div>
            @include('evaluations._content')
        @else
            @if($userCampaign->evaluation_status === 'submitted_to_employee')
            <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-bell me-2"></i>
                <div>Votre supérieur a soumis votre évaluation. Consultez vos notes, ajoutez des commentaires, puis validez ou retournez.</div>
            </div>
            @elseif($userCampaign->evaluation_status === 'validated')
            <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                <i class="fi fi-rr-check-circle me-2"></i>
                <div>Votre évaluation a été validée. La campagne est terminée pour vous.</div>
            </div>
            @endif
            @include('evaluations._content')
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let userCampaignUuid = '{{ $userCampaign->uuid ?? '' }}';
    let evalStatus = '{{ $userCampaign->evaluation_status ?? '' }}';
    let canComment = evalStatus === 'submitted_to_employee';

    function returnToSupervisor() {
        Swal.fire({
            icon: 'warning', title: 'Retourner l\'évaluation ?',
            text: 'Votre supérieur pourra modifier les notes et vous la resoumettre.',
            input: 'textarea', inputLabel: 'Commentaire (optionnel)', inputPlaceholder: 'Expliquez pourquoi vous retournez l\'évaluation...',
            showDenyButton: true, confirmButtonText: 'Retourner', denyButtonText: 'Annuler',
            confirmButtonColor: '#ffc107',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/evaluations/' + userCampaignUuid + '/return',
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

    function validateEvaluation() {
        Swal.fire({
            icon: 'question', title: 'Valider l\'évaluation ?',
            html: 'En validant, vous acceptez les notes attribuées et mettez fin à votre campagne.<br><strong>Cette action est irréversible.</strong>',
            input: 'textarea', inputLabel: 'Commentaire (optionnel)', inputPlaceholder: 'Ajouter un commentaire final...',
            showDenyButton: true, confirmButtonText: 'Valider définitivement', denyButtonText: 'Annuler',
            confirmButtonColor: '#198754',
        }).then((result) => {
            if (result.isConfirmed) {
                loader();
                $.ajax({
                    url: '/evaluations/' + userCampaignUuid + '/validate',
                    type: 'POST',
                    data: { comment: result.value || '' },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        loader('hide');
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Évaluation validée !', text: data.message, showConfirmButton: false, timer: 2000 });
                            setTimeout(() => location.reload(), 2000);
                        } else { SendError(data.message); }
                    },
                    error: function(data) { loader('hide'); SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }

    function addEvalComment(e, objUuid) {
        e.preventDefault();
        let input = document.getElementById('eval-comment-input-' + objUuid);
        let content = input.value;
        if (!content) return;
        $.ajax({
            url: '/evaluations/' + objUuid + '/comments',
            type: 'POST',
            data: { content: content },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(data) {
                if (data.success) {
                    input.value = '';
                    location.reload();
                } else { SendError(data.message); }
            },
            error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
        });
    }

    function deleteEvalComment(commentUuid) {
        Swal.fire({
            icon: 'warning', title: 'Supprimer ce commentaire ?',
            showDenyButton: true, confirmButtonText: 'Oui', denyButtonText: 'Non',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/evaluation-comments/' + commentUuid,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(data) {
                        if (data.success) { location.reload(); }
                        else { SendError(data.message); }
                    },
                    error: function(data) { SendError(data.responseJSON?.message ?? 'Une erreur est survenue'); }
                });
            }
        });
    }
</script>
@endpush
