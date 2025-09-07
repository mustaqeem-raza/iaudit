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
        Schema::create('questions_iaudit', function (Blueprint $table) {
            $table->bigInteger('question_id')->primary();

            $table->bigInteger('reference_id')->nullable();   // -> template_refs_iaudit.reference_id
            $table->bigInteger('heading_id')->nullable();      // -> headings_iaudit.heading_id
            $table->bigInteger('subheading_id')->nullable();   // -> sub_headings_iaudit.subheading_id
            $table->bigInteger('category_id')->nullable();     // -> categories_iaudit.category_id

            $table->text('question_text')->nullable();
            $table->text('information_text')->nullable();

            $table->foreign('reference_id')
                ->references('reference_id')->on('template_refs_iaudit')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('heading_id')
                ->references('heading_id')->on('headings_iaudit')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('subheading_id')
                ->references('subheading_id')->on('sub_headings_iaudit')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('category_id')
                ->references('category_id')->on('categories_iaudit')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('department_id')
                ->references('department_id')->on('departments_iaudit')
                ->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions_iaudit');
    }
};
