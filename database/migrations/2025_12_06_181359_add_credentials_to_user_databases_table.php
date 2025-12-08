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
        Schema::table('user_databases', function (Blueprint $table) {
            $table->string('db_username')->nullable()->after('name');
            $table->string('db_password')->nullable()->after('db_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_databases', function (Blueprint $table) {
            $table->dropColumn(['db_username', 'db_password']);
        });
    }
};
