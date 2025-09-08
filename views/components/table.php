<?php
/**
 * Componente de tabla reutilizable con DataTables
 * 
 * Parámetros principales:
 * $data - Array de datos desde el controlador
 * $config - Array de configuración de la tabla
 * 
 * Estructura del array $config:
 * [
 *     'title' => 'Título de la tabla',
 *     'id' => 'id_tabla_unica',
 *     'columns' => [
 *         'campo' => [
 *             'label' => 'Etiqueta',
 *             'type' => 'text|badge|status|number|currency|date|datetime|custom',
 *             'sortable' => true|false,
 *             'searchable' => true|false,
 *             'width' => '100px',
 *             'class' => 'text-center',
 *             'formatter' => function($value, $row) { return $formatted_value; }
 *         ]
 *     ],
 *     'actions' => [
 *         'edit' => true|false|['condition' => 'field == value'],
 *         'delete' => true|false|['condition' => 'field == value'],
 *         'custom' => [
 *             [
 *                 'label' => 'Acción',
 *                 'icon' => 'fas fa-icon',
 *                 'class' => 'btn-success',
 *                 'onclick' => 'functionName({id})',
 *                 'condition' => 'field == value'
 *             ]
 *         ]
 *     ],
 *     'datatables' => [
 *         'pageLength' => 25,
 *         'ordering' => true,
 *         'searching' => true,
 *         'paging' => true,
 *         'info' => true,
 *         'responsive' => true,
 *         'language' => 'es'
 *     ],
 *     'empty' => [
 *         'message' => 'No hay datos',
 *         'icon' => 'fas fa-inbox',
 *         'subtext' => 'Texto adicional',
 *         'action' => '<button>Acción</button>'
 *     ]
 * ]
 * 
 * Ejemplo de uso:
 * $data = $controller->getData();
 * $config = [
 *     'title' => 'Lista de Productos',
 *     'id' => 'productos_table',
 *     'columns' => [
 *         'id' => ['label' => 'ID', 'type' => 'badge', 'width' => '80px'],
 *         'nombre' => ['label' => 'Nombre', 'type' => 'text'],
 *         'precio' => ['label' => 'Precio', 'type' => 'currency'],
 *         'activo' => ['label' => 'Estado', 'type' => 'status']
 *     ],
 *     'actions' => [
 *         'edit' => true,
 *         'delete' => ['condition' => 'can_delete == 1']
 *     ]
 * ];
 */

// Verificar que existan los datos y configuración
if (!isset($data) || !isset($config)) {
    echo '<div class="alert alert-danger">Error: Faltan parámetros $data y $config para el componente table</div>';
    return;
}

// Configuración por defecto
$defaultConfig = [
    'title' => 'Lista de Datos',
    'id' => 'dataTable_' . uniqid(),
    'columns' => [],
    'actions' => ['edit' => true, 'delete' => true],
    'datatables' => [
        'pageLength' => 25,
        'ordering' => true,
        'searching' => true,
        'paging' => true,
        'info' => true,
        'responsive' => true,
        'language' => 'es'
    ],
    'empty' => [
        'message' => 'No hay datos para mostrar',
        'icon' => 'fas fa-inbox',
        'subtext' => null,
        'action' => null
    ],
    'class' => 'table table-striped table-hover',
    'container_class' => 'table-responsive'
];

// Merge configuración - usar array_merge para evitar problemas con valores escalares
$config = array_merge($defaultConfig, $config);

// Merge específico para arrays anidados que sí necesitan merge recursivo
if (isset($config['datatables']) && isset($defaultConfig['datatables'])) {
    $config['datatables'] = array_merge($defaultConfig['datatables'], $config['datatables']);
}
if (isset($config['empty']) && isset($defaultConfig['empty'])) {
    $config['empty'] = array_merge($defaultConfig['empty'], $config['empty']);
}

// Auto-detectar columnas si no están definidas
if (empty($config['columns']) && !empty($data)) {
    $firstRow = reset($data);
    if (is_array($firstRow)) {
        foreach (array_keys($firstRow) as $key) {
            if ($key !== 'id') { // Excluir ID de auto-detección
                $config['columns'][$key] = ['label' => ucfirst(str_replace('_', ' ', $key)), 'type' => 'text'];
            }
        }
    }
}

/**
 * Función para formatear valores según tipo
 */
function formatCellValue($value, $type, $formatter = null, $row = []) {
    // Si hay un formatter personalizado, usarlo
    if ($formatter && is_callable($formatter)) {
        return $formatter($value, $row);
    }
    
    // Verificar que el valor no sea un array antes de procesar
    if (is_array($value)) {
        $value = json_encode($value); // Convertir array a string JSON
    }
    
    // Manejar valores nulos
    if ($value === null || $value === '') {
        $value = '';
    }
    
    // Formateo según tipo
    switch ($type) {
        case 'badge':
            return '<span class="badge bg-light text-dark">#' . htmlspecialchars($value) . '</span>';
            
        case 'status':
            $isActive = ($value == '1' || $value === 1 || $value === true || $value === 'activo');
            $badgeClass = $isActive ? 'bg-success' : 'bg-secondary';
            $text = $isActive ? 'Activo' : 'Inactivo';
            return '<span class="badge ' . $badgeClass . '">' . $text . '</span>';
            
        case 'number':
            return '<span class="fw-medium">' . number_format($value) . '</span>';
            
        case 'currency':
            return '<span class="fw-medium text-success">$' . number_format($value, 2) . '</span>';
            
        case 'date':
            return $value ? date('d/m/Y', strtotime($value)) : '-';
            
        case 'datetime':
            return $value ? date('d/m/Y H:i', strtotime($value)) : '-';
            
        case 'text':
        default:
            return htmlspecialchars((string)$value);
    }
}

/**
 * Función para evaluar condiciones
 */
function evaluateActionCondition($condition, $row) {
    if (empty($condition)) return true;
    
    // Reemplazar variables de la fila en la condición
    $evaluateCondition = $condition;
    foreach ($row as $key => $value) {
        $evaluateCondition = str_replace($key, var_export($value, true), $evaluateCondition);
    }
    
    // Evaluar condición de forma segura
    try {
        return eval("return $evaluateCondition;");
    } catch (Exception $e) {
        error_log("Error evaluando condición: " . $e->getMessage());
        return false;
    }
}

/**
 * Función para generar ID único para DataTables
 */
$tableId = $config['id'];
$hasActions = !empty($config['actions']) && (
    !empty($config['actions']['edit']) || 
    !empty($config['actions']['delete']) || 
    !empty($config['actions']['custom'])
);
?>
            
<!-- Incluir CSS de DataTables si no está incluido -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($data)): ?>
            <!-- Estado vacío -->
            <div class="text-center py-5">
                <i class="<?php echo $config['empty']['icon']; ?> fa-3x text-muted mb-3"></i>
                <h5 class="text-muted"><?php echo htmlspecialchars($config['empty']['message']); ?></h5>
                <?php if ($config['empty']['subtext']): ?>
                    <p class="text-muted"><?php echo htmlspecialchars($config['empty']['subtext']); ?></p>
                <?php endif; ?>
                <?php if ($config['empty']['action']): ?>
                    <div class="mt-3"><?php echo $config['empty']['action']; ?></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Tabla con datos -->
            <div class="<?php echo $config['container_class']; ?>">
                <table id="<?php echo $tableId; ?>" class="<?php echo $config['class']; ?> mb-0">
                    <thead class="table-light">
                        <tr>
                            <?php foreach ($config['columns'] as $columnKey => $columnConfig): ?>
                                <th<?php if (isset($columnConfig['width'])): ?> style="width: <?php echo $columnConfig['width']; ?>"<?php endif; ?>>
                                    <?php echo htmlspecialchars($columnConfig['label']); ?>
                                </th>
                            <?php endforeach; ?>
                            
                            <?php if ($hasActions): ?>
                                <th style="width: 120px;" class="text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($config['columns'] as $columnKey => $columnConfig): ?>
                                    <td<?php if (isset($columnConfig['class'])): ?> class="<?php echo $columnConfig['class']; ?>"<?php endif; ?>>
                                        <?php
                                        $value = $row[$columnKey] ?? '';
                                        $type = $columnConfig['type'] ?? 'text';
                                        $formatter = $columnConfig['formatter'] ?? null;
                                        echo formatCellValue($value, $type, $formatter, $row);
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                
                                <?php if ($hasActions): ?>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Botón Editar -->
                                            <?php if (!empty($config['actions']['edit'])): ?>
                                                <?php 
                                                $showEdit = true;
                                                if (is_array($config['actions']['edit']) && isset($config['actions']['edit']['condition'])) {
                                                    $showEdit = evaluateActionCondition($config['actions']['edit']['condition'], $row);
                                                }
                                                ?>
                                                <?php if ($showEdit): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-edit" 
                                                            data-id="<?php echo $row['id'] ?? ''; ?>"
                                                            onclick="editRecord(<?php echo $row['id'] ?? '0'; ?>)"
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <!-- Botón Eliminar -->
                                            <?php if (!empty($config['actions']['delete'])): ?>
                                                <?php 
                                                $showDelete = true;
                                                if (is_array($config['actions']['delete']) && isset($config['actions']['delete']['condition'])) {
                                                    $showDelete = evaluateActionCondition($config['actions']['delete']['condition'], $row);
                                                }
                                                ?>
                                                <?php if ($showDelete): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-delete" 
                                                            data-id="<?php echo $row['id'] ?? ''; ?>"
                                                            onclick="deleteRecord(<?php echo $row['id'] ?? '0'; ?>)"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-secondary" 
                                                            title="No se puede eliminar"
                                                            disabled>
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <!-- Acciones personalizadas -->
                                            <?php if (!empty($config['actions']['custom'])): ?>
                                                <?php foreach ($config['actions']['custom'] as $action): ?>
                                                    <?php 
                                                    $showAction = true;
                                                    if (isset($action['condition'])) {
                                                        $showAction = evaluateActionCondition($action['condition'], $row);
                                                    }
                                                    ?>
                                                    <?php if ($showAction): ?>
                                                        <button type="button" 
                                                                class="btn btn-outline-<?php echo $action['variant'] ?? 'secondary'; ?> <?php echo $action['class'] ?? ''; ?>" 
                                                                data-id="<?php echo $row['id'] ?? ''; ?>"
                                                                <?php if (isset($action['onclick'])): ?>
                                                                    onclick="<?php 
                                                                    $onclick = $action['onclick'];
                                                                    foreach ($row as $key => $value) {
                                                                        $onclick = str_replace('{' . $key . '}', $value, $onclick);
                                                                    }
                                                                    echo $onclick;
                                                                    ?>"
                                                                <?php endif; ?>
                                                                title="<?php echo $action['title'] ?? $action['label'] ?? ''; ?>">
                                                            <i class="<?php echo $action['icon'] ?? 'fas fa-cog'; ?>"></i>
                                                            <?php if (isset($action['label'])): ?>
                                                                <span class="d-none d-lg-inline ms-1"><?php echo $action['label']; ?></span>
                                                            <?php endif; ?>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Incluir JavaScript de DataTables si no está incluido -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<?php if (!empty($data)): ?>
<script>
$(document).ready(function() {
    // Configuración de idioma español
    const languageConfig = {
        "decimal": "",
        "emptyTable": "No hay datos disponibles en la tabla",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
        "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
        "infoFiltered": "(filtrado de _MAX_ entradas totales)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ entradas",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "No se encontraron coincidencias",
        "paginate": {
            "first": "Primero",
            "last": "Último",
            "next": "Siguiente",
            "previous": "Anterior"
        },
        "aria": {
            "sortAscending": ": activar para ordenar columna ascendente",
            "sortDescending": ": activar para ordenar columna descendente"
        }
    };

    // Configuración de DataTables
    const tableConfig = {
        pageLength: <?php echo $config['datatables']['pageLength']; ?>,
        ordering: <?php echo $config['datatables']['ordering'] ? 'true' : 'false'; ?>,
        searching: <?php echo $config['datatables']['searching'] ? 'true' : 'false'; ?>,
        paging: <?php echo $config['datatables']['paging'] ? 'true' : 'false'; ?>,
        info: <?php echo $config['datatables']['info'] ? 'true' : 'false'; ?>,
        responsive: <?php echo $config['datatables']['responsive'] ? 'true' : 'false'; ?>,
        language: <?php echo $config['datatables']['language'] === 'es' ? 'languageConfig' : '{}'; ?>,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        <?php if ($hasActions): ?>
        columnDefs: [
            {
                targets: -1, // Última columna (acciones)
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ]
        <?php endif; ?>
    };

    // Aplicar idioma si es español
    if (<?php echo $config['datatables']['language'] === 'es' ? 'true' : 'false'; ?>) {
        tableConfig.language = languageConfig;
    }

    // Inicializar DataTable
    $('#<?php echo $tableId; ?>').DataTable(tableConfig);
});

// Funciones globales para acciones (pueden ser sobrescritas)
if (typeof editRecord !== 'function') {
    function editRecord(id) {
        console.log('Editar registro:', id);
        // Implementar en la página que usa el componente
    }
}

if (typeof deleteRecord !== 'function') {
    function deleteRecord(id) {
        console.log('Eliminar registro:', id);
        // Implementar en la página que usa el componente
    }
}
</script>
<?php endif; ?>
