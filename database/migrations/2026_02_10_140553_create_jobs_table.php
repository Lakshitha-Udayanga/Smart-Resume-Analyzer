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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('company_name')->nullable();
            $table->string('category')->nullable();
            $table->enum('job_type', ['Full-Time', 'Part-Time', 'Intern', 'Contract']);
            $table->string('location')->nullable();
            $table->decimal('salary_min', 24, 2)->nullable();
            $table->decimal('salary_max', 24, 2)->nullable();
            $table->string('experience_level')->nullable();
            $table->text('skills')->nullable(); // PHP, Laravel, MySQL
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
