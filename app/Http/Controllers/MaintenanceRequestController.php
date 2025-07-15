<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceRequestStore;
use App\Models\Car;
use App\Models\User;
use App\Services\MaintenanceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
    }

    public function store(MaintenanceRequestStore $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $car = Car::findOrFail(Arr::get($data, 'car_id'));

            $userId = Arr::get($data, 'user_id');
            $user = $userId ? User::findOrFail($userId) : null;

            $maintenanceRequest = resolve(MaintenanceService::class)
                ->createMaintenanceRequest(
                    $car,
                    $user,
                    Arr::get($data, 'scheduled_date'),
                    Arr::get($data, 'tire_replacements', []),
                );

            DB::commit();

            return response()->json(['success' => true, 'data' => $maintenanceRequest, 201]);
        } catch (Throwable $exception) {
            DB::rollBack();

            $message = 'Failed to create maintenance request: ' . $exception->getMessage();

            Log::error($message);

            return response()->json($message, $exception instanceof ModelNotFoundException ? 404 : 500);
        }
    }
}
