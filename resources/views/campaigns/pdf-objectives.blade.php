<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche des Objectifs - {{ $userName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; color: #333; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0d6efd; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #0d6efd; margin-bottom: 5px; }
        .header p { font-size: 12px; color: #666; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 4px 8px; font-size: 12px; }
        .info-table .label { font-weight: bold; color: #555; width: 150px; }
        table.objectives { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.objectives th { background-color: #0d6efd; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.objectives td { padding: 8px 10px; border-bottom: 1px solid #dee2e6; font-size: 12px; vertical-align: top; }
        table.objectives tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #dee2e6; padding-top: 10px; }
        .total-row { font-weight: bold; background-color: #e8f0fe !important; }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; background: #0d6efd; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            <i class="fi fi-rr-print"></i> Imprimer / Enregistrer en PDF
        </button>
    </div>

    <div class="header">
        <h1>FICHE DES OBJECTIFS</h1>
        <p>{{ $campaignName }} — {{ $year }}</p>
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
            <td class="label">Date :</td>
            <td>{{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="objectives">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Catégorie</th>
                <th style="width: 50%;">Objectif</th>
                <th style="width: 20%; text-align: center;">Pondération</th>
            </tr>
        </thead>
        <tbody>
            @php $totalWeight = 0; $i = 1; @endphp
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
            </tr>
            @php $totalWeight += $obj->weight; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 20px;">Total Pondération</td>
                <td style="text-align: center;">{{ $totalWeight }}%</td>
            </tr>
        </tbody>
    </table>

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
