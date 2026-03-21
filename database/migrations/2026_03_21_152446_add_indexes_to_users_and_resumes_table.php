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
        Schema::table('users', function (Blueprint $table) {
            $table->index('api_token');
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('is_traning_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['api_token']);
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['is_traning_data']);
        });
    }
};
