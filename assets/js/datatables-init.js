/**
 * DataTables Initialization - Sistema Premoldeado
 * Sistema flexible para la inicialización automática de DataTables
 */

// Configuración base para DataTables
const DataTablesConfig = {
    // Configuración común para todas las tablas
    common: {
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/Spanish.json"
        },
        "pageLength": 15,
        "responsive": true,
        "autoWidth": false,
        "processing": false,
        "serverSide": false,
        "searching": true,
        "ordering": true,
        "paging": true,
        "info": true,
        "lengthChange": true,
        "lengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Todos"]],
        "dom": "<'row'<'col-md-6'l><'col-md-6'f>>" +
               "<'row'<'col-md-12'tr>>" +
               "<'row'<'col-md-5'i><'col-md-7'p>>",
        "pagingType": "simple_numbers"
    },

    // Configuraciones específicas por tipo de tabla
    usuarios: {
        "order": [[1, "asc"]], // Ordenar por nombre de usuario
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 3, 4, 5], // ID, Perfil, Estado, Acciones
            "nonSortableColumns": [-1] // Última columna (acciones)
        }
    },

    perfiles: {
        "order": [[0, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 2, 3, 4], // ID, Estado, Usuarios, Acciones
            "nonSortableColumns": [-1] // Última columna (acciones)
        }
    },

    clientes: {
        "order": [[1, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 4, 5], // ID, Estado, Acciones
            "nonSortableColumns": [-1]
        }
    },

    productos: {
        "order": [[1, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 3, 4, 5], // ID, Precio, Estado, Acciones
            "nonSortableColumns": [-1]
        }
    },

    materiales: {
        "order": [[1, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 4, 5], // ID, Estado, Acciones
            "nonSortableColumns": [-1]
        }
    },

    pedidos: {
        "order": [[0, "desc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 4, 5, 6], // ID, Estado, Fecha, Acciones
            "nonSortableColumns": [-1]
        }
    },

    ventas: {
        "order": [[0, "desc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 4, 5, 6], // ID, Total, Fecha, Acciones
            "nonSortableColumns": [-1]
        }
    },

    proveedores: {
        "order": [[1, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 4, 5], // ID, Estado, Acciones
            "nonSortableColumns": [-1]
        }
    },

    produccion: {
        "order": [[0, "desc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, 4, 5, 6], // ID, Estado, Fecha, Acciones
            "nonSortableColumns": [-1]
        }
    },

    // Configuración genérica para tablas simples (3-4 columnas)
    simple: {
        "order": [[0, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, -1], // Primera y última columna
            "nonSortableColumns": [-1] // Última columna (generalmente acciones)
        }
    },

    // Configuración para tablas con muchas columnas (7+ columnas)
    extended: {
        "order": [[1, "asc"]],
        "autoColumnConfig": true,
        "manualConfig": {
            "centerColumns": [0, -3, -2, -1], // ID y últimas 3 columnas
            "nonSortableColumns": [-1]
        }
    }
};

/**
 * Clase principal para manejo de DataTables
 */
class DataTableManager {
    
    /**
     * Inicializar tabla con configuración específica
     * @param {string} tableId - ID de la tabla
     * @param {string} tableType - Tipo de tabla (usuarios, perfiles, etc.)
     * @param {object} customConfig - Configuración personalizada adicional
     */
    static initTable(tableId, tableType = 'common', customConfig = {}) {
        // Verificar que la tabla existe
        const $table = $(`#${tableId}`);
        if ($table.length === 0) {
            console.warn(`Tabla con ID '${tableId}' no encontrada`);
            return null;
        }

        // Obtener configuración base
        let config = { ...DataTablesConfig.common };

        // Aplicar configuración específica del tipo si existe
        if (DataTablesConfig[tableType]) {
            const typeConfig = { ...DataTablesConfig[tableType] };
            
            // Si tiene autoColumnConfig, generar columnDefs automáticamente
            if (typeConfig.autoColumnConfig && typeConfig.manualConfig) {
                typeConfig.columnDefs = DataTableManager.generateColumnDefs($table, typeConfig.manualConfig);
                delete typeConfig.autoColumnConfig;
                delete typeConfig.manualConfig;
            }
            
            config = { ...config, ...typeConfig };
        }

        // Aplicar configuración personalizada
        config = { ...config, ...customConfig };

        // Inicializar DataTable
        try {
            const dataTable = $table.DataTable(config);
            
            // Configurar eventos personalizados
            DataTableManager.setupCustomEvents(dataTable, tableId);
            
            console.log(`DataTable '${tableId}' inicializada correctamente`);
            return dataTable;
            
        } catch (error) {
            console.error(`Error al inicializar DataTable '${tableId}':`, error);
            return null;
        }
    }

    /**
     * Generar configuración de columnas automáticamente
     * @param {jQuery} $table - Elemento jQuery de la tabla
     * @param {object} manualConfig - Configuración manual (centerColumns, nonSortableColumns, etc.)
     * @returns {Array} Array de configuraciones de columnas
     */
    static generateColumnDefs($table, manualConfig) {
        const columnDefs = [];
        
        // Obtener número total de columnas
        const totalColumns = $table.find('thead th').length;
        
        // Convertir índices negativos a positivos (ej: -1 = última columna)
        const centerColumns = manualConfig.centerColumns.map(index => 
            index < 0 ? totalColumns + index : index
        );
        
        const nonSortableColumns = manualConfig.nonSortableColumns.map(index => 
            index < 0 ? totalColumns + index : index
        );
        
        // Configurar columnas centradas
        if (centerColumns.length > 0) {
            columnDefs.push({
                "targets": centerColumns,
                "className": "text-center"
            });
        }
        
        // Configurar columnas no ordenables
        if (nonSortableColumns.length > 0) {
            columnDefs.push({
                "targets": nonSortableColumns,
                "orderable": false
            });
        }
        
        console.log(`Auto-generada configuración para ${totalColumns} columnas:`, columnDefs);
        return columnDefs;
    }

    /**
     * Configurar eventos personalizados para la tabla
     * @param {object} dataTable - Instancia de DataTable
     * @param {string} tableId - ID de la tabla
     */
    static setupCustomEvents(dataTable, tableId) {
        // Evento al dibujar la tabla (útil para reinicializar tooltips)
        dataTable.on('draw', function() {
            DataTableManager.initTooltips();
        });

        // Evento de búsqueda personalizada
        dataTable.on('search.dt', function() {
            const searchValue = dataTable.search();
            if (searchValue.length > 0) {
                console.log(`Búsqueda en ${tableId}: ${searchValue}`);
            }
        });
    }

    /**
     * Inicializar tooltips de Bootstrap
     */
    static initTooltips() {
        // Destruir tooltips existentes para evitar duplicados
        $('[data-bs-toggle="tooltip"]').tooltip('dispose');
        
        // Inicializar nuevos tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                delay: { "show": 300, "hide": 100 }
            });
        });
    }

    /**
     * Actualizar datos de una tabla existente
     * @param {string} tableId - ID de la tabla
     * @param {array} newData - Nuevos datos
     */
    static updateTableData(tableId, newData) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            table.clear();
            table.rows.add(newData);
            table.draw();
        }
    }

    /**
     * Recargar datos de la tabla
     * @param {string} tableId - ID de la tabla
     */
    static reloadTable(tableId) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            table.ajax.reload();
        }
    }

    /**
     * Destruir tabla
     * @param {string} tableId - ID de la tabla
     */
    static destroyTable(tableId) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            table.destroy();
            console.log(`DataTable '${tableId}' destruida`);
        }
    }

    /**
     * Obtener datos seleccionados (si se usa selección)
     * @param {string} tableId - ID de la tabla
     */
    static getSelectedData(tableId) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            return table.rows('.selected').data().toArray();
        }
        return [];
    }

    /**
     * Exportar datos de la tabla (requiere extensiones de DataTables)
     * @param {string} tableId - ID de la tabla
     * @param {string} format - Formato de exportación ('excel', 'pdf', 'csv')
     */
    static exportTable(tableId, format = 'excel') {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            switch (format) {
                case 'excel':
                    table.button('.buttons-excel').trigger();
                    break;
                case 'pdf':
                    table.button('.buttons-pdf').trigger();
                    break;
                case 'csv':
                    table.button('.buttons-csv').trigger();
                    break;
                default:
                    console.warn(`Formato de exportación '${format}' no soportado`);
            }
        }
    }
}

/**
 * Funciones de utilidad para confirmaciones y acciones
 */
class TableUtils {
    
    /**
     * Confirmación estándar para eliminar/desactivar
     * @param {string} message - Mensaje de confirmación
     * @param {function} callback - Función a ejecutar si se confirma
     */
    static confirmAction(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }

    /**
     * Mostrar mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    static showSuccess(message) {
        // Implementar según el sistema de notificaciones usado
        console.log('Success:', message);
    }

    /**
     * Mostrar mensaje de error
     * @param {string} message - Mensaje a mostrar
     */
    static showError(message) {
        // Implementar según el sistema de notificaciones usado
        console.error('Error:', message);
    }

    /**
     * Formatear fecha para mostrar en tabla
     * @param {string} date - Fecha en formato ISO
     * @return {string} Fecha formateada
     */
    static formatDate(date) {
        if (!date) return '-';
        const d = new Date(date);
        return d.toLocaleDateString('es-ES');
    }

    /**
     * Formatear moneda para mostrar en tabla
     * @param {number} amount - Cantidad
     * @return {string} Cantidad formateada
     */
    static formatCurrency(amount) {
        if (!amount) return '$0.00';
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }
}

// Inicialización automática cuando el documento esté listo
$(document).ready(function() {
    // Inicializar tooltips al cargar la página
    DataTableManager.initTooltips();
    
    // Auto-detectar y inicializar tablas con atributos data-table-type
    $('table[data-table-type]').each(function() {
        const tableId = $(this).attr('id');
        const tableType = $(this).data('table-type');
        
        if (tableId) {
            DataTableManager.initTable(tableId, tableType);
        }
    });
    
    console.log('DataTableManager inicializado correctamente');
});

// Exportar para uso global
window.DataTableManager = DataTableManager;
window.TableUtils = TableUtils;