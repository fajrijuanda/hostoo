<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 10 GB in bytes = 10 * 1024 * 1024 * 1024 = 10737418240
        $limit = 10737418240;
        
        DB::table('users')->update(['storage_limit' => $limit]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy reverse for data update, but we could set it back to 50GB if we wanted
        // 50 GB = 53687091200
        // But for all users? Might be risky. Leaving empty or setting to previous default.
    }
};
