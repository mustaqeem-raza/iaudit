<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Week 1 of the database management module: on-screen question editing.
     * Additive only; defaults match today's behaviour, so existing rows need
     * no backfill and neither mobile endpoint changes shape.
     *
     * - answer_options, priority: edited on the admin form and stored only for
     *   now; the mobile app can read them from /api/v2/questions once it is
     *   updated. Every question accepts Yes/No/N/A today (the
     *   answers_iaudit.answer enum) and none is flagged critical. The workbook
     *   import never writes these columns, so a re-import keeps them.
     *   Allowed values: QuestionIaudit::ANSWER_OPTIONS and ::PRIORITIES.
     *
     * - deleted_at: "delete" hides a question instead of removing the row.
     *   POST /api/answers checks every question_id exists, so removing a row
     *   would reject a whole audit from a phone that cached the question, and
     *   max()+1 id assignment could hand the id to a new question.
     */
    public function up(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->string('answer_options', 16)->default('yes_no_na')->after('question_text');
            $table->string('priority', 16)->default('standard')->after('answer_options');
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
            $table->dropColumn(['answer_options', 'priority', 'deleted_at']);
        });
    }
};
