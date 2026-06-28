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
        Schema::create('ipm_traps_iaudit', function (Blueprint $table) {
            $table->id();
            $table->string('ship_name')->nullable();
            $table->string('mnemonic_all')->nullable();
            $table->string('mnemonic_fleet')->nullable();
            $table->string('mnemonic_ship')->nullable();
            $table->string('dept_name')->nullable();
            $table->string('trap_type')->nullable();
            $table->string('deck_no')->nullable();
            $table->string('area')->nullable();
            $table->string('location')->nullable();
            $table->string('location_trap')->nullable();
            $table->string('count_type')->nullable();
            $table->timestamps();

            $table->index('ship_name');
            $table->index('mnemonic_all');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipm_traps_iaudit');
    }
};
