<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom Cast para RUT Chileno
 * 
 * Formatea automáticamente los RUTs chilenos al obtenerlos de la base de datos
 * y los limpia al guardarlos (removiendo puntos y guiones).
 * 
 * Ejemplo:
 * - Al guardar: "12.345.678-9" se convierte en "123456789"
 * - Al obtener: "123456789" se muestra como "12.345.678-9"
 */
class RutChileno implements CastsAttributes
{
    /**
     * Transforma el valor al obtenerlo de la base de datos
     * Formatea el RUT con puntos y guión
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (!$value || strlen($value) < 8) {
            return $value ?? '';
        }
        
        $cuerpo = substr($value, 0, -1);
        $dv = substr($value, -1);
        
        return number_format((int)$cuerpo, 0, '', '.') . '-' . $dv;
    }

    /**
     * Transforma el valor al guardarlo en la base de datos
     * Limpia el RUT removiendo puntos y guiones
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (!$value) {
            return '';
        }
        
        // Limpia el RUT removiendo puntos, guiones y espacios
        return preg_replace('/[^0-9kK]/', '', $value);
    }
}
