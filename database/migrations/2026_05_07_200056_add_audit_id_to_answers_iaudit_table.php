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
        Schema::table('answers_iaudit', function (Blueprint $table) {
            $table->foreignId('audit_id')->nullable()->constrained('audits')->onDelete('cascade');
            $table->index('audit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_iaudit', function (Blueprint $table) {
            $table->dropConstrainedForeignId('audit_id');
        });
    }
};
