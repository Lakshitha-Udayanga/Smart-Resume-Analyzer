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
        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->decimal('match_score', 22, 2)->nullable()->after('job_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->dropColumn('match_score');
        });
    }
};
