<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Keeps the wording each answer was given against. The PDF report finds
     * an answer by matching the hand-typed question text in
     * audit-pdf-report.blade.php to the question's wording; once questions
     * can be reworded on screen (Week 1), reading the live wording would
     * blank that row in every past report. The report now reads this column
     * first and falls back to the live wording.
     *
     * Existing answers are backfilled with their question's current wording,
     * which is the wording they were answered against: nothing could be
     * reworded before this release.
     */
    public function up(): void
    {
        Schema::table('answers_iaudit', function (Blueprint $table) {
            $table->text('question_text')->nullable()->after('question_id');
        });

        // One statement; a correlated subquery runs on MySQL and on the
        // SQLite test database alike (an UPDATE … JOIN does not).
        DB::table('answers_iaudit')
            ->whereNull('question_text')
            ->update([
                'question_text' => DB::raw(
                    '(select q.question_text from questions_iaudit q where q.question_id = answers_iaudit.question_id)'
                ),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_iaudit', function (Blueprint $table) {
            $table->dropColumn('question_text');
        });
    }
};
