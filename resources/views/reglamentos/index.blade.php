@extends('layouts.app')

@section('title', 'Reglamentos - Sistema de Reservas Deportivas')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gray-900 text-white overflow-hidden">
    <!-- Background con imagen y gradiente -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/reglamentos-hero.jpg') }}" alt="Background" class="w-full h-full object-cover object-center opacity-1">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/95 via-gray-900/80 to-gray-900"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28 text-center">
        <div class="inline-flex items-center justify-center p-1 mb-6 bg-white/10 backdrop-blur-sm border border-white/20">
            <span class="px-4 py-1 text-xs font-semibold uppercase tracking-widest text-white">Protocolos y Normativa</span>
        </div>
        
        <h1 class="text-5xl md:text-6xl font-serif font-bold mb-6 tracking-tight">Reglamentos de Uso</h1>
        
        <p class="text-xl text-gray-300 max-w-2xl mx-auto italic font-light">
            "La disciplina es el puente entre las metas y los logros."
            <span class="block mt-2 text-sm text-gray-500 not-italic uppercase tracking-widest font-semibold">— Excelencia en cada práctica</span>
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-6 lg:px-8 -mt-8 relative z-20 pb-20">
    <!-- Sistema de Tabs -->
    <div class="bg-white shadow-xl overflow-hidden mb-12">
        <!-- Navegación de Tabs -->
        <div class="border-b border-gray-100 flex justify-center">
            <nav class="flex overflow-x-auto py-4 px-6 space-x-8" id="tabs-nav">
                <button onclick="showTab('normas')" 
                        class="tab-btn active text-sm font-semibold tracking-wide uppercase py-2 border-b-2 border-primary text-primary transition-colors"
                        data-tab="normas">
                    Normas Generales
                </button>
                <button onclick="showTab('horarios')" 
                        class="tab-btn text-sm font-semibold tracking-wide uppercase py-2 border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-colors"
                        data-tab="horarios">
                    Horarios de Uso
                </button>
                <button onclick="showTab('uso')" 
                        class="tab-btn text-sm font-semibold tracking-wide uppercase py-2 border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-colors"
                        data-tab="uso">
                    Uso de Canchas
                </button>
                <button onclick="showTab('prohibiciones')" 
                        class="tab-btn text-sm font-semibold tracking-wide uppercase py-2 border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-colors"
                        data-tab="prohibiciones">
                    Prohibiciones
                </button>
            </nav>
        </div>

        <!-- Contenido de Tabs -->
        <div class="p-8 lg:p-12 min-h-[600px] bg-white">
            <!-- Tab: Normas Generales (Timeline) -->
            <div id="tab-normas" class="tab-content">
                <div class="relative">
                    <!-- Línea Vertical Central -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-px bg-gray-200 hidden lg:block"></div>

                    <!-- Item 1: Anticipación -->
                    <div class="relative flex flex-col lg:flex-row items-center mb-24 group">
                        <!-- Número de Fondo -->
                        <div class="absolute top-0 right-1/2 translate-x-1/2 -mt-16 text-[120px] font-bold text-gray-50 opacity-50 select-none z-0 hidden lg:block font-serif">01</div>
                        
                        <!-- Contenido Texto (Izquierda) -->
                        <div class="w-full lg:w-1/2 lg:pr-16 text-center lg:text-right relative z-10 order-2 lg:order-1 mt-8 lg:mt-0">
                            <h3 class="text-3xl font-serif font-bold text-gray-900 mb-4">Anticipación</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Las reservas deben solicitarse con al menos <strong class="text-gray-900">24 horas de anticipación</strong> para asegurar la disponibilidad y preparación óptima del recinto.
                            </p>
                        </div>
                        
                        <!-- Punto Central -->
                        <div class="hidden lg:flex absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-900 rounded-full border-4 border-white shadow z-20"></div>
                        
                        <!-- Tarjeta Visual (Derecha) -->
                        <div class="w-full lg:w-1/2 lg:pl-16 relative z-10 order-1 lg:order-2">
                            <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow aspect-[4/3] flex flex-col items-center justify-center">
                                <div class="w-full h-full bg-white rounded-xl shadow-inner p-6 flex flex-col items-center justify-center border border-gray-100">
                                    <div class="grid grid-cols-7 gap-1 w-full max-w-[200px] opacity-20 mb-4">
                                        <!-- Mockup calendario simple -->
                                        <div class="h-3 bg-gray-400 rounded col-span-7 mb-2"></div>
                                        @for($i=0; $i<14; $i++) <div class="h-4 bg-gray-300 rounded"></div> @endfor
                                        <div class="h-4 bg-primary rounded col-span-1 ring-2 ring-offset-1 ring-primary"></div>
                                        @for($i=0; $i<13; $i++) <div class="h-4 bg-gray-300 rounded"></div> @endfor
                                    </div>
                                    <span class="text-xs font-bold tracking-widest text-gray-400 uppercase mt-4">Gestión de Tiempo</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: Aprobación (Invertido) -->
                    <div class="relative flex flex-col lg:flex-row items-center mb-24 group">
                         <!-- Número de Fondo -->
                         <div class="absolute top-0 right-1/2 translate-x-1/2 -mt-16 text-[120px] font-bold text-gray-50 opacity-50 select-none z-0 hidden lg:block font-serif">02</div>

                        <!-- Tarjeta Visual (Izquierda) -->
                        <div class="w-full lg:w-1/2 lg:pr-16 relative z-10 order-1">
                            <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow aspect-[4/3] flex flex-col items-center justify-center">
                                <div class="w-full h-full bg-gray-900 rounded-xl shadow-lg p-6 flex items-center justify-center relative overflow-hidden">
                                     <!-- Efecto firma abstracta -->
                                     <svg class="w-48 h-24 text-white/80" viewBox="0 0 200 100" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20,50 Q50,0 80,50 T140,50 T180,50" style="filter: drop-shadow(0 0 2px white);"/>
                                        <path d="M30,60 Q60,80 100,50" stroke-width="1" opacity="0.5"/>
                                     </svg>
                                    <span class="absolute bottom-4 left-6 text-[10px] font-bold tracking-widest text-white/40 uppercase">Validación Oficial</span>
                                </div>
                            </div>
                        </div>

                        <!-- Punto Central -->
                        <div class="hidden lg:flex absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-900 rounded-full border-4 border-white shadow z-20"></div>

                        <!-- Contenido Texto (Derecha) -->
                        <div class="w-full lg:w-1/2 lg:pl-16 text-center lg:text-left relative z-10 order-2 mt-8 lg:mt-0">
                            <h3 class="text-3xl font-serif font-bold text-gray-900 mb-4">Aprobación Superior</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Todas las solicitudes requieren la <strong class="text-gray-900">aprobación del jefe de recintos</strong> para ser validadas oficialmente en el sistema.
                            </p>
                        </div>
                    </div>

                    <!-- Item 3: Identificación -->
                    <div class="relative flex flex-col lg:flex-row items-center mb-24 group">
                        <div class="absolute top-0 right-1/2 translate-x-1/2 -mt-16 text-[120px] font-bold text-gray-50 opacity-50 select-none z-0 hidden lg:block font-serif">03</div>
                        
                        <div class="w-full lg:w-1/2 lg:pr-16 text-center lg:text-right relative z-10 order-2 lg:order-1 mt-8 lg:mt-0">
                            <h3 class="text-3xl font-serif font-bold text-gray-900 mb-4">Identificación y Respeto</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Se solicita presentar <strong class="text-gray-900">identificación vigente</strong> al ingresar y mantener el máximo respeto por los funcionarios del recinto.
                            </p>
                        </div>
                        
                        <div class="hidden lg:flex absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-900 rounded-full border-4 border-white shadow z-20"></div>
                        
                        <div class="w-full lg:w-1/2 lg:pl-16 relative z-10 order-1 lg:order-2">
                             <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow aspect-[4/3] flex flex-col items-center justify-center">
                                <div class="w-full h-full bg-gray-200 rounded-xl shadow-inner p-6 flex flex-col items-center justify-center overflow-hidden relative">
                                    <!-- Avatar abstracto -->
                                    <div class="w-24 h-24 bg-gray-300 rounded-full mb-4 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-tr from-gray-400 to-gray-200"></div>
                                    </div>
                                    <div class="w-32 h-4 bg-gray-300 rounded mb-2"></div>
                                    <div class="w-20 h-4 bg-gray-300 rounded"></div>
                                    <span class="absolute bottom-4 text-[10px] font-bold tracking-widest text-gray-500 uppercase">Protocolo de Entrada</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 4: Tolerancia (Invertido) -->
                    <div class="relative flex flex-col lg:flex-row items-center mb-24 group">
                        <div class="absolute top-0 right-1/2 translate-x-1/2 -mt-16 text-[120px] font-bold text-gray-50 opacity-50 select-none z-0 hidden lg:block font-serif">04</div>

                        <div class="w-full lg:w-1/2 lg:pr-16 relative z-10 order-1">
                             <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow aspect-[4/3] flex flex-col items-center justify-center">
                                <div class="w-full h-full bg-black rounded-xl shadow-lg p-6 flex items-center justify-center relative overflow-hidden">
                                    <!-- Reloj Abstracto -->
                                    <div class="w-32 h-32 rounded-full border-4 border-white/20 flex items-center justify-center relative">
                                        <div class="absolute top-0 w-1 h-3 bg-white/40"></div>
                                        <div class="absolute bottom-0 w-1 h-3 bg-white/40"></div>
                                        <div class="absolute left-0 w-3 h-1 bg-white/40"></div>
                                        <div class="absolute right-0 w-3 h-1 bg-white/40"></div>
                                        <div class="w-1 h-12 bg-white absolute top-4 origin-bottom rotate-45"></div>
                                        <div class="w-1 h-8 bg-primary absolute top-8 origin-bottom -rotate-12"></div>
                                        <div class="w-2 h-2 bg-white rounded-full z-10"></div>
                                    </div>
                                    <span class="absolute bottom-4 left-6 text-[10px] font-bold tracking-widest text-white/40 uppercase">Criterio de Tiempo</span>
                                </div>
                            </div>
                        </div>

                        <div class="hidden lg:flex absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-900 rounded-full border-4 border-white shadow z-20"></div>

                        <div class="w-full lg:w-1/2 lg:pl-16 text-center lg:text-left relative z-10 order-2 mt-8 lg:mt-0">
                            <h3 class="text-3xl font-serif font-bold text-gray-900 mb-4">Tolerancia y Liberación</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Contamos con una <strong class="text-gray-900">tolerancia de 30 minutos</strong>. Pasado este tiempo sin asistencia, la reserva podrá ser liberada para otros usuarios.
                            </p>
                        </div>
                    </div>
                
                    <!-- Item 5: Integridad -->
                    <div class="relative flex flex-col lg:flex-row items-center group">
                        <div class="absolute top-0 right-1/2 translate-x-1/2 -mt-16 text-[120px] font-bold text-gray-50 opacity-50 select-none z-0 hidden lg:block font-serif">05</div>
                        
                        <div class="w-full lg:w-1/2 lg:pr-16 text-center lg:text-right relative z-10 order-2 lg:order-1 mt-8 lg:mt-0">
                            <h3 class="text-3xl font-serif font-bold text-gray-900 mb-4">Integridad del Espacio</h3>
                            <p class="text-gray-600 leading-relaxed">
                                Es obligatorio dejar el recinto en las <strong class="text-gray-900">mismas condiciones</strong> en que fue entregado. Daños directos serán asumidos por el usuario responsable.
                            </p>
                        </div>
                        
                        <div class="hidden lg:flex absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-900 rounded-full border-4 border-white shadow z-20"></div>
                        
                        <div class="w-full lg:w-1/2 lg:pl-16 relative z-10 order-1 lg:order-2">
                             <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow aspect-[4/3] flex flex-col items-center justify-center">
                                <div class="w-full h-full bg-white rounded-xl shadow p-0 flex flex-col items-center justify-center overflow-hidden border border-gray-200 relative">
                                    <div class="absolute inset-x-0 bottom-0 top-1/3 bg-gray-100 border-t border-gray-200">
                                        <!-- Cancha abstracta -->
                                        <div class="absolute bottom-0 left-1/4 right-1/4 h-24 border-2 border-gray-300 border-b-0"></div>
                                        <div class="absolute bottom-0 left-[40%] right-[40%] h-12 border-2 border-gray-300 border-b-0 bg-white/50"></div>
                                    </div>
                                    <!-- Tablero -->
                                    <div class="absolute top-8 w-24 h-16 border-2 border-gray-800 bg-transparent/10 z-10 rounded-sm flex items-end justify-center">
                                        <div class="w-8 h-8 border-2 border-gray-800 border-t-0"></div>
                                    </div>
                                    <span class="absolute bottom-4 text-[10px] font-bold tracking-widest text-gray-400 uppercase">Responsabilidad</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Horarios -->
            <div id="tab-horarios" class="tab-content hidden max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Horarios de Funcionamiento</h2>
                    <p class="text-gray-500">Planifica tu visita conociendo nuestros horarios disponibles.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                     <!-- Card Horario -->
                     <div class="bg-white border p-8 shadow-sm hover:shadow-lg transition-shadow text-center">
                         <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                         </div>
                         <h3 class="font-bold text-gray-900 text-xl mb-2">Horario Regular</h3>
                         <p class="text-green-600 font-medium mb-4">Lunes a Domingo</p>
                         <p class="text-4xl font-bold text-gray-800">08:00 - 23:00</p>
                         <p class="text-sm text-gray-400 mt-2">Horario continuo</p>
                     </div>

                     <!-- Card Mantenimiento -->
                     <div class="bg-gray-50 border border-dashed border-gray-300 p-8 text-center flex flex-col justify-center">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                         </div>
                         <h3 class="font-bold text-gray-900 text-lg mb-2">Mantenimiento</h3>
                         <p class="text-gray-600 text-sm">Algunos recintos pueden estar cerrados temporalmente. Revisa el calendario de reservas.</p>
                     </div>
                </div>
            </div>

            <!-- Tab: Uso de Canchas -->
            <div id="tab-uso" class="tab-content hidden max-w-5xl mx-auto">
                 <div class="text-center mb-12">
                    <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Especificaciones por Recinto</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Recinto 1 -->
                    <div class="group relative overflow-hidden shadow-md hover:shadow-xl transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        <img src="{{ asset('storage/recintos/Tiw4joEeip2UAqTI5a5vieGWONNhIHzbY6zLkRyL.jpg') }}" alt="Epicentro 1 y 2" class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">Epicentro 1 y 2</h3>
                            <ul class="text-sm space-y-1 text-gray-200">
                                <li>• Multiuso (Fútbol, Básquet, Vóley)</li>
                                <li>• Calzado deportivo obligatorio</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Recinto 2 -->
                    <div class="group relative overflow-hidden shadow-md hover:shadow-xl transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        <img src="{{ asset('storage/recintos/n5piEMDrg5w7WSMZF8zZxIAWltDzX9zNDD2wRJBH.jpg') }}" alt="Fortín Sotomayor" class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">Fortín Sotomayor</h3>
                            <ul class="text-sm space-y-1 text-gray-200">
                                <li>• Césped Sintético</li>
                                <li>• Prohibido tacos metálicos</li>
                            </ul>
                        </div>
                    </div>

                     <!-- Recinto 3 -->
                     <div class="group relative overflow-hidden shadow-md hover:shadow-xl transition-all">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        <img src="{{ asset('storage/recintos/yzDgfaCMbHwwt1MiC0JALwFdHgFWJIGIptHOAfpL.jpg') }}" alt="Piscina Olímpica" class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <h3 class="text-xl font-bold mb-2">Piscina Olímpica</h3>
                            <ul class="text-sm space-y-1 text-gray-200">
                                <li>• Gorro obligatorio</li>
                                <li>• Ducha previa obligatoria</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Prohibiciones -->
            <div id="tab-prohibiciones" class="tab-content hidden max-w-6xl mx-auto">
                 <div class="text-center mb-16">
                    <h2 class="text-4xl font-serif font-bold text-gray-900 mb-3">Prohibiciones Estrictas</h2>
                    <p class="text-red-500 font-bold uppercase tracking-[0.2em] text-sm">Tolerancia Cero</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-16 mb-20">
                    <!-- Item 1: Alcohol -->
                    <div class="flex flex-col items-start">
                        <div class="w-8 h-8 text-red-500 mb-6">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm-4.95-10a4.95 4.95 0 119.9 0 4.95 4.95 0 01-9.9 0z"/>
                                <path d="M5.5 12a6.5 6.5 0 0110.875-4.83l-8.545 8.545A6.476 6.476 0 015.5 12zm13 0a6.5 6.5 0 01-10.875 4.83l8.545-8.545A6.476 6.476 0 0118.5 12z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-3">Alcohol y Drogas</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">Prohibido el consumo e ingreso bajo influencia de sustancias psicotrópicas o bebidas alcohólicas.</p>
                    </div>

                    <!-- Item 2: Fumar -->
                    <div class="flex flex-col items-start">
                        <div class="w-8 h-8 text-red-500 mb-6">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5" />
                                <!-- Icono cigarro estilizado -->
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1v12z" class="hidden" /> 
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke="none" fill="currentColor" class="opacity-0"/> 
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25" opacity="0"/>
                                <!-- Placeholder Smoking replacement -->
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-3 3-3-3" class="hidden"/>
                            </svg>
                            <!-- Custom Smoking Icon SVG -->
                            <svg class="absolute -mt-8" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 10L6 22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M7 16H4V13H7V16Z" fill="currentColor"/>
                                <path d="M10 13H7V16H10V13Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M21 5C21 5 19 7 19 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M17 5C17 5 16 6 16 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M13 10H19V13H13V10Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-3">Fumar</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">Zona libre de humo, incluyendo cigarrillos electrónicos y vaporizadores en todos los espacios.</p>
                    </div>

                    <!-- Item 3: Mascotas -->
                    <div class="flex flex-col items-start">
                        <div class="w-8 h-8 text-red-500 mb-6">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 5h-2v2h2V5zm-4 0h-2v2h2V5zM7 5H5v2h2V5zm4 0H9v2h2V5zm6 14c-1.1 0-2-.9-2-2 0-1.1.9-2 2-2s2 .9 2 2c0 1.1-.9 2-2 2zm-8 0c-1.1 0-2-.9-2-2 0-1.1.9-2 2-2s2 .9 2 2c0 1.1-.9 2-2 2z"/>
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5.5-2.5l2.16-2.16c-.2-.55-.37-1.13-.51-1.74l-2.65 1.15c.32.99.71 1.93 1.19 2.81zm-1.02-4.5l1.69-.74c.05-.59.13-1.17.25-1.73L5.3 9.4c-.23.95-.36 1.95-.36 2.97 0 .21.01.42.02.63h.52zm8.56 5.54c-.95.23-1.95.36-2.97.36s-2.02-.13-2.97-.36l.74-1.69c.56.12 1.14.2 1.73.25V18h1v-2.04c.59-.05 1.17-.13 1.73-.25l.74 1.69z"/>
                                <!-- Simple Paw -->
                                <path d="M12 20c-1.29 0-2.49-.66-3.18-1.75-.45-1.39.29-2.9 1.63-3.41 1.48-.56 3.12.18 3.58 1.64h.01c.21.66.19 1.34-.06 1.95-.6 1-1.72 1.57-2.98 1.57zM8.09 13.91c-.48-.37-1.16-.4-1.65-.07-.72.47-.96 1.44-.54 2.16.42.72 1.39.96 2.11.54.72-.47.96-1.44.54-2.16-.09-.17-.23-.32-.46-.47zM6.92 10.95c-.38-.47-1.06-.61-1.58-.33-.76.4-1.12 1.36-.81 2.15.31.79 1.25 1.25 2.01.85.76-.4 1.12-1.36.81-2.15a1.27 1.27 0 00-.43-.52zM12 9.5c-.71 0-1.32.48-1.5 1.16-.25.96.34 1.93 1.3 2.18s1.93-.34 2.18-1.3c.25-.96-.34-1.93-1.3-2.18-.23-.06-.46-.07-.68-.07-.06.19-.01.21 0 .21z" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-3">Mascotas</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">No se permite el ingreso de animales de ningún tipo al recinto deportivo.</p>
                    </div>

                    <!-- Item 4: Comercio -->
                    <div class="flex flex-col items-start mr-8">
                        <div class="w-8 h-8 text-red-500 mb-6">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M5.25 2.25a3 3 0 00-3 3v4.318a3 3 0 00.879 2.121l9.58 9.581c.92.92 2.39 1.186 3.548.428a25.897 25.897 0 003.882-3.132c.983-1.031.97-2.735-.028-3.733l-9.581-9.58A3 3 0 008.318 2.25H5.25zM6 6a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-3">Comercio Ambulante</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">Ventas no autorizadas y actividades comerciales sin previo permiso están prohibidas.</p>
                    </div>

                    <!-- Item 5: Violencia -->
                    <div class="flex flex-col items-start mr-8">
                        <div class="w-8 h-8 text-red-500 mb-6">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" class="hidden" />
                                <path d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5" />
                                <path d="M10 3v2m4-2v2" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-3">Comportamiento Violento</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">Tolerancia cero ante agresiones físicas o verbales hacia el personal u otros usuarios.</p>
                    </div>

                     <!-- Item 6: Parlantes -->
                     <div class="flex flex-col items-start">
                        <div class="w-8 h-8 text-red-500 mb-6">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25m-10.5-6l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12l-2.25 2.25m2.25-2.25l-2.25-2.25" class="hidden"/>
                                <line x1="16" y1="8" x2="22" y2="16" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg mb-3">Uso de Parlantes</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">No se permite el uso de equipos de sonido o música a alto volumen que interfiera con los demás.</p>
                    </div>
                </div>

                <!-- Footer Disclaimer -->
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-8 text-center max-w-4xl mx-auto shadow-sm">
                    <p class="text-gray-500 italic text-sm leading-relaxed">
                        El incumplimiento de cualquiera de estas normas facultará a la administración para solicitar el retiro inmediato del recinto.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Contact Info -->
    <div class="text-center mt-12 bg-white/50 backdrop-blur rounded-xl p-8 border border-white/60">
        <p class="text-gray-500 mb-4">¿Tienes dudas sobre la normativa?</p>
        <div class="flex flex-wrap justify-center gap-6">
            <a href="mailto:reservas@muniarica.cl" class="text-primary font-semibold hover:underline">reservas@muniarica.cl</a>
            <span class="text-gray-300">|</span>
            <a href="tel:+56582205500" class="text-primary font-semibold hover:underline">+56 58 2205500</a>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Ocultar contenidos
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Resetear botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-primary', 'text-primary');
        btn.classList.add('border-transparent', 'text-gray-400');
    });
    
    // Activar seleccionado
    document.getElementById('tab-' + tabName).classList.remove('hidden');
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-400');
    activeBtn.classList.add('active', 'border-primary', 'text-primary');
}
</script>
@endsection
