<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('modeles', function (Blueprint $table) {
            $table->increments('id')->startingValue(100);
            $table->string('nomModel');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->string('engine');
            $table->timestamps();

        });
        DB::statement("ALTER TABLE modeles ADD CONSTRAINT engine_check CHECK (engine IN ('Petrol', 'Hybrid','Electric'))");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modeles');
    }
};
