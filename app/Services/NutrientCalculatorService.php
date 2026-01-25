<?php

namespace App\Services;

use App\Models\MenuDay;
use App\Models\Patient;

class NutrientCalculatorService
{
    public function calculateDayWithComparison(MenuDay $day): array
    {
        $day->loadMissing(['menuPlan.patient', 'meals.items.food.nutrients']);

        $totals = $day->calculateNutrients();
        $patient = $day->menuPlan->patient;

        return [
            'totals' => $totals,
            'limits' => $this->getPatientLimits($patient),
            'comparison' => $this->compareWithLimits($totals, $patient),
        ];
    }

    public function getPatientLimits(Patient $patient): array
    {
        return [
            'kcal' => $patient->target_kcal_per_day,
            'protein_g' => $patient->target_protein_g_per_day,
            'sodium_mg' => $patient->limit_sodium_mg_per_day,
            'potassium_mg' => $patient->limit_potassium_mg_per_day,
            'phosphorus_mg' => $patient->limit_phosphorus_mg_per_day,
        ];
    }

    public function compareWithLimits(array $totals, Patient $patient): array
    {
        $comparison = [];

        $comparison['kcal'] = $this->compareTarget(
            $totals['kcal'],
            $patient->target_kcal_per_day
        );

        $comparison['protein_g'] = $this->compareTarget(
            $totals['protein_g'],
            $patient->target_protein_g_per_day
        );

        $comparison['sodium_mg'] = $this->compareLimit(
            $totals['sodium_mg'],
            $patient->limit_sodium_mg_per_day
        );

        $comparison['potassium_mg'] = $this->compareLimit(
            $totals['potassium_mg'],
            $patient->limit_potassium_mg_per_day
        );

        $comparison['phosphorus_mg'] = $this->compareLimit(
            $totals['phosphorus_mg'],
            $patient->limit_phosphorus_mg_per_day
        );

        return $comparison;
    }

    private function compareTarget(?float $value, ?float $target): string
    {
        if ($target === null || $target == 0) {
            return 'ok';
        }

        $percentage = ($value / $target) * 100;

        if ($percentage < 90) {
            return 'under';
        } elseif ($percentage > 110) {
            return 'over';
        }

        return 'ok';
    }

    private function compareLimit(?float $value, ?float $limit): string
    {
        if ($limit === null || $limit == 0) {
            return 'ok';
        }

        $percentage = ($value / $limit) * 100;

        if ($percentage >= 100) {
            return 'over';
        } elseif ($percentage >= 80) {
            return 'warning';
        }

        return 'ok';
    }
}