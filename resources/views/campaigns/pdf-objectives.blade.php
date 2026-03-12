<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport individuel de gestion de la performance {{ $year }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; padding: 20px; }
        .header { margin-bottom: 20px; }
        .header h1 { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 15px; }
        .info-section { margin-bottom: 20px; font-size: 11px; line-height: 1.6; }
        .info-section p { margin: 3px 0; }
        .section-title { font-weight: bold; font-size: 12px; margin-top: 25px; margin-bottom: 10px; text-decoration: underline; }
        table.objectives { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.objectives th, table.objectives td { border: 1px solid #000; padding: 6px 8px; text-align: left; font-size: 10px; }
        table.objectives th { background-color: #fff; font-weight: bold; }
        table.objectives td { vertical-align: top; }
        .total-row { font-weight: bold; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; background: #0d6efd; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            Imprimer / Enregistrer en PDF
        </button>
    </div>

    <div class="header">
        <h1>Rapport individuel de gestion de la performance {{ $year }}</h1>
    </div>

    <div class="info-section">
        <p><strong>Nom :</strong> {{ strtoupper(explode(' ', $userName)[count(explode(' ', $userName)) - 1]) }}</p>
        <p><strong>Prénoms :</strong> {{ implode(' ', array_slice(explode(' ', $userName), 0, -1)) }}</p>
        <p><strong>Fonction :</strong> {{ $userCampaign->user->position ?? '-' }}</p>
        <p><strong>Date d'embauche :</strong> {{ $userCampaign->user->hire_date ? \Carbon\Carbon::parse($userCampaign->user->hire_date)->format('d/m/Y') : '-' }}</p>
        <p><strong>Direction / Service :</strong> {{ $userCampaign->user->entity->name ?? '-' }}</p>
        <p><strong>Étape de la campagne :</strong> {{ $campaign->status_label }}</p>
        <p><strong>Nom de l'évaluateur :</strong> {{ $userCampaign->supervisor->full_name ?? '-' }}</p>
    </div>

    @foreach($objectivesByCategory as $categoryUuid => $categoryObjectives)
        @php
            $firstObjective = $categoryObjectives->first();
            $categoryName = $firstObjective->category->name ?? 'Autres objectifs';
            $categoryAbbr = 'OI';
            
            if (stripos($categoryName, 'collectif') !== false) {
                $categoryAbbr = 'OC';
            } elseif (stripos($categoryName, 'développement') !== false || stripos($categoryName, 'comportemental') !== false) {
                $categoryAbbr = 'OD';
            }
        @endphp
        
        <div class="section-title">{{ $categoryName }}</div>
        <table class="objectives">
            <thead>
                <tr>
                    <th style="width: 8%;"></th>
                    <th style="width: 37%;">Description de l'objectif</th>
                    <th style="width: 11%; text-align: center;">Poids (P)</th>
                    <th style="width: 22%;">Commentaires de l'évalué</th>
                    <th style="width: 22%;">Commentaires de l'évaluateur</th>
                    <th style="width: 0%;">Note</th>
                </tr>
            </thead>
            <tbody>
                @php $categoryTotal = 0; $index = 1; @endphp
                @foreach($categoryObjectives as $obj)
                <tr>
                    <td>{{ $categoryAbbr }} {{ $index++ }}</td>
                    <td>
                        <strong>{{ $obj->title }}</strong>
                        @if($obj->description)
                        <br><span style="font-size: 9px; color: #333;">{{ $obj->description }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $obj->weight }}%</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @php $categoryTotal += $obj->weight; @endphp
                @endforeach
                <tr class="total-row">
                    <td></td>
                    <td>Total</td>
                    <td style="text-align: center;">{{ $categoryTotal }}%</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endforeach

    @if(!in_array($campaign->status, ['draft', 'objective_in_progress']))
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <div class="section-title">Score individuel : 100/100 - Répond à toutes les attentes</div>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <td style="border: 1px solid #000; padding: 8px; width: 25%; vertical-align: top; font-weight: bold; font-size: 11px;">
                    Commentaire général de l'évaluateur
                </td>
                <td style="border: 1px solid #000; padding: 8px; width: 75%; vertical-align: top; font-size: 10px;">
                    {{ $userCampaign->supervisor_comment ?? 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX' }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 8px; width: 25%; vertical-align: top; font-weight: bold; font-size: 11px;">
                    Commentaire général de l'évalué
                </td>
                <td style="border: 1px solid #000; padding: 8px; width: 75%; vertical-align: top; font-size: 10px;">
                    {{ $userCampaign->employee_comment ?? 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX' }}
                </td>
            </tr>
        </table>
    </div>
    @endif
</body>
</html>
