<?php

namespace App\Http\Controllers;

use App\Models\MenuPlan;
use App\Models\MenuDay;
use App\Models\Patient;
use App\Services\NutrientCalculatorService;
use Illuminate\Http\Request;

class MenuPlanController extends Controller
{
    public function __construct(
        private NutrientCalculatorService $nutrientCalculator
    ) {}

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $patientId = $request->query('patient_id');
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = MenuPlan::query()
            ->with(['patient', 'days'])
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($patientId !== null && $patientId !== '') {
            $query->where('patient_id', (int) $patientId);
        }

        $menuPlans = $query->paginate($perPage)->withQueryString();

        $patients = Patient::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('menu-plans.index', compact('menuPlans', 'patients', 'q', 'patientId', 'perPage'));
    }

    public function create(Request $request)
    {
        $patients = Patient::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        $patientId = $request->query('patient_id');

        return view('menu-plans.create', compact('patients', 'patientId'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $menuPlan = MenuPlan::create($data);

        return redirect()
            ->route('menu-plans.show', $menuPlan)
            ->with('success', 'Planul a fost creat.');
    }

    public function show(Request $request, MenuPlan $menuPlan)
    {
        $perPage = (int) $request->query('per_page', 5);

        if (!in_array($perPage, [5, 10, 15, 20], true)) {
            $perPage = 5;
        }

        $menuPlan->load(['patient']);

        $days = MenuDay::query()
            ->where('menu_plan_id', $menuPlan->id)
            ->with(['meals.items.food.nutrients'])
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $daysWithNutrients = [];
        foreach ($days as $day) {
            $daysWithNutrients[] = [
                'day' => $day,
                'nutrients' => $this->nutrientCalculator->calculateDayWithComparison($day),
            ];
        }

        return view('menu-plans.show', compact('menuPlan', 'daysWithNutrients', 'days', 'perPage'));
    }

    public function pdf(MenuPlan $menuPlan)
    {
        $menuPlan->load([
            'patient',
            'days.meals.mealType',
            'days.meals.items.food.nutrients',
            'days.meals.items.recipe.items.food.nutrients',
        ]);

        $daysForExport = [];
        $planGrandTotals = $this->emptyNutrientTotals();

        foreach ($menuPlan->days as $day) {
            $dayRows = [];
            $mealCategoryTotals = [
                'breakfast' => $this->emptyNutrientTotals(),
                'lunch' => $this->emptyNutrientTotals(),
                'dinner' => $this->emptyNutrientTotals(),
                'snacks' => $this->emptyNutrientTotals(),
                'other' => $this->emptyNutrientTotals(),
            ];

            foreach ($day->meals as $meal) {
                $itemRows = [];

                foreach ($meal->items as $item) {
                    $itemRows[] = [
                        'item' => $item,
                        'nutrients' => $item->calculateNutrients(),
                    ];
                }

                $mealTotals = $meal->calculateNutrients();
                $category = $this->resolveMealCategory($meal->display_name);
                $mealCategoryTotals[$category] = $this->sumNutrients($mealCategoryTotals[$category], $mealTotals);

                $dayRows[] = [
                    'meal' => $meal,
                    'items' => $itemRows,
                    'totals' => $mealTotals,
                ];
            }

            $dayGrandTotals = $day->calculateNutrients();
            $planGrandTotals = $this->sumNutrients($planGrandTotals, $dayGrandTotals);

            $daysForExport[] = [
                'day' => $day,
                'rows' => $dayRows,
                'summary' => [
                    'breakfast' => $mealCategoryTotals['breakfast'],
                    'lunch' => $mealCategoryTotals['lunch'],
                    'dinner' => $mealCategoryTotals['dinner'],
                    'snacks' => $mealCategoryTotals['snacks'],
                    'other' => $mealCategoryTotals['other'],
                    'grand_total' => $dayGrandTotals,
                ],
            ];
        }

        return view('menu-plans.pdf', [
            'menuPlan' => $menuPlan,
            'daysForExport' => $daysForExport,
            'planGrandTotals' => $planGrandTotals,
        ]);
    }

    public function edit(MenuPlan $menuPlan)
    {
        $patients = Patient::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('menu-plans.edit', compact('menuPlan', 'patients'));
    }

    public function update(Request $request, MenuPlan $menuPlan)
    {
        $data = $this->validated($request);

        $menuPlan->update($data);

        return redirect()
            ->route('menu-plans.show', $menuPlan)
            ->with('success', 'Planul a fost actualizat.');
    }

    public function destroy(MenuPlan $menuPlan)
    {
        $menuPlan->delete();

        return redirect()
            ->route('menu-plans.index')
            ->with('success', 'Planul a fost sters.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'name' => ['required', 'string', 'max:200'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function emptyNutrientTotals(): array
    {
        return [
            'kcal' => 0,
            'protein_g' => 0,
            'fat_g' => 0,
            'carb_g' => 0,
            'sodium_mg' => 0,
            'potassium_mg' => 0,
            'phosphorus_mg' => 0,
        ];
    }

    private function sumNutrients(array $base, array $increment): array
    {
        foreach ($base as $key => $value) {
            $base[$key] += (float) ($increment[$key] ?? 0);
        }

        return $base;
    }

    private function resolveMealCategory(string $mealName): string
    {
        $normalized = strtolower(trim($mealName));

        if (str_contains($normalized, 'gustare')) {
            return 'snacks';
        }

        if (str_contains($normalized, 'cina')) {
            return 'dinner';
        }

        if (str_contains($normalized, 'pranz')) {
            return 'lunch';
        }

        if (str_contains($normalized, 'mic dejun')) {
            return 'breakfast';
        }

        return 'other';
    }
}
