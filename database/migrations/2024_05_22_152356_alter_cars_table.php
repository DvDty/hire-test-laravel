<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedBigInteger('front_tire_id');
            $table->unsignedBigInteger('rear_tire_id');

            $table->foreign('front_tire_id')->references('id')->on('tires');
            $table->foreign('rear_tire_id')->references('id')->on('tires');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('front_tire_id');
            $table->dropColumn('rear_tire_id');
        });
    }
};
