<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Discovered while writing the Week 1 importer: unlike text_boxes_iaudit
     * (which already has a reusable `reference_code` string column),
     * criteria_iaudit has no column that can hold the Data sheet's
     * Short_Code as a natural dedup key. Without one, re-running the import
     * can't reliably upsert criteria rows (only insert-or-guess), breaking
     * the "re-import is idempotent" requirement. Additive, nullable —
     * table has only 39 rows, no relation depends on this.
     */
    public function up(): void
    {
        Schema::table('criteria_iaudit', function (Blueprint $table) {
            $table->string('short_code')->nullable()->index()->after('template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criteria_iaudit', function (Blueprint $table) {
            $table->dropColumn('short_code');
        });
    }
};
