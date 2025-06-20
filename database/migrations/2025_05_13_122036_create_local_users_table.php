<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('local_users', function (Blueprint $table) {
            $table->string('uid')->primary();
            $table->string('email');
            $table->string('username');
            $table->string('name');
            $table->string('profile_image')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('local_users');
    }
};
