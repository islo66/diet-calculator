<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $isActive = $request->query('is_active');
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = Patient::query()
            ->withCount(['menuPlans', 'recipes'])
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($q !== '') {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('first_name', 'like', '%' . $q . '%')
                    ->orWhere('last_name', 'like', '%' . $q . '%')
                    ->orWhere('diagnosis', 'like', '%' . $q . '%');
            });
        }

        if ($isActive === '1' || $isActive === '0') {
            $query->where('is_active', (bool) $isActive);
        }

        $patients = $query->paginate($perPage)->withQueryString();

        return view('patients.index', compact('patients', 'q', 'isActive', 'perPage'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $patient = Patient::create($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Pacientul a fost creat.');
    }

    public function show(Patient $patient)
    {
        $patient->loadCount(['menuPlans', 'recipes']);

        $menuPlans = $patient->menuPlans()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $recipes = $patient->recipes()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        return view('patients.show', compact('patient', 'menuPlans', 'recipes'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $this->validated($request);

        $patient->update($data);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Pacientul a fost actualizat.');
    }

    public function destroy(Patient $patient)
    {
        if ($patient->menuPlans()->exists()) {
            return redirect()
                ->route('patients.index')
                ->with('error', 'Nu poti sterge pacientul deoarece are planuri de meniu asociate.');
        }

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Pacientul a fost sters.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'first_name' => ['required', 'string', 'max:200'],
            'last_name' => ['required', 'string', 'max:200'],
            'sex' => ['nullable', 'string', 'in:M,F'],
            'birthdate' => ['nullable', 'date', 'before_or_equal:today'],
            'current_height_cm' => ['nullable', 'integer', 'min:50', 'max:300'],
            'current_weight_kg' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'target_kcal_per_day' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'target_protein_g_per_day' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'target_carbs_g_per_day' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'target_fat_g_per_day' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'limit_sodium_mg_per_day' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'limit_potassium_mg_per_day' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'limit_phosphorus_mg_per_day' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'limit_fluids_ml_per_day' => ['nullable', 'integer', 'min:0', 'max:20000'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
