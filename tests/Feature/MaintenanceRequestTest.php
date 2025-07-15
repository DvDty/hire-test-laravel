<?php

namespace Tests\Feature;

use App\Enums\TirePosition;
use App\Models\Car;
use App\Models\Tire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $cars = Car::factory(10)->create(['user_id' => $this->user->id]);
        $this->car = $cars->first();
        $this->tire = Tire::factory()->create();
    }

    public function test_can_create_maintenance_request()
    {
        $tire = Tire::factory()->create(['stock' => 2]);

        $response = $this->postJson('/api/maintenance-requests', [
            'car_id' => $this->car->id,
            'user_id' => $this->user->id,
            'scheduled_date' => now()->addDays(7),
            'tire_replacements' => [
                [
                    'tire_id' => $tire->id,
                    'position' => TirePosition::FRONT_LEFT,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $tire->refresh();
        $this->assertEquals(1, $tire->stock);

        $this->assertDatabaseHas('tire_replacements', [
            'car_id' => $this->car->id,
            'tire_id' => $tire->id,
            'position' => 'front_left',
        ]);

        $this->car->refresh();
        $this->assertNotNull($this->car->last_maintenance_date);
    }

    public function test_can_create_maintenance_request_multiple_tires()
    {
        $tire = Tire::factory()->create(['stock' => 2]);

        $response = $this->postJson('/api/maintenance-requests', [
            'car_id' => $this->car->id,
            'user_id' => $this->user->id,
            'scheduled_date' => now()->addDays(7),
            'tire_replacements' => [
                [
                    'tire_id' => $tire->id,
                    'position' => TirePosition::FRONT_LEFT,
                ],
                [
                    'tire_id' => $tire->id,
                    'position' => TirePosition::FRONT_RIGHT,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $tire->refresh();
        $this->assertEquals(0, $tire->stock);

        $this->assertDatabaseHas('tire_replacements', [
            'car_id' => $this->car->id,
            'tire_id' => $tire->id,
            'position' => 'front_left',
        ]);

        $this->assertDatabaseHas('tire_replacements', [
            'car_id' => $this->car->id,
            'tire_id' => $tire->id,
            'position' => 'front_right',
        ]);
    }

    public function test_cannot_create_maintenance_request_with_insufficient_stock()
    {
        $tire = Tire::factory()->create(['stock' => 0]);

        $response = $this->postJson('/api/maintenance-requests', [
            'car_id' => $this->car->id,
            'tire_replacements' => [
                [
                    'tire_id' => $tire->id,
                    'position' => TirePosition::FRONT_LEFT,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrorFor('tire_replacements.0.tire_id');

        $this->assertDatabaseMissing('maintenance_requests', [
            'car_id' => $this->car->id,
        ]);

        $tire->refresh();
        $this->assertEquals(0, $tire->stock);
    }

    public function test_cannot_create_maintenance_request_with_insufficient_stock_when_multiple_tires()
    {
        $tire = Tire::factory()->create(['stock' => 1]);

        $response = $this->postJson('/api/maintenance-requests', [
            'car_id' => $this->car->id,
            'user_id' => $this->user->id,
            'scheduled_date' => now()->addDays(7),
            'tire_replacements' => [
                [
                    'tire_id' => $tire->id,
                    'position' => TirePosition::FRONT_LEFT,
                ],
                [
                    'tire_id' => $tire->id,
                    'position' => TirePosition::FRONT_RIGHT,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrorFor('tire_replacements.0.tire_id');

        $this->assertDatabaseMissing('maintenance_requests', [
            'car_id' => $this->car->id,
        ]);

        $tire->refresh();
        $this->assertEquals(1, $tire->stock);
    }
}