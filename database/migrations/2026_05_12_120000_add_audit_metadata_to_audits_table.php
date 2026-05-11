<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->string('consultant')->nullable()->after('ship_id');
            $table->string('consultant_position')->nullable()->after('consultant');
            $table->timestamp('submitted_at')->nullable()->after('consultant_position');
            $table->text('notes')->nullable()->after('submitted_at');

            $table->string('pcro_name')->nullable()->after('notes');
            $table->string('pcro_position')->nullable()->after('pcro_name');

            $table->string('pco_name')->nullable()->after('pcro_position');
            $table->string('pco_position')->nullable()->after('pco_name');

            $table->string('pic_name')->nullable()->after('pco_position');
            $table->string('pic_position')->nullable()->after('pic_name');

            $table->string('port_from')->nullable()->after('pic_position');
            $table->string('port_to')->nullable()->after('port_from');

            $table->date('date_from')->nullable()->after('port_to');
            $table->date('date_to')->nullable()->after('date_from');
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn([
                'consultant',
                'consultant_position',
                'submitted_at',
                'notes',
                'pcro_name',
                'pcro_position',
                'pco_name',
                'pco_position',
                'pic_name',
                'pic_position',
                'port_from',
                'port_to',
                'date_from',
                'date_to',
            ]);
        });
    }
};
