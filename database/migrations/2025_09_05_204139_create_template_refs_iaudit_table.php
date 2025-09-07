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
        Schema::create('template_refs_iaudit', function (Blueprint $table) {
            $table->bigInteger('reference_id')->primary();
            $table->string('template_code')->nullable();
            $table->string('reference_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_refs_iaudit');
    }
};
