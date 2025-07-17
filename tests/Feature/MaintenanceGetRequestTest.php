<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\MaintenanceRequest;
use Tests\TestCase;

class MaintenanceGetRequestTest extends TestCase
{
    private static bool $seededMaintenanceRequests = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed just once for all tests
        if (!self::$seededMaintenanceRequests) {
            MaintenanceRequest::factory()->count(10000)->create();

            self::$seededMaintenanceRequests = true;
        }
    }

    public function test_get_maintenance_requests_endpoint_is_paginated()
    {
        $response = $this->getJson('/api/maintenance-requests');;

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_performance_of_get_maintenance_requests_endpoint()
    {
        $start = microtime(true);

        $this->getJson(route('maintenance-requests.index'))->assertSuccessful();

        $duration = microtime(true) - $start;

        $this->assertLessThan(1, $duration, 'Request took too long.');
    }

    public function test_performance_with_filters()
    {
        $start = microtime(true);

        $uri = route('maintenance-requests.index', [
            'car_brand' => Brand::first()->name,
            'plate_number' => 'A',
        ]);

        $this->getJson($uri)->assertSuccessful();

        $duration = microtime(true) - $start;

        $this->assertLessThan(1, $duration, 'Request took too long.');
    }
}
