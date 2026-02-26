@php
    $rating = $userCampaign->rating;
    $ratingLevel = $userCampaign->rating_level;
    $ratingColor = $userCampaign->rating_color;
@endphp

{{-- Note globale --}}
@if($rating !== null)
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-4">
            <div class="text-center">
                <div class="score-display text-{{ $ratingColor }}">{{ number_format($rating, 1) }}/100</div>
                <small class="text-muted">Note globale</small>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold text-{{ $ratingColor }}">{{ $ratingLevel }}</span>
                    <span class="fw-bold">{{ number_format($rating, 1) }}/100</span>
                </div>
                <div class="rating-gauge" style="height: 12px;">
                    <div class="gauge-fill bg-{{ $ratingColor }}" style="width: {{ $rating }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-3">
    {{-- Sidebar décisions --}}
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fi fi-rr-time-past me-1"></i> Historique</h6>
            </div>
            <div class="card-body p-3">
                @if($decisions->count() > 0)
                <div class="decision-timeline">
                    @foreach($decisions as $decision)
                    <div class="decision-timeline-item">
                        <div class="timeline-dot bg-{{ $decision->action_color }}"></div>
                        <div>
                            <span class="fw-semibold d-block" style="font-size: 12px;">{{ $decision->action_label }}</span>
                            <small class="text-muted d-block">{{ $decision->actor->full_name }}</small>
                            @if($decision->comment)
                            <small class="d-block mt-1" style="font-size: 12px;">{{ $decision->comment }}</small>
                            @endif
                            <small class="text-muted" style="font-size: 11px;">{{ $decision->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center mb-0" style="font-size: 13px;">Aucun historique.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Liste des objectifs avec notes --}}
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fi fi-rr-bullseye me-1"></i> Mes objectifs évalués ({{ $objectives->count() }})</h6>
            </div>
            <div class="card-body">
                @if($objectives->count() === 0)
                    <div class="text-center text-muted py-4">
                        <i class="fi fi-rr-bullseye fs-1 d-block mb-3"></i>
                        <h6>Aucun objectif</h6>
                    </div>
                @else
                    @foreach($objectives as $obj)
                    @php
                        $hasScore = $obj->score !== null;
                        $noteObtenue = $hasScore ? $obj->score : null;
                    @endphp
                    <div class="obj-eval-card {{ $hasScore ? 'scored' : 'unscored' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-semibold">{{ $obj->title }}</h6>
                                @if($obj->description)
                                <p class="mb-0" style="font-size: 13px; color: var(--bs-secondary-color);">{{ $obj->description }}</p>
                                @endif
                                <div class="d-flex gap-2 mt-1">
                                    @if($hasScore)
                                    <span class="badge bg-primary-subtle text-primary">Note: {{ $obj->score }} / {{ $obj->weight }}</span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary">Non noté</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 11px;">Note obtenue</small>
                                <span class="fw-bold">{{ $noteObtenue !== null ? $noteObtenue . '/' . $obj->weight : '-' }}</span>
                            </div>
                        </div>

                        {{-- Commentaires d'évaluation pour cet objectif --}}
                        <div class="mt-3">
                            <small class="fw-semibold text-muted d-block mb-2"><i class="fi fi-rr-comment-alt me-1"></i> Commentaires d'évaluation ({{ $obj->evaluationComments->count() }})</small>
                            @if($obj->evaluationComments->count() > 0)
                            <div class="ps-2">
                                @foreach($obj->evaluationComments as $comment)
                                <div class="comment-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex gap-2 align-items-start">
                                            <div class="avatar avatar-xs">
                                                <span class="avatar-text rounded-circle bg-primary-subtle text-primary fw-bold" style="font-size:11px">{{ strtoupper(substr($comment->user->first_name, 0, 1) . substr($comment->user->last_name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <span class="fw-semibold d-block" style="font-size:13px">{{ $comment->user->full_name }}</span>
                                                <p class="mb-0" style="font-size:13px">{{ $comment->content }}</p>
                                                <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        </div>
                                        @if($comment->user_uuid === Auth::id())
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" onclick="deleteEvalComment('{{ $comment->uuid }}')" data-bs-toggle="tooltip" title="Supprimer">
                                            <i class="fi fi-rr-trash" style="font-size:11px"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if($userCampaign->evaluation_status === 'submitted_to_employee')
                            <form class="mt-2" onsubmit="addEvalComment(event, '{{ $obj->uuid }}')">
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" id="eval-comment-input-{{ $obj->uuid }}" placeholder="Ajouter un commentaire..." required>
                                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light">
                                        <i class="fi fi-rr-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
