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
        Schema::create('templates_iaudit', function (Blueprint $table) {
            $table->bigInteger('template_id')->primary();
            $table->string('template_code')->nullable();
            $table->string('report_title')->nullable();
            $table->bigInteger('department_id')->nullable();  // FK -> departments_iaudit
            $table->bigInteger('reference_id')->nullable();   // FK -> template_refs_iaudit

            $table->foreign('department_id')
                ->references('department_id')->on('departments_iaudit')
                ->onUpdate('cascade')->onDelete('set null');

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
        Schema::dropIfExists('templates_iaudit');
    }
};
