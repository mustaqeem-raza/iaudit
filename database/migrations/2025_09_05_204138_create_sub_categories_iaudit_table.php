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
        Schema::create('sub_categories_iaudit', function (Blueprint $table) {
            $table->bigInteger('subheading_id')->primary();
            $table->bigInteger('heading_id'); // FK to headings_iaudit.heading_id
            $table->string('name')->nullable();

            $table->foreign('heading_id')
                ->references('heading_id')->on('headings_iaudit')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories_iaudit');
    }
};
