<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $menuPlan->name }} - PDF</title>
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        h2 { font-size: 15px; margin: 0 0 8px; }
        h3 { font-size: 12px; margin: 0 0 8px; }
        .meta { color: #4b5563; margin-bottom: 10px; }
        .note { background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; margin: 10px 0 14px; }
        .day-page { page-break-after: always; }
        .day-page:last-child { page-break-after: auto; }
        .meal-box { border: 1px solid #e5e7eb; border-radius: 6px; margin: 10px 0; padding: 8px; }
        .meal-title { font-weight: 700; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 5px; vertical-align: top; }
        th { background: #f9fafb; text-align: left; font-size: 10px; text-transform: uppercase; color: #374151; }
        .text-right { text-align: right; }
        .totals-row td { background: #f3f4f6; font-weight: 700; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
        .summary-card { border: 1px solid #d1d5db; border-radius: 6px; padding: 8px; }
        .summary-card .label { color: #6b7280; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
        .summary-card .value { font-weight: 700; }
        .grand { border-color: #111827; }
    </style>
</head>
<body>
    <h1>{{ $menuPlan->name }}</h1>
    <div class="meta">
        Pacient: {{ $menuPlan->patient->full_name }}<br>
        @if($menuPlan->starts_at || $menuPlan->ends_at)
            Perioada:
            {{ $menuPlan->starts_at?->format('d.m.Y') ?? '—' }}
            -
            {{ $menuPlan->ends_at?->format('d.m.Y') ?? '—' }}
        @endif
    </div>

    <div class="note">
        Pentru salvare PDF: deschide print (`Ctrl/Cmd + P`) si selecteaza `Save as PDF`.
    </div>

    @foreach($daysForExport as $dayExport)
        @php($day = $dayExport['day'])
        <section class="day-page">
            <h2>{{ $day->name }}</h2>

            @foreach($dayExport['rows'] as $mealRow)
                @php($meal = $mealRow['meal'])
                <div class="meal-box">
                    <div class="meal-title">{{ $meal->display_name }}</div>

                    @if(count($mealRow['items']) > 0)
                        <table>
                            <thead>
                            <tr>
                                <th>Produs / Mancare</th>
                                <th>Portie</th>
                                <th class="text-right">Kcal</th>
                                <th class="text-right">Prot (g)</th>
                                <th class="text-right">Gras (g)</th>
                                <th class="text-right">Carb (g)</th>
                                <th class="text-right">Na (mg)</th>
                                <th class="text-right">K (mg)</th>
                                <th class="text-right">P (mg)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mealRow['items'] as $itemRow)
                                @php($n = $itemRow['nutrients'])
                                <tr>
                                    <td>{{ $itemRow['item']->name }}</td>
                                    <td>{{ number_format((float)$itemRow['item']->portion_qty, 1) }} {{ $itemRow['item']->portion_unit }}</td>
                                    <td class="text-right">{{ number_format((float)$n['kcal'], 0) }}</td>
                                    <td class="text-right">{{ number_format((float)$n['protein_g'], 1) }}</td>
                                    <td class="text-right">{{ number_format((float)$n['fat_g'], 1) }}</td>
                                    <td class="text-right">{{ number_format((float)$n['carb_g'], 1) }}</td>
                                    <td class="text-right">{{ number_format((float)$n['sodium_mg'], 0) }}</td>
                                    <td class="text-right">{{ number_format((float)$n['potassium_mg'], 0) }}</td>
                                    <td class="text-right">{{ number_format((float)$n['phosphorus_mg'], 0) }}</td>
                                </tr>
                            @endforeach
                            <tr class="totals-row">
                                <td colspan="2">Total {{ $meal->display_name }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['kcal'], 0) }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['protein_g'], 1) }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['fat_g'], 1) }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['carb_g'], 1) }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['sodium_mg'], 0) }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['potassium_mg'], 0) }}</td>
                                <td class="text-right">{{ number_format((float)$mealRow['totals']['phosphorus_mg'], 0) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    @else
                        <div style="color:#6b7280;">Fara item-uri pe aceasta masa.</div>
                    @endif
                </div>
            @endforeach

            @php($summary = $dayExport['summary'])
            <h3>Totaluri zi</h3>
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="label">Total Mic Dejun</div>
                    <div class="value">{{ number_format((float)$summary['breakfast']['kcal'], 0) }} kcal | {{ number_format((float)$summary['breakfast']['protein_g'], 1) }} g prot</div>
                </div>
                <div class="summary-card">
                    <div class="label">Total Pranz</div>
                    <div class="value">{{ number_format((float)$summary['lunch']['kcal'], 0) }} kcal | {{ number_format((float)$summary['lunch']['protein_g'], 1) }} g prot</div>
                </div>
                <div class="summary-card">
                    <div class="label">Total Cina</div>
                    <div class="value">{{ number_format((float)$summary['dinner']['kcal'], 0) }} kcal | {{ number_format((float)$summary['dinner']['protein_g'], 1) }} g prot</div>
                </div>
                <div class="summary-card">
                    <div class="label">Total Gustari</div>
                    <div class="value">{{ number_format((float)$summary['snacks']['kcal'], 0) }} kcal | {{ number_format((float)$summary['snacks']['protein_g'], 1) }} g prot</div>
                </div>
                <div class="summary-card">
                    <div class="label">Total Alte Mese</div>
                    <div class="value">{{ number_format((float)$summary['other']['kcal'], 0) }} kcal | {{ number_format((float)$summary['other']['protein_g'], 1) }} g prot</div>
                </div>
                <div class="summary-card grand">
                    <div class="label">Grand Total Zi</div>
                    <div class="value">
                        {{ number_format((float)$summary['grand_total']['kcal'], 0) }} kcal |
                        {{ number_format((float)$summary['grand_total']['protein_g'], 1) }} g prot |
                        Na {{ number_format((float)$summary['grand_total']['sodium_mg'], 0) }} mg |
                        K {{ number_format((float)$summary['grand_total']['potassium_mg'], 0) }} mg |
                        P {{ number_format((float)$summary['grand_total']['phosphorus_mg'], 0) }} mg
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    <section>
        <h2>Grand Total Plan</h2>
        <table>
            <thead>
            <tr>
                <th>Kcal</th>
                <th>Prot (g)</th>
                <th>Gras (g)</th>
                <th>Carb (g)</th>
                <th>Na (mg)</th>
                <th>K (mg)</th>
                <th>P (mg)</th>
            </tr>
            </thead>
            <tbody>
            <tr class="totals-row">
                <td class="text-right">{{ number_format((float)$planGrandTotals['kcal'], 0) }}</td>
                <td class="text-right">{{ number_format((float)$planGrandTotals['protein_g'], 1) }}</td>
                <td class="text-right">{{ number_format((float)$planGrandTotals['fat_g'], 1) }}</td>
                <td class="text-right">{{ number_format((float)$planGrandTotals['carb_g'], 1) }}</td>
                <td class="text-right">{{ number_format((float)$planGrandTotals['sodium_mg'], 0) }}</td>
                <td class="text-right">{{ number_format((float)$planGrandTotals['potassium_mg'], 0) }}</td>
                <td class="text-right">{{ number_format((float)$planGrandTotals['phosphorus_mg'], 0) }}</td>
            </tr>
            </tbody>
        </table>
    </section>
</body>
</html>
