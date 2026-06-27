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
        Schema::create('ipm_efk_iaudit', function (Blueprint $table) {
            $table->id();
            $table->string('ship_name')->nullable();
            $table->string('mnemonic_both')->nullable();
            $table->string('mnemonic_fleet')->nullable();
            $table->string('mnemonic_ship')->nullable();
            $table->string('efk_type')->nullable();
            $table->string('deck_no')->nullable();
            $table->string('department')->nullable();
            $table->string('area')->nullable();
            $table->string('location')->nullable();
            $table->string('install_date')->nullable();
            $table->string('type_uvt')->nullable();
            $table->string('count_type')->nullable();
            $table->timestamps();

            $table->index('ship_name');
            $table->index('mnemonic_both');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipm_efk_iaudit');
    }
};
