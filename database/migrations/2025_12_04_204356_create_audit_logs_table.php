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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Usuario que realizó la acción
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name'); // Guardamos el nombre por si se elimina el usuario
            $table->string('user_email');
            $table->string('user_role');
            
            // Acción realizada
            $table->string('action'); // 'aprobar_reserva', 'rechazar_reserva', 'crear_incidencia', etc.
            $table->string('description'); // Descripción legible de la acción
            
            // Modelo afectado (polymorphic)
            $table->string('auditable_type')->nullable(); // App\Models\Reserva, App\Models\Incidencia
            $table->unsignedBigInteger('auditable_id')->nullable(); // ID del modelo
            
            // Valores antes y después (JSON)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Información adicional
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['user_id', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};