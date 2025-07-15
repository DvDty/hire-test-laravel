<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceRequestStore;
use App\Models\Car;
use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Services\MaintenanceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Throwable;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $maintenanceRequests = MaintenanceRequest::with([
            'car' => function ($query) {
                $query->select('id', 'model_id', 'matricule', 'last_maintenance_date');
            },
            'car.modele' => function ($query) {
                $query->select('id', 'brand_id', 'nomModel');
            },
            'car.modele.brand' => function ($query) {
                $query->select('id', 'name', 'country');
            },
            'user' => function ($query) {
                $query->select('id', 'name');
            },
            'tireReplacements' => function ($query) {
                $query->select('id', 'maintenance_request_id', 'tire_id', 'position');
            },
            'tireReplacements.tire' => function ($query) {
                $query->select('id', 'brand', 'type');
            },
        ])->select('id', 'car_id', 'user_id', 'status', 'scheduled_date', 'created_at');

        if ($request->has('car_brand')) {
            $maintenanceRequests->whereHas('car.modele.brand', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('car_brand') . '%');
            });
        }

        if ($request->has('car_model')) {
            $maintenanceRequests->whereHas('car.modele', function ($query) use ($request) {
                $query->where('nomModel', 'like', '%' . $request->input('car_model') . '%');
            });
        }

        if ($request->has('plate_number')) {
            $maintenanceRequests->whereHas('car', function ($query) use ($request) {
                $query->where('matricule', 'like', '%' . $request->input('plate_number') . '%');
            });
        }

        if ($request->has('username')) {
            $maintenanceRequests->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('username') . '%');
            });
        }

        $maintenanceRequests = $maintenanceRequests
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $maintenanceRequests]);
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
                    collect(Arr::get($data, 'tire_replacements', [])),
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
