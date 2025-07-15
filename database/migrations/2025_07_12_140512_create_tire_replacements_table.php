<?php

use App\Models\Car;
use App\Models\MaintenanceRequest;
use App\Models\Tire;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tire_replacements', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Car::class);
            $table->foreignIdFor(Tire::class);
            $table->foreignIdFor(MaintenanceRequest::class);

            $table->enum('position', ['front_left', 'front_right', 'rear_left', 'rear_right']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tire_replacements');
    }
};