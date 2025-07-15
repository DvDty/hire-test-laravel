<?php

namespace App\Jobs;

use App\Models\MaintenanceRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendMaintenanceMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public MaintenanceRequest $maintenanceRequest)
    {
    }

    public function handle(): void
    {
        Log::info('Maintenance request received ' . $this->maintenanceRequest->id);
    }
}
