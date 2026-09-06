<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Additive only, all nullable — reuses question_ncs_iaudit as "extra
     * report text per question" (it already plays that role for the old
     * nc_* columns) rather than growing questions_iaudit further.
     */
    public function up(): void
    {
        Schema::table('question_ncs_iaudit', function (Blueprint $table) {
            $table->text('responsibility')->nullable();
            $table->string('consultant_remark')->nullable();
            $table->string('vsp_item_no')->nullable();
            $table->unsignedSmallInteger('point_loss')->nullable();
            $table->string('vsp_reference')->nullable();
            $table->string('vsp_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_ncs_iaudit', function (Blueprint $table) {
            $table->dropColumn([
                'responsibility',
                'consultant_remark',
                'vsp_item_no',
                'point_loss',
                'vsp_reference',
                'vsp_description',
            ]);
        });
    }
};
