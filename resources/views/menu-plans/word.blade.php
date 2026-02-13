<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>{{ $menuPlan->name }} - Word</title>
    <style>
        @page { size: A4 portrait; margin: 18mm; }
        body { font-family: "Times New Roman", Arial, sans-serif; font-size: 12pt; color: #000; }
        .day-page { page-break-after: always; }
        .day-page:last-child { page-break-after: auto; }
        .days-title { text-align: center; font-weight: 700; margin-bottom: 6pt; }
        .plan-title { text-align: center; font-weight: 700; text-decoration: underline; margin-bottom: 14pt; }
        .meal-block { margin-bottom: 10pt; }
        .meal-title { font-weight: 700; text-decoration: underline; margin-bottom: 4pt; }
        ul { margin: 0 0 6pt 18pt; padding: 0; }
        li { margin: 2pt 0; }
        .recipe-portion { font-weight: 700; text-decoration: underline; margin: 4pt 0; }
        .recipe-notes { margin-left: 18pt; font-style: italic; }
        .muted { color: #666; }
    </style>
</head>
<body>
@foreach($daysForExport as $dayExport)
    @php($day = $dayExport['day'])
    <section class="day-page">
        <div class="days-title">{{ $day->name }}</div>
        <div class="plan-title">{{ $menuPlan->name }}</div>

        @foreach($dayExport['rows'] as $mealRow)
            @php($meal = $mealRow['meal'])
            <div class="meal-block">
                <div class="meal-title">{{ $meal->display_name }}:</div>

                @if(count($mealRow['items']) > 0)
                    <ul>
                        @foreach($mealRow['items'] as $itemRow)
                            @php($item = $itemRow['item'])
                            <li>
                                {{ $item->name }}
                                @if($item->portion_qty && $item->portion_unit)
                                    {{ number_format((float)$item->portion_qty, 1) }}{{ $item->portion_unit }}
                                @endif
                                @if($item->notes)
                                    - {{ $item->notes }}
                                @endif

                                @if($item->item_type === \\App\\Models\\MealItem::TYPE_RECIPE && $item->recipe)
                                    <div class="recipe-portion">
                                        Portia {{ number_format((float)$item->portion_qty, 1) }} {{ $item->portion_unit }}
                                    </div>
                                    @if($item->recipe->items->count() > 0)
                                        <ul>
                                            @foreach($item->recipe->items as $recipeItem)
                                                <li>
                                                    {{ $recipeItem->food?->name ?? __('app.common.unknown') }}
                                                    {{ number_format((float)$recipeItem->qty, 1) }}{{ $recipeItem->unit }}
                                                    @if($recipeItem->notes)
                                                        - {{ $recipeItem->notes }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if($item->recipe->notes)
                                        <div class="recipe-notes">Mod de preparare: {{ $item->recipe->notes }}</div>
                                    @endif
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="muted">Fara item-uri pe aceasta masa.</div>
                @endif
            </div>
        @endforeach
    </section>
@endforeach
</body>
</html>
