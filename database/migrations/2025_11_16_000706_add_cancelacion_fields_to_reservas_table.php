<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('codigo_cancelacion', 32)->unique()->nullable()->after('acepta_reglamento');
            $table->timestamp('fecha_cancelacion')->nullable()->after('codigo_cancelacion');
            $table->foreignId('cancelada_por')->nullable()->constrained('users')->onDelete('set null')->after('fecha_cancelacion');
            $table->text('motivo_cancelacion')->nullable()->after('cancelada_por');
            $table->boolean('cancelada_por_usuario')->default(false)->after('motivo_cancelacion');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['cancelada_por']);
            $table->dropColumn([
                'codigo_cancelacion',
                'fecha_cancelacion',
                'cancelada_por',
                'motivo_cancelacion',
                'cancelada_por_usuario'
            ]);
        });
    }
};