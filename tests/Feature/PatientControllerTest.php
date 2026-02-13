<?php

namespace Tests\Feature;

use App\Models\MenuPlan;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_patients_index(): void
    {
        Patient::factory()->create([
            'first_name' => 'Ion',
            'last_name' => 'Popescu',
        ]);

        $response = $this->actingAs($this->user)->get(route('patients.index'));

        $response->assertOk();
        $response->assertSee('Ion Popescu');
    }

    public function test_can_create_patient(): void
    {
        $response = $this->actingAs($this->user)->post(route('patients.store'), [
            'first_name' => 'Maria',
            'last_name' => 'Ionescu',
            'sex' => 'F',
            'birthdate' => '1990-05-10',
            'current_height_cm' => 168,
            'current_weight_kg' => 62.5,
            'diagnosis' => 'CKD',
            'target_kcal_per_day' => 1800,
            'target_protein_g_per_day' => 60,
            'target_carbs_g_per_day' => 210,
            'target_fat_g_per_day' => 60,
            'limit_sodium_mg_per_day' => 2000,
            'limit_potassium_mg_per_day' => 2500,
            'limit_phosphorus_mg_per_day' => 900,
            'limit_fluids_ml_per_day' => 1800,
            'notes' => 'Pacient nou',
            'is_active' => 1,
        ]);

        $patient = Patient::query()->first();

        $response->assertRedirect(route('patients.show', $patient));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'first_name' => 'Maria',
            'last_name' => 'Ionescu',
            'diagnosis' => 'CKD',
            'is_active' => true,
        ]);
    }

    public function test_can_update_patient(): void
    {
        $patient = Patient::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('patients.update', $patient), [
            'first_name' => 'New',
            'last_name' => 'Name',
            'sex' => 'M',
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('patients.show', $patient));
        $response->assertSessionHas('success');

        $patient->refresh();
        $this->assertSame('New', $patient->first_name);
        $this->assertSame(false, $patient->is_active);
    }

    public function test_can_delete_patient_without_menu_plans(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('patients.destroy', $patient));

        $response->assertRedirect(route('patients.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    public function test_cannot_delete_patient_with_menu_plans(): void
    {
        $patient = Patient::factory()->create();
        MenuPlan::factory()->create(['patient_id' => $patient->id]);

        $response = $this->actingAs($this->user)->delete(route('patients.destroy', $patient));

        $response->assertRedirect(route('patients.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('patients', ['id' => $patient->id]);
    }
}
