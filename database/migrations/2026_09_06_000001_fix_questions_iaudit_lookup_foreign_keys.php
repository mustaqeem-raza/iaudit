<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two independent fixes, bundled because the second one only becomes
     * possible once the first lands:
     *
     * 1. questions_iaudit.department_id is currently a raw VARCHAR holding
     *    the Excel sheet's department text ("Provisions", "Deck", ...)
     *    verbatim — never resolved against departments_iaudit at all. Every
     *    imported question effectively stores its own copy of the
     *    department name instead of referencing the one canonical row,
     *    which is exactly the redundancy the new single-tab import is
     *    supposed to reduce, not reproduce. This backfills every existing
     *    value to a real departments_iaudit.department_id (creating the
     *    department row if the sheet introduced a name that didn't exist
     *    yet — the same get-or-create policy already used for
     *    heading/subheading/category), then swaps the column to a proper
     *    bigint so it can carry a real foreign key.
     *
     * 2. Confirmed via `SHOW CREATE TABLE questions_iaudit` on the live DB:
     *    heading_id/subheading_id/category_id were declared with
     *    ->foreign() back in the original create_questions_iaudit_table
     *    migration, but none of the three ever actually became enforced
     *    constraints — only plain, unenforced indexes exist today (no
     *    CONSTRAINT ... FOREIGN KEY clause at all). Zero orphaned rows exist
     *    against any of the three (checked directly before writing this),
     *    so it's safe to add the real constraints now rather than carry the
     *    gap forward. reference_id is deliberately left alone: it's unused
     *    by the new importer and points at template_refs_iaudit, a table
     *    already slated for retirement (schema-update-minimal-approach.md
     *    §2.4) — enforcing a FK there now would be effort spent on a column
     *    on its way out.
     */
    public function up(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->bigInteger('department_id_new')->nullable()->after('department_id');
        });

        $distinctNames = DB::table('questions_iaudit')
            ->whereNotNull('department_id')
            ->distinct()
            ->pluck('department_id');

        foreach ($distinctNames as $rawName) {
            $name = trim((string) $rawName);
            if ($name === '') {
                continue;
            }

            // Collation on this table is utf8mb4_unicode_ci (confirmed live)
            // — a plain `=` match is already case-insensitive, same lookup
            // the importer's other get-or-create helpers rely on.
            $department = DB::table('departments_iaudit')->where('name', $name)->first();

            if ($department) {
                $departmentId = $department->department_id;
            } else {
                $departmentId = (int) (DB::table('departments_iaudit')->max('department_id') ?? 0) + 1;
                DB::table('departments_iaudit')->insert([
                    'department_id' => $departmentId,
                    'name'          => $name,
                ]);
            }

            DB::table('questions_iaudit')
                ->where('department_id', $rawName)
                ->update(['department_id_new' => $departmentId]);
        }

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->renameColumn('department_id_new', 'department_id');
        });

        // The old plain indexes share the exact names Laravel's ->foreign()
        // would default to below — drop them first or the ADD collides.
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->dropIndex('questions_iaudit_heading_id_foreign');
            $table->dropIndex('questions_iaudit_subheading_id_foreign');
            $table->dropIndex('questions_iaudit_category_id_foreign');
        });

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('department_id')->on('departments_iaudit')
                ->cascadeOnUpdate()->nullOnDelete();

            $table->foreign('heading_id')
                ->references('heading_id')->on('headings_iaudit')
                ->cascadeOnUpdate()->nullOnDelete();

            $table->foreign('subheading_id')
                ->references('subheading_id')->on('sub_headings_iaudit')
                ->cascadeOnUpdate()->nullOnDelete();

            $table->foreign('category_id')
                ->references('category_id')->on('categories_iaudit')
                ->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Best-effort reverse: restores the plain VARCHAR shape and drops the
     * new constraints. Does not attempt to reconstruct the pre-migration
     * plain (unenforced) indexes on heading/subheading/category — dropping
     * just the FK constraint by name leaves their backing index in place,
     * which is the same state they were in before this migration.
     */
    public function down(): void
    {
        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['heading_id']);
            $table->dropForeign(['subheading_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->string('department_id_old')->nullable()->after('department_id');
        });

        DB::table('questions_iaudit')
            ->join('departments_iaudit', 'departments_iaudit.department_id', '=', 'questions_iaudit.department_id')
            ->update(['questions_iaudit.department_id_old' => DB::raw('departments_iaudit.name')]);

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });

        Schema::table('questions_iaudit', function (Blueprint $table) {
            $table->renameColumn('department_id_old', 'department_id');
        });
    }
};
