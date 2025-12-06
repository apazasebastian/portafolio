<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidTelefonoChileno implements Rule
{
    /**
     * Determina si la validación pasa.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Eliminar espacios, guiones y otros caracteres comunes
        $telefono = preg_replace('/[\s\-\(\)\.]+/', '', $value);
        
        // Eliminar el prefijo +56 o 56 si existe
        $telefono = preg_replace('/^\+?56/', '', $telefono);
        
        // Validar formato de celular chileno
        // Los celulares chilenos empiezan con 9 y tienen 9 dígitos en total
        // Ejemplos válidos: 912345678, 987654321
        if (preg_match('/^9\d{8}$/', $telefono)) {
            return true;
        }
        
        // También aceptar teléfonos fijos (8 dígitos, empieza con 2-7)
        // Ejemplos: 221234567 (Santiago), 552345678 (regiones)
        if (preg_match('/^[2-7]\d{7,8}$/', $telefono)) {
            return true;
        }
        
        return false;
    }

    /**
     * Obtener el mensaje de error de validación.
     *
     * @return string
     */
    public function message()
    {
        return 'El número de teléfono debe ser un número chileno válido (ej: +56 9 1234 5678 o 912345678).';
    }
}