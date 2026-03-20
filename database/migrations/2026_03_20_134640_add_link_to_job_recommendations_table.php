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
            $table->text('link')->nullable()->after('matched_skills');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_recommendations', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
