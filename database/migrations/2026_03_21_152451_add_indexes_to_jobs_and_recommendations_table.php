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
        Schema::table('jobs', function (Blueprint $table) {
            $table->index('title');
            $table->index('company_name');
            $table->index('category');
            $table->index('job_type');
            $table->index('location');
            $table->index('experience_level');
            $table->index('status');
            $table->index('post_date');
            $table->index('closing_date');
        });

        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->index('parsed_data_id');
            $table->index('job_title');
            $table->index('match_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['company_name']);
            $table->dropIndex(['category']);
            $table->dropIndex(['job_type']);
            $table->dropIndex(['location']);
            $table->dropIndex(['experience_level']);
            $table->dropIndex(['status']);
            $table->dropIndex(['post_date']);
            $table->dropIndex(['closing_date']);
        });

        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
            $table->dropIndex(['job_title']);
            $table->dropIndex(['match_score']);
        });
    }
};
