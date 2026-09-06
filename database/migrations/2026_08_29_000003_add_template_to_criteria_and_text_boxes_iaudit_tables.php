<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Both tables currently key off reference_id -> template_refs_iaudit.
     * Adding a plain `template` string lets the new importer scope
     * criteria/text-box rows by template (matching the Data sheet's
     * `Criteria` = 'Criteria'/'Text' rows) without touching that bridge.
     */
    public function up(): void
    {
        Schema::table('criteria_iaudit', function (Blueprint $table) {
            $table->string('template')->nullable()->index()->after('criteria_id');
        });

        Schema::table('text_boxes_iaudit', function (Blueprint $table) {
            $table->string('template')->nullable()->index()->after('text_box_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criteria_iaudit', function (Blueprint $table) {
            $table->dropColumn('template');
        });

        Schema::table('text_boxes_iaudit', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
