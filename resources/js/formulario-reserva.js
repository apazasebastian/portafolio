// FORMATO AUTOMÁTICO DE RUT
document.addEventListener('DOMContentLoaded', function () {
    const rutInput = document.getElementById('rut');

    if (rutInput) {
        rutInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9kK]/g, '');

            if (value.length > 1) {
                let body = value.slice(0, -1);
                let dv = value.slice(-1).toUpperCase();

                // Formatear con puntos
                body = body.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                e.target.value = body + '-' + dv;
            } else {
                e.target.value = value;
            }
        });
    }

    // SELECTOR DE REGIONES Y COMUNAS DE CHILE
    const regionSelect = document.getElementById('region');
    const comunaSelect = document.getElementById('comuna');

    // Solo región de Arica y Parinacota
    const comunasArica = ['Arica', 'Camarones', 'Putre', 'General Lagos'];

    if (regionSelect && comunaSelect) {
        // Establecer región fija: Arica y Parinacota
        regionSelect.innerHTML = '<option value="Arica y Parinacota" selected>Arica y Parinacota</option>';

        // Cargar comunas automáticamente
        comunaSelect.disabled = false;
        comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
        comunasArica.forEach(function (comuna) {
            let option = document.createElement('option');
            option.value = comuna;
            option.textContent = comuna;
            comunaSelect.appendChild(option);
        });
    }
});