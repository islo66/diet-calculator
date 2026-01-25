<?php

namespace App\Http\Controllers;

use App\Models\MenuPlan;
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

    public function show(MenuPlan $menuPlan)
    {
        $menuPlan->load([
            'patient',
            'days.meals.items.food.nutrients',
        ]);

        $daysWithNutrients = [];
        foreach ($menuPlan->days as $day) {
            $daysWithNutrients[] = [
                'day' => $day,
                'nutrients' => $this->nutrientCalculator->calculateDayWithComparison($day),
            ];
        }

        return view('menu-plans.show', compact('menuPlan', 'daysWithNutrients'));
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
}