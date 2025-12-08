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
        Schema::table('domains', function (Blueprint $table) {
            $table->string('ftp_host')->nullable();
            $table->string('ftp_username')->nullable();
            $table->text('ftp_password')->nullable();
            $table->string('cyberpanel_website_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['ftp_host', 'ftp_username', 'ftp_password', 'cyberpanel_website_id']);
        });
    }
};
