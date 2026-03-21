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
        Schema::table('parsed_data', function (Blueprint $table) {
            $table->index('resume_id');
        });

        Schema::table('strengths', function (Blueprint $table) {
            $table->index('parsed_data_id');
        });

        Schema::table('weaknesses', function (Blueprint $table) {
            $table->index('parsed_data_id');
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->index('parsed_data_id');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->index('parsed_data_id');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->index('parsed_data_id');
        });

        Schema::table('soft_skills', function (Blueprint $table) {
            $table->index('parsed_data_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parsed_data', function (Blueprint $table) {
            $table->dropIndex(['resume_id']);
        });

        Schema::table('strengths', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
        });

        Schema::table('weaknesses', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
        });

        Schema::table('soft_skills', function (Blueprint $table) {
            $table->dropIndex(['parsed_data_id']);
        });
    }
};
