@extends('layouts.app')

@section('title', 'Résultats - ' . $campaign->name)

@section('styles')
<style>
    .podium-card {
        border-radius: var(--bs-border-radius-lg);
        text-align: center;
        transition: transform 0.2s;
    }
    .podium-card:hover {
        transform: translateY(-4px);
    }
    .podium-rank {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
    }
    .podium-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
    }
    .entity-avg-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
    }
    .mini-donut-cell {
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('campaigns.show', $campaign->uuid) }}" class="btn btn-outline-secondary btn-sm waves-effect me-2">
                    <i class="fi fi-rr-arrow-left me-1"></i> Retour
                </a>
                <span class="fw-bold fs-5">Résultats — {{ $campaign->name }} ({{ $campaign->year }})</span>
                <span class="badge bg-{{ $campaign->status_color }}-subtle text-{{ $campaign->status_color }} ms-2">{{ $campaign->status_label }}</span>
            </div>
        </div>

        {{-- ===== SCORE GLOBAL (Donut) + PODIUM ===== --}}
        <div class="row g-3 mb-4">
            {{-- Score Global avec camembert --}}
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><i class="fi fi-rr-chart-pie me-1"></i> Score global</h6>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div id="globalDonut"></div>
                        <div class="text-center mt-2">
                            @php
                                $level = match(true) {
                                    $globalScore < 20 => 'Insuffisant',
                                    $globalScore < 40 => 'Passable',
                                    $globalScore < 60 => 'Satisfaisant',
                                    $globalScore < 80 => 'Bien',
                                    default => 'Excellent',
                                };
                                $globalColor = match(true) {
                                    $globalScore < 20 => 'danger',
                                    $globalScore < 40 => 'warning',
                                    $globalScore < 60 => 'info',
                                    $globalScore < 80 => 'primary',
                                    default => 'success',
                                };
                            @endphp
                            <span class="badge bg-{{ $globalColor }}-subtle text-{{ $globalColor }} fs-6">{{ $level }}</span>
                        </div>
                        <hr class="w-100">
                        <div class="d-flex justify-content-around w-100 text-center">
                            <div>
                                <div class="fs-5 fw-bold">{{ $totalParticipants }}</div>
                                <small class="text-muted">Participants</small>
                            </div>
                            <div>
                                <div class="fs-5 fw-bold">{{ $validatedCount }}</div>
                                <small class="text-muted">Validés</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Podium: Top 3 --}}
            @foreach($podium as $index => $uc)
            @php
                $rankColors = ['warning', 'secondary', 'info'];
                $rankLabels = ['1er', '2e', '3e'];
                $color = $rankColors[$index] ?? 'secondary';
                $initials = strtoupper(substr($uc->user->first_name, 0, 1) . substr($uc->user->last_name, 0, 1));
                $ratingVal = $uc->rating ?? 0;
            @endphp
            <div class="col-md-{{ $podium->count() === 1 ? '8' : ($podium->count() === 2 ? '4' : '4') }}">
                <div class="card podium-card h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="podium-rank bg-{{ $color }}-subtle text-{{ $color }}">{{ $rankLabels[$index] }}</span>
                        </div>
                        <div class="podium-avatar bg-{{ $uc->rating_color }}-subtle text-{{ $uc->rating_color }} mx-auto mb-3">
                            {{ $initials }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ $uc->user->full_name }}</h6>
                        <small class="text-muted d-block mb-1">{{ $uc->user->position ?? '' }}</small>
                        @if($uc->user->entity)
                        <small class="text-muted d-block mb-2">{{ $uc->user->entity->name }}</small>
                        @endif
                        <div class="display-6 fw-bold text-{{ $uc->rating_color }} mb-1">{{ $ratingVal }}%</div>
                        <span class="badge bg-{{ $uc->rating_color }}-subtle text-{{ $uc->rating_color }}">{{ $uc->rating_level ?? '-' }}</span>
                    </div>
                </div>
            </div>
            @endforeach

            @if($podium->count() === 0)
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center justify-content-center text-muted py-5">
                        <div class="text-center">
                            <i class="fi fi-rr-trophy fs-1 d-block mb-2"></i>
                            <h6>Aucun résultat disponible</h6>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- ===== RÉSULTATS PAR ENTITÉ ===== --}}
        @foreach($entitiesWithParticipants as $eIdx => $entityData)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="fi fi-rr-building me-1"></i> {{ $entityData['entity']->name }}
                    <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $entityData['members']->count() }} membre(s)</span>
                </h6>
                <span class="badge entity-avg-badge bg-primary-subtle text-primary">
                    Moyenne: {{ $entityData['avg_rating'] }}%
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Collaborateur</th>
                                <th>Supérieur</th>
                                <th style="width: 140px;">Note</th>
                                <th style="width: 120px;">Appréciation</th>
                                <th style="width: 120px;">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entityData['members'] as $idx => $uc)
                            @php
                                $initials = strtoupper(substr($uc->user->first_name, 0, 1) . substr($uc->user->last_name, 0, 1));
                                $ratingVal = $uc->rating ?? 0;
                                $chartId = 'miniDonut_' . $eIdx . '_' . $idx;
                            @endphp
                            <tr>
                                <td class="fw-semibold text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-text rounded-circle bg-{{ $uc->rating_color }}-subtle text-{{ $uc->rating_color }} fw-bold">{{ $initials }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block">{{ $uc->user->full_name }}</span>
                                            <small class="text-muted">{{ $uc->user->position ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($uc->supervisor)
                                        <small>{{ $uc->supervisor->full_name }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="mini-donut-cell">
                                        <div id="{{ $chartId }}"></div>
                                        <span class="fw-bold">{{ $ratingVal }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $uc->rating_color }}-subtle text-{{ $uc->rating_color }}">{{ $uc->rating_level ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $uc->evaluation_status_color }}-subtle text-{{ $uc->evaluation_status_color }}">{{ $uc->evaluation_status_label }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        @if(count($entitiesWithParticipants) === 0)
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fi fi-rr-chart-histogram fs-1 d-block mb-3"></i>
                <h6>Aucun résultat à afficher</h6>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let bsColors = {
        danger:  '#dc3545',
        warning: '#ffc107',
        info:    '#0dcaf0',
        primary: '#0d6efd',
        success: '#198754',
    };

    function ratingColor(val) {
        if (val < 20) return bsColors.danger;
        if (val < 40) return bsColors.warning;
        if (val < 60) return bsColors.info;
        if (val < 80) return bsColors.primary;
        return bsColors.success;
    }

    // Score global donut
    let globalScore = {{ $globalScore }};
    let globalChartColor = ratingColor(globalScore);
    new ApexCharts(document.querySelector('#globalDonut'), {
        series: [globalScore],
        chart: { type: 'radialBar', height: 220 },
        plotOptions: {
            radialBar: {
                hollow: { size: '60%' },
                dataLabels: {
                    name: { show: true, fontSize: '13px', color: '#6c757d', offsetY: -8 },
                    value: { show: true, fontSize: '28px', fontWeight: 700, color: globalChartColor, offsetY: 4, formatter: function(val) { return val + '%'; } }
                },
                track: { background: '#e9ecef', strokeWidth: '100%' }
            }
        },
        fill: { colors: [globalChartColor] },
        stroke: { lineCap: 'round' },
        labels: ['Score global']
    }).render();

    // Mini donuts par participant
    @php
        $miniDonutData = [];
        foreach ($entitiesWithParticipants as $eIdx => $entityData) {
            foreach ($entityData['members'] as $idx => $uc) {
                $miniDonutData[] = ['id' => 'miniDonut_' . $eIdx . '_' . $idx, 'val' => $uc->rating ?? 0];
            }
        }
    @endphp
    let miniDonuts = @json($miniDonutData);

    miniDonuts.forEach(function(item) {
        let el = document.querySelector('#' + item.id);
        if (!el) return;
        let c = ratingColor(item.val);
        new ApexCharts(el, {
            series: [item.val],
            chart: { type: 'radialBar', height: 45, width: 45, sparkline: { enabled: true } },
            plotOptions: {
                radialBar: {
                    hollow: { size: '40%' },
                    dataLabels: { name: { show: false }, value: { show: false } },
                    track: { background: '#e9ecef', strokeWidth: '100%' }
                }
            },
            fill: { colors: [c] },
            stroke: { lineCap: 'round' }
        }).render();
    });
});
</script>
@endpush
