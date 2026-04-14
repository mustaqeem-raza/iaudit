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
        Schema::create('other_crt_iaudit', function (Blueprint $table) {
            $table->id();
            $table->string('other_crt_ref')->nullable();
            $table->string('other_crt_main_heading')->nullable();
            $table->integer('other_crt_ordinal')->nullable();
            $table->string('other_crt_type_mnemonic')->nullable();
            $table->string('other_crt_type')->nullable();
            $table->string('other_crt_compliance')->nullable();  
            $table->text('other_crt_logic')->nullable();         
            $table->string('other_crt_sub_heading')->nullable();
            $table->string('other_crt_category')->nullable();
            $table->string('other_crt_sub_category')->nullable();
            $table->text('other_crt_non_compliance_text')->nullable();
            $table->text('other_crt_i_info')->nullable();
            $table->string('other_crt_usph_ref')->nullable();    
            $table->string('other_crt_ship_san_ref')->nullable();
            $table->string('other_crt_anvia_ref')->nullable();   
            $table->string('other_crt_mpi_ref')->nullable();     
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_crt_iaudit');
    }
};
