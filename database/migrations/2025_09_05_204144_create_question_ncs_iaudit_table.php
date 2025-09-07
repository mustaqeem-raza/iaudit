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
        Schema::create('question_ncs_iaudit', function (Blueprint $table) {
            $table->id(); // internal PK
            $table->bigInteger('question_id')->nullable(); // FK -> questions_iaudit.question_id

            $table->string('nc_heading')->nullable();
            $table->text('nc_text')->nullable();
            $table->string('nc_rem_hd')->nullable();
            $table->text('nc_rem_text')->nullable();
            $table->string('nc_con_hd')->nullable();
            $table->text('nc_con_text')->nullable();
            $table->string('nc_usph_hd')->nullable();
            $table->text('nc_usph_text')->nullable();
            $table->string('nc_ipm_hd')->nullable();
            $table->string('nc_ipm_ref')->nullable();

            $table->foreign('question_id')
                ->references('question_id')->on('questions_iaudit')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_ncs_iaudit');
    }
};
