<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_library', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->foreign('user_id')->references('uid')->on('local_users')->onDelete('cascade');
            $table->unsignedBigInteger('book_id');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->enum('status', ['PLAN_TO_READ', 'READING', 'FINISH'])->default('PLAN_TO_READ');
            $table->integer('last_page_read')->nullable();
            $table->timestamp('updated_at');
            $table->float('rating')->nullable();
            $table->index(['user_id']);
            $table->index(['book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_library');
    }
};
