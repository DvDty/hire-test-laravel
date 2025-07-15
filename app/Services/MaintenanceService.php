<?php

namespace App\Services;

use App\Enums\MaintenanceRequestStatus;
use App\Enums\TirePosition;
use App\Jobs\SendMaintenanceMessage;
use App\Mail\ConfirmationEmail;
use App\Models\Car;
use App\Models\MaintenanceRequest;
use App\Models\Tire;
use App\Models\TireReplacement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MaintenanceService
{
    public function createMaintenanceRequest(
        Car $car,
        ?User $user,
        ?string $scheduledDate,
        Collection $tireReplacements,
    ): MaintenanceRequest {
        Log::info('Starting maintenance request creation', func_get_args());

        /** @var MaintenanceRequest $maintenanceRequest */
        $maintenanceRequest = MaintenanceRequest::create([
            'car_id' => $car->id,
            'user_id' => $user?->id,
            'status' => MaintenanceRequestStatus::PENDING,
            'scheduled_date' => $scheduledDate,
        ]);

        dispatch(new SendMaintenanceMessage($maintenanceRequest));

        if ($user) {
            Mail::to($user)->queue(new ConfirmationEmail());
        }

        Log::info('Maintenance request created', ['Maintenance Request ID' => $maintenanceRequest->id]);

        // The code below would be triggered by a user action when the maintenance of the car starts
        $maintenanceRequest->update(['status' => MaintenanceRequestStatus::IN_PROGRESS]);

        $failedTireReplacements = collect();

        /** @var EloquentCollection<int, Tire> $tires */
        $tires = Tire::query()->whereIn('id', $tireReplacements->pluck('tire_id'))->get()->keyBy('id');

        foreach ($tireReplacements as $tireReplacement) {
            $success = $this->processTireReplacement(
                $maintenanceRequest,
                $tires->get(Arr::get($tireReplacement, 'tire_id')),
                TirePosition::from(Arr::get($tireReplacement, 'position')),
            );

            if (!$success) {
                Log::info('Unsuccessful tire replacement', $tireReplacement);

                $failedTireReplacements->push($tireReplacement);
            }
        }

        if ($failedTireReplacements->count() === 0) {
            $maintenanceRequest->update(['status' => MaintenanceRequestStatus::COMPLETED]);
        }

        $car->touch('last_maintenance_date');

        return $maintenanceRequest;
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