# EJEMPLO PRÁCTICO: Listado de Clientes usando el Template

## 📁 Archivo: `listado_clientes.php`

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Gestión de Clientes') ?> - Sistema Premoldeado</title>
    
    <?php 
    // Incluir estilos y scripts comunes
    include_once __DIR__ . '/../../components/common-styles.php';
    ?>
</head>
<body>
    <div class="container-fluid py-4">
        <?php
        // Mostrar mensajes flash si existen
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            $alertType = $flash['type'] === 'success' ? 'alert-success' : 
                        ($flash['type'] === 'error' ? 'alert-danger' : 'alert-info');
            echo '<div class="alert ' . $alertType . ' alert-dismissible fade show" role="alert">';
            echo '<i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($flash['message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['flash_message']);
        }
        ?>

        <!-- Header de la página -->
        <div class="page-header text-center">
            <h1 class="mb-3">
                <i class="fas fa-users me-2"></i>
                <?= htmlspecialchars($titulo ?? 'Gestión de Clientes') ?>
            </h1>
            <p class="mb-0 opacity-75">Administra la información de tus clientes</p>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?a=crear" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Nuevo Cliente
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/Sistema_Premoldeado/dashboard.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                        </a>
                    </div>
                </div>
                
                <!-- Tarjeta de la tabla -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i>
                            Lista de Clientes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <!-- TABLA CON CONFIGURACIÓN AUTOMÁTICA -->
                            <table class="table table-hover" id="clientes_table" data-table-type="clientes">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Nombre Completo</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($clientes)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="fas fa-users fa-2x mb-2"></i><br>
                                                No hay clientes registrados
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($clientes as $cliente): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-primary"><?= htmlspecialchars($cliente['id']) ?></span>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></strong>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($cliente['email']) ?>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($cliente['telefono'] ?? '-') ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (($cliente['estado'] ?? 1) == 1): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?a=editar&id=<?= $cliente['id'] ?>" 
                                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="/Sistema_Premoldeado/controllers/ClienteController.php?a=eliminar&id=<?= $cliente['id'] ?>" 
                                                           class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                           onclick="return confirm('¿Estás seguro de eliminar este cliente?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        console.log('✅ Página de clientes cargada correctamente');
    });
    </script>
</body>
</html>
```

## 🎯 **CAMBIOS REALIZADOS DEL TEMPLATE:**

### 1. **Personalización Básica:**
- ✅ Cambié `elementos` por `clientes`
- ✅ Cambié `elementos_table` por `clientes_table`
- ✅ Cambié `data-table-type="simple"` por `data-table-type="clientes"`
- ✅ Actualicé el `colspan="6"` para 6 columnas

### 2. **Columnas Específicas:**
- ✅ **ID**: Badge con número
- ✅ **Nombre Completo**: Concatenación de nombre + apellido
- ✅ **Email**: Campo directo
- ✅ **Teléfono**: Con fallback a '-'
- ✅ **Estado**: Badge activo/inactivo
- ✅ **Acciones**: Botones editar/eliminar

### 3. **Enlaces Funcionales:**
- ✅ **Nuevo Cliente**: `ClienteController.php?a=crear`
- ✅ **Editar**: `ClienteController.php?a=editar&id={id}`
- ✅ **Eliminar**: `ClienteController.php?a=eliminar&id={id}`

## 🚀 **OTROS EJEMPLOS RÁPIDOS:**

### **Para Productos (7 columnas):**
```php
<table id="productos_table" data-table-type="productos">
    <thead>
        <tr>
            <th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th>
            <th>Stock</th><th>Estado</th><th>Acciones</th>
        </tr>
    </thead>
    <!-- colspan="7" en el mensaje vacío -->
</table>
```

### **Para tabla simple (3 columnas):**
```php
<table id="categorias_table" data-table-type="simple">
    <thead>
        <tr>
            <th>ID</th><th>Nombre</th><th>Acciones</th>
        </tr>
    </thead>
    <!-- colspan="3" en el mensaje vacío -->
</table>
```

## ✅ **VENTAJAS DEL TEMPLATE ACTUALIZADO:**

1. **Automático**: Solo cambia el `data-table-type` y funciona
2. **Flexible**: Se adapta a cualquier número de columnas
3. **Consistente**: Misma apariencia en todo el sistema
4. **Mantenible**: Un solo lugar para actualizaciones
5. **Documentado**: Incluye guía completa de uso

¡El template es totalmente reutilizable! Solo necesitas cambiar las columnas y el tipo de tabla. 🎉