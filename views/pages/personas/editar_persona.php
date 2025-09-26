<?php
$pageTitle = 'Editar Persona';
$persona = $persona ?? [];
?>
<div class="container">
    <h2><?= $pageTitle ?></h2>
    <form method="POST" action="?action=update&id=<?= $persona['id'] ?>">
        <div class="mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= htmlspecialchars($persona['apellidos'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control" id="nombres" name="nombres" value="<?= htmlspecialchars($persona['nombres'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="tipo_documento" class="form-label">Tipo de Documento</label>
            <select class="form-select" id="tipo_documento" name="tipo_documento">
                <option value="DNI" <?= ($persona['tipo_documento'] ?? '') == 'DNI' ? 'selected' : '' ?>>DNI</option>
                <option value="CUIT" <?= ($persona['tipo_documento'] ?? '') == 'CUIT' ? 'selected' : '' ?>>CUIT</option>
                <option value="CUIL" <?= ($persona['tipo_documento'] ?? '') == 'CUIL' ? 'selected' : '' ?>>CUIL</option>
                <option value="PASAPORTE" <?= ($persona['tipo_documento'] ?? '') == 'PASAPORTE' ? 'selected' : '' ?>>Pasaporte</option>
                <option value="CEDULA" <?= ($persona['tipo_documento'] ?? '') == 'CEDULA' ? 'selected' : '' ?>>Cédula</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="numero_documento" class="form-label">Número de Documento</label>
            <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?= htmlspecialchars($persona['numero_documento'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($persona['telefono'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($persona['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($persona['direccion'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="localidad" class="form-label">Localidad</label>
            <input type="text" class="form-control" id="localidad" name="localidad" value="<?= htmlspecialchars($persona['localidad'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="provincia" class="form-label">Provincia</label>
            <input type="text" class="form-control" id="provincia" name="provincia" value="<?= htmlspecialchars($persona['provincia'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="codigo_postal" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?= htmlspecialchars($persona['codigo_postal'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($persona['fecha_nacimiento'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones"><?= htmlspecialchars($persona['observaciones'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="?action=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
