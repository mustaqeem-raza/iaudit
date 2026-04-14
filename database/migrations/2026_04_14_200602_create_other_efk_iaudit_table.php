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
        Schema::create('other_efk_iaudit', function (Blueprint $table) {
            $table->id();

            $table->string('other_efk_ref')->nullable();
            $table->string('other_efk_main_heading')->nullable();
            $table->integer('other_efk_ordinal')->nullable();
            $table->string('other_efk_type_mnemonic')->nullable();
            $table->string('other_efk_type')->nullable();

            $table->string('other_efk_compliance')->nullable();
            $table->text('other_efk_logic')->nullable();

            $table->string('other_efk_sub_heading')->nullable();
            $table->string('other_efk_category')->nullable();
            $table->string('other_efk_sub_category')->nullable();

            $table->text('other_efk_non_compliance_text')->nullable();
            $table->text('other_efk_i_info')->nullable();

            $table->string('other_efk_usph_ref')->nullable();
            $table->string('other_efk_ship_san_ref')->nullable();
            $table->string('other_efk_anvia_ref')->nullable();
            $table->string('other_efk_mpi_ref')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_efk_iaudit');
    }
};
