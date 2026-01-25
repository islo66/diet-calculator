<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $patientId = $request->query('patient_id');
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = Recipe::query()
            ->with(['patient', 'items.food'])
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($patientId !== null && $patientId !== '') {
            if ($patientId === 'global') {
                $query->whereNull('patient_id');
            } else {
                $query->where('patient_id', (int) $patientId);
            }
        }

        $recipes = $query->paginate($perPage)->withQueryString();

        $patients = Patient::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('recipes.index', compact('recipes', 'patients', 'q', 'patientId', 'perPage'));
    }

    public function create(Request $request)
    {
        $patients = Patient::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        $patientId = $request->query('patient_id');

        return view('recipes.create', compact('patients', 'patientId'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (empty($data['patient_id'])) {
            $data['patient_id'] = null;
        }

        $recipe = Recipe::create($data);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Reteta a fost creata. Adauga ingredientele.');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['patient', 'items.food.nutrients']);

        $totalNutrients = $recipe->calculateTotalNutrients();
        $nutrientsPer100 = $recipe->nutrients_per_100;

        return view('recipes.show', compact('recipe', 'totalNutrients', 'nutrientsPer100'));
    }

    public function edit(Recipe $recipe)
    {
        $patients = Patient::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('recipes.edit', compact('recipe', 'patients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $data = $this->validated($request);

        if (empty($data['patient_id'])) {
            $data['patient_id'] = null;
        }

        $recipe->update($data);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Reteta a fost actualizata.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Reteta a fost stearsa.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'name' => ['required', 'string', 'max:200'],
            'yield_qty' => ['required', 'numeric', 'min:1'],
            'yield_unit' => ['required', 'string', 'in:g,ml'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}