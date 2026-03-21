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
        Schema::create('traning_data_sets', function (Blueprint $table) {
            $table->id();
            $table->text('certificates')->nullable();
            $table->text('experiences')->nullable();
            $table->text('skills')->nullable();
            $table->text('matching_job_list')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traning_data_sets');
    }
};
