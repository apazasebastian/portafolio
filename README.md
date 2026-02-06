<div align="center">
  <img src="public/images/logos/logo-header.png" alt="Logo Municipalidad" width="200" />
  
  <br />
  
  <h1>Sistema de Gestión de Recintos Deportivos</h1>
  
  <p>
    <strong>Municipalidad de Arica</strong>
  </p>
  
  <p>
    Una plataforma integral diseñada para modernizar, transparentar y optimizar <br>
    la solicitud y administración de espacios públicos deportivos.
  </p>

  <p>
    <a href="#-características">Características</a> •
    <a href="#-tecnologías">Tecnologías</a> •
    <a href="#-instalación">Instalación</a> •
    <a href="#-galería">Galería</a>
  </p>
  
  <br />

  <img src="https://img.shields.io/badge/Estado-En_Desarrollo-green?style=flat-square" alt="Status" />
  <img src="https://img.shields.io/badge/Versión-1.0.0-blue?style=flat-square" alt="Version" />
  <img src="https://img.shields.io/badge/Licencia-Privada-red?style=flat-square" alt="License" />
</div>

---

## Descripción

Este sistema resuelve la necesidad de gestionar eficientemente los recintos deportivos municipales. Permite a los ciudadanos y organizaciones agendar espacios mediante un calendario interactivo en tiempo real, mientras otorga a la administración municipal herramientas robustas para la aprobación de solicitudes, auditoría de acciones, generación de reportes y control de incidencias.

## Características

El sistema se divide en dos grandes áreas funcionales:

### Portal Administrativo
* **Dashboard Inteligente:** Métricas en tiempo real sobre ocupación y solicitudes.
* **Gestión de Roles:** Control granular con `Jefe de Recintos` y `Encargados` (vía Spatie Permissions).
* **Auditoría Total:** Trazabilidad completa de cada acción realizada en el sistema.
* **Reportes Avanzados:** Exportación de estadísticas a PDF y Excel.
* **Control de Incidencias:** Seguimiento de daños o problemas post-uso.

### Portal Ciudadano
* **Reserva Interactiva:** Verificación de disponibilidad en tiempo real.
* **Validaciones Locales:** Integración con validación de RUT y teléfonos chilenos.
* **Notificaciones:** Alertas automáticas por correo electrónico (Aprobación, Rechazo, Cancelación).
* **Autogestión:** Sistema seguro de cancelación mediante códigos únicos.

---

## Tecnologías

Este proyecto utiliza un stack moderno y robusto enfocado en el rendimiento y la escalabilidad.

<div align="center">

| Backend | Frontend | Base de Datos | Herramientas |
| :---: | :---: | :---: | :---: |
| ![Laravel](https://img.shields.io/badge/Laravel_12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white) | ![Vue.js](https://img.shields.io/badge/Vue.js_3-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white) | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white) | ![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white) |
| ![PHP](https://img.shields.io/badge/PHP_8.2-777BB4?style=for-the-badge&logo=php&logoColor=white) | ![Tailwind](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white) | | ![Git](https://img.shields.io/badge/Git-F05032?style=for-the-badge&logo=git&logoColor=white) |
| | ![Inertia](https://img.shields.io/badge/Inertia.js-9553E9?style=for-the-badge&logo=inertia&logoColor=white) | | ![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white) |

</div>

---

## Instalación

Para mantener la limpieza de este documento, despliega la sección correspondiente para ver los pasos técnicos.

<details>
<summary><strong> Desplegar guía de instalación paso a paso</strong></summary>
<br>

**Prerrequisitos:** PHP 8.2+, Composer, Node.js 18+, MySQL 8.0+.

1.  **Clonar el repositorio**
    ```bash
    git clone [https://github.com/tu-usuario/sistema-reservas-arica.git](https://github.com/tu-usuario/sistema-reservas-arica.git)
    cd sistema-reservas-arica
    ```

2.  **Instalar dependencias**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Configuración de entorno**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Asegúrate de configurar tu base de datos y credenciales SMTP en el archivo `.env`.*

4.  **Base de datos y Storage**
    ```bash
    php artisan migrate --seed
    php artisan storage:link
    ```

5.  **Crear Administrador (Tinker)**
    ```bash
    php artisan tinker
    ```
    ```php
    \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'admin@muniarica.cl',
        'password' => bcrypt('password'),
        'role' => 'jefe_recintos',
        'activo' => true
    ]);
    ```

6.  **Iniciar**
    ```bash
    php artisan serve
    ```
</details>

---

## Galería

<div align="center">
  <table>
    <tr>
      <td align="center"><strong>Calendario Público</strong></td>
      <td align="center"><strong>Panel Administrativo</strong></td>
    </tr>
    <tr>
      <td><img src="https://github.com/user-attachments/assets/0a54e3a3-612f-44ba-a496-6633130f352d" alt="Calendario" width="400"/></td>
      <td><img src="https://github.com/user-attachments/assets/1bec5627-7a21-47cb-ba4e-cb72fe8e773f" alt="Admin Dashboard" width="400"/></td>
    </tr>
    <tr>
      <td align="center"><strong>Gestión de Reservas</strong></td>
      <td align="center"><strong>Reportes y Estadísticas</strong></td>
    </tr>
    <tr>
      <td><img src="https://github.com/user-attachments/assets/f63bf111-e10e-4c5b-b604-b8f395b07248" alt="Reservas" width="400"/></td>
      <td><img src="https://github.com/user-attachments/assets/b541411f-a290-463c-b6b8-70c252b33245" alt="Estadisticas" width="400"/></td>
    </tr>
  </table>
</div>

---

<div align="center">
  <p>Desarrollado para la <strong>Ilustre Municipalidad de Arica</strong>.</p>
  <p>
    <sub>Este proyecto es software privado y su uso está restringido a la entidad municipal.</sub>
  </p>
</div>
