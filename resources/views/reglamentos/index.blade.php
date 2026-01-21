@extends('layouts.app')

@section('title', 'Reglamentos - Sistema de Reservas Deportivas')

@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
    <!-- Header de la página -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 flex items-center justify-center">
            <svg class="w-8 h-8 mr-3 text-primary" fill="currentColor" viewBox="0 0 20 20">
            </svg>
            Reglamentos de Uso
        </h1>
        <p class="text-gray-600">
            Conoce las normas y regulaciones para el uso de nuestros recintos deportivos
        </p>
    </div>

    <!-- Sistema de Tabs -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Navegación de Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex flex-wrap -mb-px" id="tabs-nav">
                <button onclick="showTab('normas')" 
                        class="tab-btn active px-6 py-4 text-sm font-medium border-b-2 flex items-center"
                        data-tab="normas">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Normas Generales
                </button>
                <button onclick="showTab('horarios')" 
                        class="tab-btn px-6 py-4 text-sm font-medium border-b-2 flex items-center"
                        data-tab="horarios">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    Horarios
                </button>
                <button onclick="showTab('uso')" 
                        class="tab-btn px-6 py-4 text-sm font-medium border-b-2 flex items-center"
                        data-tab="uso">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Uso de Canchas
                </button>
                <button onclick="showTab('prohibiciones')" 
                        class="tab-btn px-6 py-4 text-sm font-medium border-b-2 flex items-center"
                        data-tab="prohibiciones">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Prohibiciones
                </button>
            </nav>
        </div>

        <!-- Contenido de Tabs -->
        <div class="p-6">
            <!-- Tab: Normas Generales -->
            <div id="tab-normas" class="tab-content">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Normas Generales</h2>
                <div class="space-y-4 text-gray-700">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r">
                        <p class="font-semibold text-blue-800">Importante</p>
                        <p>Es responsabilidad del usuario leer las normas generales del uso del recinto, con la finalidad de respetar las reglas y mantener mejor organización y convivencia.</p>
                    </div>
                    
                    <ul class="space-y-3 ml-4">
                        <li>Las reservas deben solicitarse con al menos <strong>24 horas de anticipación</strong>.</li>
                        <li>Todas las solicitudes requieren <strong>aprobación del jefe de recintos</strong>.</li>
                        <li>Se solicita mantener el <strong>respeto por los funcionarios</strong> del recinto.</li>
                        <li>El usuario deberá presentar <strong>identificación vigente</strong> al momento de ingresar.</li>
                        <li>Cada reserva tiene una <strong>tolerancia de 30 minutos</strong> desde la hora de inicio. Pasado ese tiempo, la reserva puede ser liberada.</li>
                        <li>Es obligatorio dejar el recinto en las <strong>mismas condiciones</strong> en que fue entregado.</li>
                        <li>En caso de daños a las instalaciones, el usuario deberá <strong>asumir los costos de reparación</strong>.</li>
                        <li>Recibirá <strong>confirmación por correo electrónico</strong> una vez aprobada su reserva.</li>
                    </ul>
                </div>
            </div>

            <!-- Tab: Horarios -->
            <div id="tab-horarios" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Horarios de Funcionamiento</h2>
                <div class="space-y-6 text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                            <h3 class="font-bold text-green-800 text-lg mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Horario Regular
                            </h3>
                            <p class="text-lg"><strong>Lunes a Domingo:</strong> 08:00 - 23:00 hrs</p>
                        </div>
                        
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-5">
                            <h3 class="font-bold text-orange-800 text-lg mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Días de Mantenimiento
                            </h3>
                            <p>Algunos recintos pueden estar cerrados por mantenimiento. Consulte el calendario para ver disponibilidad.</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                        <h3 class="font-bold text-gray-800 text-lg mb-3">Duración de las Reservas</h3>
                        <ul class="space-y-2">
                            <li>Cada bloque horario tiene una duración de <strong>1 hora</strong>.</li>
                            <li>Se pueden reservar bloques consecutivos según disponibilidad.</li>
                            <li>El máximo de bloques por reserva es de <strong>4 horas continuas</strong>.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tab: Uso de Canchas -->
            <div id="tab-uso" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Uso de Canchas y Recintos</h2>
                <div class="space-y-6 text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <h3 class="font-bold text-primary text-lg mb-3">Epicentro 1 y 2</h3>
                            <ul class="space-y-2 text-sm">
                                <li>Canchas multiuso para fútbol, básquetbol, vóleibol</li>
                                <li>Uso de calzado deportivo adecuado</li>
                                <li>Capacidad máxima según tipo de actividad</li>
                            </ul>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <h3 class="font-bold text-primary text-lg mb-3">Fortín Sotomayor</h3>
                            <ul class="space-y-2 text-sm">
                                <li>Cancha de césped sintético</li>
                                <li>Prohibido el uso de zapatos con tacos metálicos</li>
                                <li>Ideal para fútbol y eventos deportivos</li>
                            </ul>
                        </div>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
                            <h3 class="font-bold text-primary text-lg mb-3">Piscina Olímpica</h3>
                            <ul class="space-y-2 text-sm">
                                <li>Uso obligatorio de gorro de natación</li>
                                <li>Ducharse antes de ingresar a la piscina</li>
                                <li>Cerrado los lunes por mantenimiento</li>
                            </ul>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-5">
                            <h3 class="font-bold text-blue-800 text-lg mb-3">Equipamiento</h3>
                            <p class="text-sm">Los usuarios deben traer su propio equipamiento deportivo (balones, raquetas, etc.). El recinto no se hace responsable por objetos perdidos.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Prohibiciones -->
            <div id="tab-prohibiciones" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">Prohibiciones</h2>
                <div class="space-y-4">
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                        <p class="font-semibold text-red-800 mb-2">Está estrictamente prohibido:</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start space-x-3 p-4 bg-white border border-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Bebidas Alcohólicas</p>
                                <p class="text-sm text-gray-600">Está prohibido el ingreso y consumo de alcohol en todas las instalaciones.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-white border border-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Fumar</p>
                                <p class="text-sm text-gray-600">Prohibido fumar en todas las áreas del recinto, incluyendo zonas exteriores.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-white border border-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Mascotas</p>
                                <p class="text-sm text-gray-600">No se permite el ingreso de mascotas a las instalaciones deportivas.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-white border border-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Comercio Ambulante</p>
                                <p class="text-sm text-gray-600">No está permitida la venta ambulante dentro de las instalaciones.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-white border border-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Conductas Violentas</p>
                                <p class="text-sm text-gray-600">Cualquier acto de violencia resultará en expulsión inmediata y prohibición de futuras reservas.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3 p-4 bg-white border border-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Daños a Instalaciones</p>
                                <p class="text-sm text-gray-600">Está prohibido dañar equipamiento, mobiliario o infraestructura del recinto.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Contacto -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            ¿Tienes dudas?
        </h3>
        <p class="text-blue-700 mb-4">Si tienes consultas sobre los reglamentos o el uso de las instalaciones, no dudes en contactarnos:</p>
        <div class="flex flex-wrap gap-4">
            <a href="tel:+56582205500" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                </svg>
                +56 58 2205500
            </a>
            <a href="mailto:reservas@muniarica.cl" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                reservas@muniarica.cl
            </a>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-primary', 'text-primary');
        btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Mostrar el contenido seleccionado
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    
    // Activar el botón seleccionado
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.add('active', 'border-primary', 'text-primary');
    activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
}

// Estilos iniciales para tabs
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (!btn.classList.contains('active')) {
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        } else {
            btn.classList.add('border-primary', 'text-primary');
        }
    });
});
</script>
@endsection
