<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('user_library_id');
            $table->integer('page_read');
            $table->timestamp('recorded_at');
            $table->foreign('user_library_id')->references('id')->on('user_library')->onDelete('cascade');
            $table->primary(['user_library_id', 'page_read', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
