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
            $table->foreignId('job_id')->nullable()->after('parsed_data_id')->constrained('jobs')->onDelete('cascade');
            $table->string('company_name')->nullable()->after('job_title');
            $table->json('matched_skills')->nullable()->after('match_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn(['job_id', 'company_name', 'matched_skills']);
        });
    }
};
