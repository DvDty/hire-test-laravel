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
        Schema::table('modeles', function (Blueprint $table) {
            DB::statement("ALTER TABLE modeles DROP CONSTRAINT engine_check");
            $table->dropColumn('engine');
            $table->unsignedInteger('engine_type_id')->nullable();

            $table->foreign('engine_type_id')
                ->references('id')->on('engine_types')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modeles', function (Blueprint $table) {
            $table->dropColumn('engine_type_id');
            $table->string('engine')->nullable();
        });

        DB::statement("ALTER TABLE modeles ADD CONSTRAINT engine_check CHECK (engine IN ('Petrol', 'Hybrid','Electric'))");
    }
};
