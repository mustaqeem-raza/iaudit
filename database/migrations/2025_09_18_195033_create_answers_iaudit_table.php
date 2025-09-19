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
        Schema::create('answers_iaudit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ship_id');
            $table->unsignedBigInteger('question_id');
            $table->enum('answer', ['Yes', 'No', 'N/A']);
            $table->text('note')->nullable();
            $table->json('files')->nullable(); 
            $table->timestamps();

            // indexes & FK constraints (optional - adjust table names if different)
            $table->index('user_id');
            $table->index('ship_id');
            $table->index('question_id');

            // Add foreign keys if you want referential integrity (uncomment if those tables exist)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ship_id')->references('id')->on('ships')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions_iaudit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers_iaudit');
    }
};
