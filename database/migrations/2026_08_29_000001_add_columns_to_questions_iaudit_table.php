<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Additive only — every new column is nullable (or safely defaulted) so
     * the 150 pre-existing rows, and the answers_iaudit rows that point at
     * them, are never touched. See Week 1 plan for the full rationale
     * (department_id kept as a plain string, question_id PK left as-is).
     */
    public function up(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->string('department_id')->nullable()->after('question_id');
            $table->string('short_code')->nullable()->after('department_id');
            $table->string('template')->nullable()->after('short_code');
            $table->enum('row_type', ['template', 'text', 'criteria', 'question'])
                ->nullable()->after('template');
            $table->unsignedInteger('ordinal')->nullable();
            $table->string('text_icon')->nullable();
            $table->string('block_ref')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('imported_at')->nullable();

            $table->unique(['template', 'short_code'], 'questions_iaudit_template_short_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->dropUnique('questions_iaudit_template_short_code_unique');
            $table->dropColumn([
                'department_id',
                'short_code',
                'template',
                'row_type',
                'ordinal',
                'text_icon',
                'block_ref',
                'is_active',
                'imported_at',
            ]);
        });
    }
};
