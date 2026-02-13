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
        // Add 'informe' to the tipo enum
        DB::statement("ALTER TABLE incidencias MODIFY COLUMN tipo ENUM('problema_posuso', 'dano', 'otro', 'informe') NOT NULL DEFAULT 'otro'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE incidencias MODIFY COLUMN tipo ENUM('problema_posuso', 'dano', 'otro') NOT NULL DEFAULT 'otro'");
    }
};
