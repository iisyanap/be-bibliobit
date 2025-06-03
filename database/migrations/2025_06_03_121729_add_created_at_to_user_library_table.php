<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtToUserLibraryTable extends Migration
{
    public function up()
    {
        Schema::table('user_library', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable(); // Tambahkan kolom created_at
        });
    }

    public function down()
    {
        Schema::table('user_library', function (Blueprint $table) {
            $table->dropColumn('created_at');
        });
    }
}