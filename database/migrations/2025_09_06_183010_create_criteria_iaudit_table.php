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
        Schema::create('criteria_iaudit', function (Blueprint $table) {
            $table->bigInteger('criteria_id')->primary();
            $table->bigInteger('reference_id')->nullable(); // -> template_refs_iaudit.reference_id
            $table->string('main_heading')->nullable();
            $table->string('table_heading')->nullable();
            $table->text('question')->nullable();

            $table->foreign('reference_id')
                ->references('reference_id')->on('template_refs_iaudit')
                ->onUpdate('cascade')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_iaudit');
    }
};
