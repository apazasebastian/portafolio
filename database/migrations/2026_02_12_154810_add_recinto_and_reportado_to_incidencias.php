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
        Schema::table('incidencias', function (Blueprint $table) {
            // Make reserva_id nullable (informes don't reference a reservation)
            $table->unsignedBigInteger('reserva_id')->nullable()->change();
            
            // Add recinto_id for direct recinto reference (used by informes)
            $table->unsignedBigInteger('recinto_id')->nullable()->after('reserva_id');
            $table->foreign('recinto_id')->references('id')->on('recintos')->onDelete('cascade');
            
            // Add reportado_por to store the email of who filed the report
            $table->string('reportado_por')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropForeign(['recinto_id']);
            $table->dropColumn(['recinto_id', 'reportado_por']);
            $table->unsignedBigInteger('reserva_id')->nullable(false)->change();
        });
    }
};
