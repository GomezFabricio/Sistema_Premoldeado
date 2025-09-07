        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- JavaScript personalizado -->
    <script>
        // Configuración global de SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Función para mostrar alertas de confirmación
        function confirmarEliminacion(callback, titulo = '¿Estás seguro?', texto = 'Esta acción no se puede deshacer.') {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        // Función para mostrar mensajes de éxito
        function mostrarExito(mensaje) {
            Toast.fire({
                icon: 'success',
                title: mensaje
            });
        }

        // Función para mostrar mensajes de error
        function mostrarError(mensaje) {
            Toast.fire({
                icon: 'error',
                title: mensaje
            });
        }

        // Función para mostrar mensajes de información
        function mostrarInfo(mensaje) {
            Toast.fire({
                icon: 'info',
                title: mensaje
            });
        }

        // Auto-dismiss de alertas de Bootstrap después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Función para validar formularios
        function validarFormulario(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        }

        // Limpiar validaciones al escribir
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('is-invalid')) {
                e.target.classList.remove('is-invalid');
            }
        });

        // Función para formatear números
        function formatearNumero(numero, decimales = 2) {
            return new Intl.NumberFormat('es-AR', {
                minimumFractionDigits: decimales,
                maximumFractionDigits: decimales
            }).format(numero);
        }

        // Función para formatear moneda
        function formatearMoneda(numero) {
            return new Intl.NumberFormat('es-AR', {
                style: 'currency',
                currency: 'ARS'
            }).format(numero);
        }

        // Función para formatear fecha
        function formatearFecha(fecha) {
            return new Intl.DateTimeFormat('es-AR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            }).format(new Date(fecha));
        }

        // Función para confirmar navegación si hay cambios sin guardar
        let cambiosSinGuardar = false;
        
        function marcarCambios() {
            cambiosSinGuardar = true;
        }
        
        function confirmarSalida() {
            if (cambiosSinGuardar) {
                return 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
            }
        }
        
        window.addEventListener('beforeunload', confirmarSalida);

        // Función para resetear el estado de cambios
        function resetearCambios() {
            cambiosSinGuardar = false;
        }

        // Agregar listeners a formularios para detectar cambios
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(function(input) {
                    input.addEventListener('change', marcarCambios);
                });
                
                // Resetear cambios al enviar formulario
                form.addEventListener('submit', resetearCambios);
            });
        });

        // Función para copiar texto al portapapeles
        function copiarAlPortapapeles(texto) {
            navigator.clipboard.writeText(texto).then(function() {
                mostrarExito('Texto copiado al portapapeles');
            }).catch(function() {
                mostrarError('Error al copiar texto');
            });
        }

        // Función para imprimir
        function imprimirPagina() {
            window.print();
        }

        // Función para exportar tabla a CSV
        function exportarTablaCSV(tablaId, nombreArchivo = 'datos.csv') {
            const tabla = document.getElementById(tablaId);
            if (!tabla) return;
            
            let csv = [];
            const filas = tabla.querySelectorAll('tr');
            
            filas.forEach(function(fila) {
                const celdas = fila.querySelectorAll('td, th');
                const filaData = [];
                celdas.forEach(function(celda) {
                    filaData.push('"' + celda.textContent.replace(/"/g, '""') + '"');
                });
                csv.push(filaData.join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', nombreArchivo);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    </script>
</body>
</html>
