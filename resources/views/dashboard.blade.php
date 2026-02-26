@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('styles')
<style>
    .stat-card { border-radius: var(--bs-border-radius-lg); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .spinner-section { display: flex; align-items: center; justify-content: center; min-height: 120px; }
    .spinner-section .spinner-border { width: 2rem; height: 2rem; }
    .campaign-phase { padding: 10px 14px; border-radius: 8px; }
    .sub-table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--bs-secondary-color); }
</style>
@endsection

@section('content')
{{-- Bienvenue --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1" id="welcomeTitle">Bienvenue, {{ Auth::user()->first_name }} !</h4>
        <p class="text-muted mb-0">Voici un résumé de votre activité sur la plateforme AXIAL.</p>
    </div>
    <small class="text-muted" id="dashDate"></small>
</div>

{{-- Ligne 1: Mini-cards stats --}}
<div class="row g-3 mb-4" id="statsRow">
    {{-- Card: Campagnes actives --}}
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size:13px;">Campagnes actives</p>
                        <div class="spinner-section" id="spinCampaigns"><div class="spinner-border text-primary" role="status"></div></div>
                        <h3 class="fw-bold mb-0 d-none" id="valCampaigns"></h3>
                        <small class="text-muted d-none" id="valCampaignsTotal"></small>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary"><i class="fi fi-rr-folder"></i></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Card: Mes Objectifs --}}
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size:13px;">Mes Objectifs</p>
                        <div class="spinner-section" id="spinObjectives"><div class="spinner-border text-success" role="status"></div></div>
                        <h3 class="fw-bold mb-0 d-none" id="valObjectives"></h3>
                        <small class="text-muted d-none" id="valObjectivesCampaign"></small>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success"><i class="fi fi-rr-bullseye"></i></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Card: Ma Note --}}
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size:13px;">Ma Note</p>
                        <div class="spinner-section" id="spinRating"><div class="spinner-border text-warning" role="status"></div></div>
                        <h3 class="fw-bold mb-0 d-none" id="valRating"></h3>
                        <small class="d-none" id="valRatingStatus"></small>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="fi fi-rr-chart-histogram"></i></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Card: Collaborateurs à évaluer --}}
    <div class="col-md-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1" style="font-size:13px;">À évaluer</p>
                        <div class="spinner-section" id="spinSubEval"><div class="spinner-border text-info" role="status"></div></div>
                        <h3 class="fw-bold mb-0 d-none" id="valSubEval"></h3>
                        <small class="text-muted d-none" id="valSubEvalTotal"></small>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info"><i class="fi fi-rr-users"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ligne 2: Campagne en cours + Mon évaluation --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="fi fi-rr-flag me-1"></i> Campagne en cours</h6>
                <a href="/campaigns" class="btn btn-outline-primary btn-sm waves-effect" id="linkCampaign" style="display:none;">Voir <i class="fi fi-rr-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body">
                <div class="spinner-section" id="spinCampaign"><div class="spinner-border text-primary" role="status"></div></div>
                <div class="d-none" id="campaignContent"></div>
                <div class="d-none text-center text-muted py-4" id="campaignEmpty">
                    <i class="fi fi-rr-flag fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">Aucune campagne active pour le moment.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fi fi-rr-chart-pie me-1"></i> Répartition des notes</h6>
            </div>
            <div class="card-body">
                <div class="spinner-section" id="spinChart"><div class="spinner-border text-primary" role="status"></div></div>
                <div class="d-none" id="chartContainer">
                    <div id="ratingChart"></div>
                </div>
                <div class="d-none text-center text-muted py-4" id="chartEmpty">
                    <i class="fi fi-rr-chart-histogram fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">Aucune note disponible.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Ligne 3: Collaborateurs + Dernières campagnes --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="fi fi-rr-users me-1"></i> Mes Collaborateurs</h6>
                <a href="/supervisor/evaluations" class="btn btn-outline-primary btn-sm waves-effect" id="linkSubordinates" style="display:none;">Évaluer <i class="fi fi-rr-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="spinner-section p-4" id="spinSub"><div class="spinner-border text-primary" role="status"></div></div>
                <div class="d-none" id="subContent"></div>
                <div class="d-none text-center text-muted py-4" id="subEmpty">
                    <i class="fi fi-rr-users fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">Aucun collaborateur à afficher.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fi fi-rr-time-past me-1"></i> Dernières campagnes</h6>
            </div>
            <div class="card-body p-0">
                <div class="spinner-section p-4" id="spinRecent"><div class="spinner-border text-primary" role="status"></div></div>
                <div class="d-none" id="recentContent"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
$(function() {
    let now = new Date();
    $('#dashDate').text(now.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));

    let bsColorMap = { danger: '#dc3545', warning: '#ffc107', info: '#0dcaf0', primary: '#0d6efd', success: '#198754', secondary: '#6c757d', dark: '#212529' };

    $.get('{{ route("dashboard.data") }}', function(data) {
        // ===== STATS =====
        let s = data.stats;

        // Campagnes actives
        $('#spinCampaigns').addClass('d-none');
        $('#valCampaigns').text(s.active_campaigns).removeClass('d-none');
        $('#valCampaignsTotal').text('sur ' + s.total_campaigns + ' campagne(s)').removeClass('d-none');

        // Mes objectifs
        $('#spinObjectives').addClass('d-none');
        $('#valObjectives').text(s.my_objectives).removeClass('d-none');
        if (s.my_campaign_name) {
            $('#valObjectivesCampaign').text(s.my_campaign_name).removeClass('d-none');
        } else {
            $('#valObjectivesCampaign').text('Aucune campagne').removeClass('d-none');
        }

        // Ma note
        $('#spinRating').addClass('d-none');
        if (s.my_rating !== null) {
            $('#valRating').text(s.my_rating + '%').removeClass('d-none');
            $('#valRatingStatus').html('<span class="badge bg-' + s.my_eval_status_color + '-subtle text-' + s.my_eval_status_color + '">' + s.my_eval_status_label + '</span>').removeClass('d-none');
        } else {
            $('#valRating').text('-').removeClass('d-none');
            if (s.my_eval_status_label) {
                $('#valRatingStatus').html('<span class="badge bg-' + s.my_eval_status_color + '-subtle text-' + s.my_eval_status_color + '">' + s.my_eval_status_label + '</span>').removeClass('d-none');
            } else {
                $('#valRatingStatus').text('Pas d\'évaluation').addClass('text-muted').removeClass('d-none');
            }
        }

        // À évaluer
        $('#spinSubEval').addClass('d-none');
        $('#valSubEval').text(s.subordinates_to_evaluate).removeClass('d-none');
        $('#valSubEvalTotal').text('sur ' + s.total_subordinates + ' collaborateur(s)').removeClass('d-none');

        // ===== CAMPAGNE EN COURS =====
        $('#spinCampaign').addClass('d-none');
        let c = data.current_campaign;
        if (c) {
            $('#linkCampaign').attr('href', '/campaigns/' + c.uuid).show();
            let objPct = c.total_participants > 0 ? Math.round(c.objectives_completed / c.total_participants * 100) : 0;
            let evalPct = c.total_participants > 0 ? Math.round(c.evals_validated / c.total_participants * 100) : 0;

            let html = `
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">${c.name} <small class="text-muted fw-normal">(${c.year})</small></h5>
                    </div>
                    <span class="badge bg-${c.status_color}-subtle text-${c.status_color}">${c.status_label}</span>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="campaign-phase bg-primary-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-primary" style="font-size:13px;"><i class="fi fi-rr-bullseye me-1"></i> Phase Objectifs</span>
                                <span class="fw-bold text-primary">${c.objectives_completed}/${c.total_participants}</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary" style="width:${objPct}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                                <span class="text-muted">${c.objective_starts_at || '-'}</span>
                                <span class="text-muted">${c.objective_stops_at || '-'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="campaign-phase bg-warning-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-warning" style="font-size:13px;"><i class="fi fi-rr-chart-histogram me-1"></i> Phase Évaluation</span>
                                <span class="fw-bold text-warning">${c.evals_validated}/${c.total_participants}</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-warning" style="width:${evalPct}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                                <span class="text-muted">${c.evaluation_starts_at || '-'}</span>
                                <span class="text-muted">${c.evaluation_stops_at || '-'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-center">
                        <div class="fs-5 fw-bold">${c.total_participants}</div>
                        <small class="text-muted">Participants</small>
                    </div>
                    <div class="text-center">
                        <div class="fs-5 fw-bold text-primary">${objPct}%</div>
                        <small class="text-muted">Obj. complétés</small>
                    </div>
                    <div class="text-center">
                        <div class="fs-5 fw-bold text-warning">${evalPct}%</div>
                        <small class="text-muted">Éval. validées</small>
                    </div>
                </div>`;
            $('#campaignContent').html(html).removeClass('d-none');
        } else {
            $('#campaignEmpty').removeClass('d-none');
        }

        // ===== GRAPHIQUE RÉPARTITION DES NOTES =====
        $('#spinChart').addClass('d-none');
        let rd = data.rating_distribution;
        let rdValues = Object.values(rd);
        let hasRatings = rdValues.some(v => v > 0);
        if (hasRatings) {
            $('#chartContainer').removeClass('d-none');
            new ApexCharts(document.querySelector('#ratingChart'), {
                series: [{ name: 'Participants', data: rdValues }],
                chart: { type: 'bar', height: 250, toolbar: { show: false } },
                plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
                colors: [bsColorMap.danger, bsColorMap.warning, bsColorMap.info, bsColorMap.primary, bsColorMap.success],
                xaxis: { categories: Object.keys(rd), labels: { style: { fontSize: '11px' } } },
                yaxis: { stepSize: 1, labels: { style: { fontSize: '11px' } } },
                legend: { show: false },
                dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
                tooltip: { y: { formatter: function(val) { return val + ' participant(s)'; } } }
            }).render();
        } else {
            $('#chartEmpty').removeClass('d-none');
        }

        // ===== COLLABORATEURS =====
        $('#spinSub').addClass('d-none');
        let subs = data.subordinates;
        if (subs.length > 0) {
            $('#linkSubordinates').show();
            let thtml = `<div class="table-responsive"><table class="table table-hover align-middle mb-0 sub-table">
                <thead class="table-light"><tr>
                    <th>Collaborateur</th><th>Objectifs</th><th>Évaluation</th><th>Note</th>
                </tr></thead><tbody>`;
            subs.forEach(function(sub) {
                let ratingHtml = sub.rating !== null
                    ? `<span class="fw-bold text-${sub.rating_color}">${sub.rating}%</span>`
                    : '<span class="text-muted">-</span>';
                thtml += `<tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm">
                                <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold">${sub.initials}</span>
                            </div>
                            <div>
                                <span class="fw-semibold d-block">${sub.full_name}</span>
                                <small class="text-muted">${sub.position || ''}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-${sub.objective_status_color}-subtle text-${sub.objective_status_color}">${sub.objective_status_label}</span></td>
                    <td><span class="badge bg-${sub.evaluation_status_color}-subtle text-${sub.evaluation_status_color}">${sub.evaluation_status_label}</span></td>
                    <td>${ratingHtml}</td>
                </tr>`;
            });
            thtml += '</tbody></table></div>';
            $('#subContent').html(thtml).removeClass('d-none');
        } else {
            $('#subEmpty').removeClass('d-none');
        }

        // ===== DERNIÈRES CAMPAGNES =====
        $('#spinRecent').addClass('d-none');
        let rc = data.recent_campaigns;
        if (rc.length > 0) {
            let rhtml = '<ul class="list-group list-group-flush">';
            rc.forEach(function(camp) {
                rhtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="/campaigns/${camp.uuid}" class="fw-semibold text-body text-decoration-none">${camp.name}</a>
                        <small class="text-muted d-block">${camp.year}</small>
                    </div>
                    <span class="badge bg-${camp.status_color}-subtle text-${camp.status_color}">${camp.status_label}</span>
                </li>`;
            });
            rhtml += '</ul>';
            $('#recentContent').html(rhtml).removeClass('d-none');
        }

    }).fail(function() {
        // En cas d'erreur, masquer tous les spinners
        $('.spinner-section').html('<span class="text-danger"><i class="fi fi-rr-exclamation me-1"></i> Erreur de chargement</span>');
    });
});
</script>
@endpush
