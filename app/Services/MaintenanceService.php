<?php

namespace App\Services;

use App\Enums\MaintenanceRequestStatus;
use App\Enums\TirePosition;
use App\Models\Car;
use App\Models\MaintenanceRequest;
use App\Models\Tire;
use App\Models\TireReplacement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaintenanceService
{
    /**
     * @return array{success: bool, maintenance_request?: MaintenanceRequest, message?: string}
     */
    public function createMaintenanceRequest(array $data): array
    {
        try {
            DB::beginTransaction();

            Log::info('Starting maintenance request creation', $data);

            /** @var Car $car */
            $car = Car::findOrFail($data['car_id']);

            /** @var ?User $user */
            $user = isset($data['user_id']) ? User::findOrFail($data['user_id']) : null;

            /** @var MaintenanceRequest $maintenanceRequest */
            $maintenanceRequest = MaintenanceRequest::create([
                'car_id' => $car->id,
                'user_id' => $user?->id,
                'status' => MaintenanceRequestStatus::PENDING,
                'scheduled_date' => $data['scheduled_date'] ?? null,
            ]);

            Log::info('Maintenance request created', ['Maintenance Request ID' => $maintenanceRequest->id]);

            // In a real application the code below would be triggered by a user action when the work is starting
            $maintenanceRequest->update(['status' => MaintenanceRequestStatus::IN_PROGRESS]);

            $tireReplacements = collect($data['tire_replacements'] ?? []);
            $failTireReplacements = collect();

            /** @var Collection<int, Tire> $tires */
            $tires = Tire::whereIn('id', $tireReplacements->pluck('tire_id'))->get()->keyBy('id');

            foreach ($tireReplacements as $tireReplacement) {
                $success = $this->processTireReplacement(
                    $maintenanceRequest,
                    $tires->get(Arr::get($tireReplacement, 'tire_id')),
                    TirePosition::from(Arr::get($tireReplacement, 'position')),
                );

                if (!$success) {
                    $failTireReplacements->push($tireReplacement);
                }
            }

            if ($failTireReplacements->count() === 0) {
                $maintenanceRequest->update(['status' => MaintenanceRequestStatus::COMPLETED]);
            }

            $car->touch('last_maintenance_date');

            DB::commit();

            return ['success' => true, 'maintenance_request' => $maintenanceRequest];
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Maintenance request creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create maintenance request: ' . $e->getMessage(),
            ];
        }
    }

    private function processTireReplacement(
        MaintenanceRequest $maintenanceRequest,
        Tire $tire,
        TirePosition $tirePosition,
    ): bool {
        Log::info('Processing tire replacement');

        if (!$tire->hasStock()) {
            Log::warning(sprintf('Insufficient stock for tire %s %s', $tire->brand, $tire->model));

            return false;
        }

        $tireReplacement = TireReplacement::create([
            'car_id' => $maintenanceRequest->car_id,
            'tire_id' => $tire->id,
            'position' => $tirePosition,
            'replaced_at' => now(),
            'maintenance_request_id' => $maintenanceRequest->id,
        ]);

        $tire->deductStock();

        Log::info('Tire replacement record created', [
            'tire_replacement_id' => $tireReplacement->id,
            'position' => $tirePosition->value,
        ]);

        return true;
    }
} 