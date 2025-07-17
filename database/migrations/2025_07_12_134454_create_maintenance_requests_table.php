<?php

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Car::class)->constrained();
            $table->foreignIdFor(User::class)->nullable()->constrained();

            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('completed_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};