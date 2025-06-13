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
        Schema::create('reading_progress', function (Blueprint $table) {
            // PERBAIKAN 1: Tambahkan primary key auto-increment standar.
            $table->id();

            // PERBAIKAN 2: Tambahkan kolom user_id untuk efisiensi query statistik.
            $table->string('user_id');
            $table->foreign('user_id')->references('uid')->on('local_users')->onDelete('cascade');

            $table->unsignedBigInteger('user_library_id');
            $table->foreign('user_library_id')->references('id')->on('user_library')->onDelete('cascade');

            $table->integer('page_read');
            $table->timestamp('recorded_at');

            // PERBAIKAN 3: Tambahkan kolom created_at dan updated_at standar.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
