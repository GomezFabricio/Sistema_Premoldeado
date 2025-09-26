<?php
/**
 * 📊 EXCEL EXPORTER - Sistema de Exportación Universal
 * Exporta datos de cualquier módulo a CSV/Excel sin dependencias externas
 * Autor: Sistema Premoldeado
 * Versión: 1.0
 */

class ExcelExporter {
    
    /**
     * Exporta datos a CSV (compatible con Excel)
     * @param array $data - Datos a exportar
     * @param array $columns - Configuración de columnas
     * @param string $filename - Nombre del archivo
     */
    public static function exportToCSV($data, $columns, $filename = 'export') {
        // Headers para descarga
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d_H-i-s') . '.csv"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        // BOM para UTF-8 (para que Excel reconozca acentos)
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        $headers = [];
        foreach ($columns as $key => $config) {
            $headers[] = $config['label'] ?? ucfirst($key);
        }
        fputcsv($output, $headers, ';'); // Usar ; como separador para Excel español
        
        // Datos
        foreach ($data as $row) {
            $exportRow = [];
            foreach ($columns as $key => $config) {
                $value = $row[$key] ?? '';
                
                // Aplicar formatter si existe, pero limpiando HTML
                if (isset($config['formatter']) && is_callable($config['formatter'])) {
                    $formatted = $config['formatter']($value, $row);
                    // Limpiar HTML tags para CSV
                    $value = strip_tags($formatted);
                    $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
                } else {
                    // Formateo básico según tipo
                    switch ($config['type'] ?? 'text') {
                        case 'currency':
                            $value = '$' . number_format(floatval($value), 2);
                            break;
                        case 'date':
                            if ($value && $value !== '0000-00-00') {
                                $value = date('d/m/Y', strtotime($value));
                            }
                            break;
                        default:
                            $value = strval($value);
                    }
                }
                
                $exportRow[] = $value;
            }
            fputcsv($output, $exportRow, ';');
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Genera reporte HTML imprimible
     * @param array $data - Datos a exportar
     * @param array $columns - Configuración de columnas  
     * @param string $title - Título del reporte
     */
    public static function generateReport($data, $columns, $title = 'Reporte') {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title><?= htmlspecialchars($title) ?></title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
                .report-info { margin-bottom: 20px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="no-print">
                <button onclick="window.print()">🖨️ Imprimir</button>
                <button onclick="window.close()">❌ Cerrar</button>
            </div>
            
            <h1><?= htmlspecialchars($title) ?></h1>
            <div class="report-info">
                <strong>Fecha:</strong> <?= date('d/m/Y H:i:s') ?><br>
                <strong>Total registros:</strong> <?= count($data) ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <?php foreach ($columns as $key => $config): ?>
                            <th><?= htmlspecialchars($config['label'] ?? ucfirst($key)) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $key => $config): ?>
                                <td class="<?= $config['class'] ?? '' ?>">
                                    <?php
                                    $value = $row[$key] ?? '';
                                    if (isset($config['formatter']) && is_callable($config['formatter'])) {
                                        echo $config['formatter']($value, $row);
                                    } else {
                                        echo htmlspecialchars($value);
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 30px; font-size: 12px; color: #666; text-align: center;">
                Generado por Sistema Premoldeado - <?= date('Y') ?>
            </div>
        </body>
        </html>
        <?php
        $html = ob_get_clean();
        return $html;
    }
    
    /**
     * Obtiene configuración de columnas para export basada en config de tabla
     * @param array $tableConfig - Configuración de la tabla
     * @return array - Columnas filtradas para export
     */
    public static function getExportColumns($tableConfig) {
        $columns = $tableConfig['columns'] ?? [];
        $exportColumns = [];
        
        // Filtrar columnas que no queremos en export
        $excludeKeys = ['actions', 'custom_actions'];
        
        foreach ($columns as $key => $config) {
            if (!in_array($key, $excludeKeys)) {
                $exportColumns[$key] = $config;
            }
        }
        
        return $exportColumns;
    }
}

/**
 * 🎯 FUNCIÓN HELPER PARA CONTROLLERS
 * Maneja export requests automáticamente
 */
function handleExportRequest($data, $tableConfig, $moduleName) {
    if (isset($_GET['action']) && $_GET['action'] === 'export') {
        $format = $_GET['format'] ?? 'csv';
        $columns = ExcelExporter::getExportColumns($tableConfig);
        
        switch ($format) {
            case 'csv':
            case 'excel':
                ExcelExporter::exportToCSV($data, $columns, $moduleName);
                break;
                
            case 'report':
            case 'print':
                $html = ExcelExporter::generateReport($data, $columns, 'Reporte de ' . ucfirst($moduleName));
                echo $html;
                exit();
                break;
        }
    }
}
?>