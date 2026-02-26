<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Mi-parcours - {{ $userName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0dcaf0; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #0dcaf0; margin-bottom: 5px; }
        .header p { font-size: 12px; color: #666; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 8px; font-size: 12px; }
        .info-table .label { font-weight: bold; color: #555; width: 150px; }
        table.objectives { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.objectives th { background-color: #0dcaf0; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; }
        table.objectives td { padding: 8px 10px; border-bottom: 1px solid #dee2e6; font-size: 12px; vertical-align: top; }
        table.objectives tr:nth-child(even) { background-color: #f8f9fa; }
        .section-title { font-size: 14px; font-weight: bold; color: #0dcaf0; margin: 25px 0 10px; border-bottom: 1px solid #0dcaf0; padding-bottom: 5px; }
        .comment-box { border: 1px solid #dee2e6; border-radius: 6px; padding: 15px; min-height: 80px; margin-bottom: 15px; background: #fafafa; }
        .comment-box.empty { color: #999; font-style: italic; }
        .history-item { font-size: 11px; color: #666; margin-bottom: 3px; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #dee2e6; padding-top: 10px; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; background: #0dcaf0; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            Imprimer / Enregistrer en PDF
        </button>
    </div>

    <div class="header">
        <h1>FICHE D'ÉVALUATION MI-PARCOURS</h1>
        <p>{{ $campaign->name }} — {{ $campaign->year }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Collaborateur :</td>
            <td>{{ $userName }}</td>
            <td class="label">Poste :</td>
            <td>{{ $userCampaign->user->position ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Entité :</td>
            <td>{{ $userCampaign->user->entity->name ?? '-' }}</td>
            <td class="label">Supérieur :</td>
            <td>{{ $userCampaign->supervisor->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Date :</td>
            <td>{{ now()->format('d/m/Y') }}</td>
            <td class="label">Période :</td>
            <td>{{ $campaign->midterm_starts_at ? $campaign->midterm_starts_at->format('d/m/Y') : '' }} — {{ $campaign->midterm_stops_at ? $campaign->midterm_stops_at->format('d/m/Y') : '' }}</td>
        </tr>
    </table>

    <div class="section-title">Objectifs et Réalisations</div>

    <table class="objectives">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Catégorie</th>
                <th style="width: 30%;">Objectif</th>
                <th style="width: 10%; text-align: center;">Pond.</th>
                <th style="width: 35%;">Réalisations / Observations</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach($objectives as $obj)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $obj->category->name ?? '-' }}</td>
                <td>
                    <strong>{{ $obj->title }}</strong>
                    @if($obj->description)
                    <br><small style="color: #666;">{{ $obj->description }}</small>
                    @endif
                </td>
                <td style="text-align: center;">{{ $obj->weight }}%</td>
                <td>
                    @if($obj->histories->count() > 0)
                        @foreach($obj->histories->where('phase', 'midterm') as $h)
                        <div class="history-item">
                            <strong>{{ ucfirst($h->field) }}:</strong> {{ $h->old_value }} → {{ $h->new_value }}
                            <br><small>par {{ $h->changedBy->full_name ?? '' }} le {{ $h->created_at->format('d/m/Y') }}</small>
                        </div>
                        @endforeach
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Commentaires</div>

    <p style="font-weight: bold; margin-bottom: 5px;">Commentaire du supérieur :</p>
    <div class="comment-box {{ !$userCampaign->supervisor_comment ? 'empty' : '' }}">
        {{ $userCampaign->supervisor_comment ?? 'Aucun commentaire' }}
    </div>

    <p style="font-weight: bold; margin-bottom: 5px;">Commentaire du collaborateur :</p>
    <div class="comment-box {{ !$userCampaign->employee_comment ? 'empty' : '' }}">
        {{ $userCampaign->employee_comment ?? 'Aucun commentaire' }}
    </div>

    <div style="margin-top: 50px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center; padding-top: 40px; border-top: 1px solid #333;">
                    <strong>Signature du collaborateur</strong>
                </td>
                <td style="width: 50%; text-align: center; padding-top: 40px; border-top: 1px solid #333;">
                    <strong>Signature du supérieur</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Généré automatiquement par AXIAL — Plateforme de Gestion des Objectifs & Évaluations
    </div>
</body>
</html>
