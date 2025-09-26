## ANÁLISIS DE MÓDULOS, CIRCUITO DEL SISTEMA Y REVISIÓN DE PERSONAS (CLIENTES)

### 1. Módulos Detectados y Estado CRUD

| Módulo        | Controlador | Vistas | CRUD (Listar, Alta, Baja, Modificar) | Estado |
|-------------- |:-----------:|:------:|:------------------------------------:|:------:|
| Productos     |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Producción    |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Materiales    |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Ventas        |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Pedidos       |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Usuarios      |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Proveedores   |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Personas      |   ✅        |  ✅    |   ✅                                | ✅ Completo |

Todos los módulos principales tienen controlador, vistas y archivos para listar, crear, editar y baja lógica.

---

### 2. Circuito del Sistema (Mapa de Relaciones)

- **Personas** es la entidad base: de ella derivan Clientes, Proveedores y Usuarios.
- **Clientes** y **Proveedores** dependen de Personas (por FK).
- **Productos** y **Materiales** pueden estar vinculados a Proveedores.
- **Pedidos** dependen de Clientes y Productos/Materiales.
- **Producción** consume Materiales y genera Productos.
- **Ventas** dependen de Clientes y Productos.
- **Usuarios** dependen de Personas y Perfiles.
- **Pedidos** y **Ventas** requieren que el módulo Personas esté completo y funcional.

---

### 3. Módulo Personas (Clientes) - Revisión CRUD

- **Controlador:** `PersonaController.php` implementa todos los métodos necesarios para CRUD (index, create, store, edit, update, delete).
- **Vistas:** existen archivos para crear (`crear_persona.php`), editar (`editar_persona.php`) y listar (`listado_personas.php`) personas.
- **Alta:** formulario completo en `crear_persona.php`.
- **Edición:** formulario en `editar_persona.php` con carga de datos.
- **Listado:** muestra todas las personas y permite editar/eliminar.
- **Baja lógica:** la acción delete está presente y pide confirmación.
- **Conclusión:** El módulo Personas tiene el CRUD completo y funcional.

---

### 4. Avance seguro con el módulo Pedidos

**Dependencias:**
- El módulo Pedidos depende de Personas (Clientes) y Productos/Materiales.
- Antes de avanzar, asegúrate que:
	- El CRUD de Personas funciona correctamente (ya validado).
	- El CRUD de Productos/Materiales está operativo.

**Pasos para avanzar:**
1. Verifica que el listado de clientes y productos esté accesible desde Pedidos.
2. Implementa el formulario de creación de pedido, permitiendo seleccionar cliente y productos.
3. Asegura que la relación entre pedido y cliente/producto se guarde correctamente.
4. Implementa la edición y baja lógica de pedidos.
5. Testea el flujo completo: alta, edición, baja y listado.

---

### 5. Recomendaciones

- Mantén la documentación y el análisis en este archivo.
- No dupliques archivos ni crees carpetas extra.
- Avanza módulo por módulo, validando dependencias antes de implementar nuevas funcionalidades.
| Clientes      |   ✅        |  ✅    |   ✅                                | ✅ Completo |
| Personas      |   ❌        |  ❌    |   ❌                                | ❌ Faltante |
| Perfiles      |   ❌        |  ❌    |   ❌                                | ❌ Faltante |
| Compras       |   ❌        |  ❌    |   ❌                                | ❌ Faltante |
| Auditoría     |   ❌        |  ❌    |   ❌                                | ❌ Faltante |
| Reportes      |   ❌        |  ❌    |   ❌                                | ❌ Faltante |

## 2. Detalle por Módulo

- **Productos**: Controlador, vistas y CRUD completo. Alta, edición y baja lógica funcionales. Listado centralizado.
- **Producción**: Controlador, vistas y CRUD completo. Integración con reservas y estados.
- **Materiales**: Controlador, vistas y CRUD completo. Baja lógica implementada.
- **Ventas**: Controlador y vistas presentes. Falta baja lógica y validación de edición.
- **Pedidos**: Controlador y vistas presentes. Falta baja lógica y validación de edición.
- **Usuarios**: Controlador, vistas y CRUD completo. Perfiles gestionados desde submódulo.
- **Proveedores**: Controlador, vistas y CRUD completo. Baja lógica funcional.
- **Clientes**: Controlador, vistas y CRUD completo. Baja lógica funcional.
- **Personas, Perfiles, Compras, Auditoría, Reportes**: No detectados controladores ni vistas base. CRUD no implementado.

## 3. Mapa de Ruta y Dependencias

- **Dependencias principales:**
	- Pedidos → Clientes, Productos
	- Producción → Productos, Materiales, Reservas
	- Ventas → Productos, Clientes, Pedidos
	- Proveedores → Personas
	- Usuarios → Perfiles
- **Prioridad de corrección:**
	1. Ventas y Pedidos: Implementar baja lógica y validación de edición.
	2. Personas y Perfiles: Crear controladores y vistas base, implementar CRUD.
	3. Compras, Auditoría, Reportes: Crear módulos y controladores, definir vistas y lógica.
	4. Mejorar validaciones y seguridad en todos los módulos.

## 4. Problemas Detectados y Sugerencias

- **Faltan controladores/vistas en módulos secundarios (Personas, Perfiles, Compras, Auditoría, Reportes).**
	- Sugerencia: Crear archivos base y definir estructura mínima de CRUD.
- **Ventas y Pedidos sin baja lógica.**
	- Sugerencia: Implementar baja lógica en controladores y modelos.
- **Dependencias no documentadas en algunos módulos.**
	- Sugerencia: Agregar comentarios y diagramas de flujo en el código.
- **Acceso directo a vistas sin pasar por controlador.**
	- Sugerencia: Redirigir siempre por el controlador para cargar datos correctamente.
- **Archivos duplicados y backups:**
	- Sugerencia: Eliminar manualmente archivos viejos y carpetas duplicadas.

## 5. Resumen y Siguiente Paso

- El sistema tiene los módulos principales operativos, pero requiere completar baja lógica y CRUD en ventas y pedidos, y crear módulos faltantes.
- Priorizar corrección en dependencias críticas y asegurar que el acceso sea siempre por controlador.
- Actualizar este reporte tras cada avance relevante.

---

*Este reporte se actualiza solo en este archivo. No se generan carpetas ni reportes extra.*
