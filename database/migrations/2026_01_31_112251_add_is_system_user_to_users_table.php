<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('is_system_user')->default(0)->after('phone');


            User::create([
                'name' => 'lakshitha udayanga',
                'phone' => '0775255226',
                'email' => 'lakshitha@info.lk',
                'password' => Hash::make('12345678'),
                'is_system_user' => 1
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_system_user');
        });
    }
};
