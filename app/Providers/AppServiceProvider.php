<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

/**SE COMENTO PARA APRENDISAJE SOLAMENTE
 *  AppServiceProvider es el proveedor de servicios principal de la aplicación
 * 
 * Un "Service Provider" es un lugar central donde registras y configuras servicios/dependencias
 * que tu aplicación necesita al iniciar.
 * 
 * Este es el provider más importante y se ejecuta siempre cuando Laravel inicia.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     *  Método REGISTER - Registra servicios en el contenedor de inyección de dependencias
     * 
     * Aquí defines servicios que serán inyectados en otros lugares de tu app.
     * Ejemplo: bindear interfaces a clases concretas, registrar singletons, etc.
     * 
     * Se ejecuta PRIMERO durante el bootstrap de la aplicación.
     * En este caso está vacío porque no hay servicios personalizados para registrar.
     */
    public function register(): void
    {
        // Aquí iría código como:
        // $this->app->singleton(NombreServicio::class, function () {
        //     return new NombreServicio();
        // });
    }

    /**
     *  Método BOOT - Inicializa/configura servicios y componentes de la aplicación
     * 
     * Se ejecuta DESPUÉS que todos los servicios están registrados.
     * Aquí configuras comportamientos globales, rutas, vistas, eventos, etc.
     * 
     * Este es el método que se ejecuta últimamente, cuando la aplicación está lista.
     */
    public function boot(): void
    {
        /**
         *  Vite::prefetch() - Optimiza la carga de assets (CSS, JS) del navegador
         * 
         * Vite es el bundler/compilador de assets de Laravel (reemplaza Webpack)
         * 
         * prefetch() indica al navegador que "precargue" recursos en paralelo
         * concurrency: 3 significa que carga máximo 3 archivos simultáneamente
         * 
         * Beneficio: Los assets se descargan más rápido sin sobrecargar la conexión
         * 
         * Ejemplo sin prefetch:
         *   - Carga archivo 1 → Espera → Carga archivo 2 → Espera → Carga archivo 3
         * 
         * Ejemplo con prefetch(concurrency: 3):
         *   - Carga archivos 1, 2, 3 AL MISMO TIEMPO (máximo 3)
         */
        Vite::prefetch(concurrency: 3);
    }
}