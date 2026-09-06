<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original create_answers_iaudit_table migration *declares*
     * question_id -> questions_iaudit.question_id with onDelete('cascade'),
     * but verified live: no such constraint actually exists in the
     * database (only audit_id -> audits.id is enforced). This migration
     * ADDS the constraint for the first time, with onDelete('restrict')
     * rather than cascade — a question with live answers attached must
     * block deletion, not silently take the answers down with it.
     *
     * Confirmed zero orphaned answers_iaudit rows before writing this, so
     * it is safe to add against the current live data. Kept as its own
     * migration so it can be deployed/rolled back independently of the
     * additive column migrations above.
     *
     * Discovered while writing this migration: adding the FK fails outright
     * with a MySQL 3780 "incompatible" error, because questions_iaudit.
     * question_id is a signed `bigint` while answers_iaudit.question_id is
     * `bigint unsigned` — a pre-existing type mismatch. This is almost
     * certainly *why* the FK declared in the original create_answers_iaudit_
     * table migration never actually took effect live (it would have hit
     * this same error). Fixed here by widening questions_iaudit.question_id
     * to unsigned first — safe, since all 150 existing values are positive
     * and this doesn't change PK/auto-increment semantics (left as-is per
     * the Week 1 decision not to flip $incrementing yet).
     */
    public function up(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id')->change();
        });

        Schema::table('answers_iaudit', function (Blueprint $table) {
            $table->foreign('question_id', 'answers_iaudit_question_id_foreign')
                ->references('question_id')->on('questions_iaudit')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_iaudit', function (Blueprint $table) {
            $table->dropForeign('answers_iaudit_question_id_foreign');
        });

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->bigInteger('question_id')->change();
        });
    }
};
