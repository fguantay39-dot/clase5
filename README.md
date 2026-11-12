# Gimnasia y Tiro de Salta

Sitio del hincha con secciones informativas y formularios procesados en PHP.

## Ejecutar con XAMPP

1. Copiá la carpeta del proyecto a `C:\xampp\htdocs\gyt-nuevo`.
2. Iniciá Apache y MySQL desde el panel de XAMPP.
3. Para la base de usuarios, importá `database.sql` desde phpMyAdmin.
4. Abrí `http://localhost/gyt-nuevo/`.
5. La entrega de Clase 5 está en `http://localhost/gyt-nuevo/clase5/`.

## Clase 5 — Formularios y seguridad XSS

El desarrollo está dentro de la carpeta `clase5`.

- **POST — Contacto:** procesa nombre, correo y mensaje en el servidor. Valida campos obligatorios, longitud y formato del correo. Cuando hay errores, conserva los valores ingresados; si es válido, muestra una confirmación. Esta demostración conserva el último envío en la sesión PHP y no envía correos ni guarda datos permanentemente.
- **GET — Búsqueda:** filtra novedades por palabra clave o categoría, valida el largo de la consulta y conserva la búsqueda en el campo.
- **Seguridad:** los valores de entrada se normalizan y validan en PHP. Todo dato dinámico que vuelve a la página se escapa con `htmlspecialchars()` antes de mostrarse.

### Capturas de pantalla

Las capturas de la aplicación funcionando se guardan en [`clase5/capturas`](./clase5/capturas/):

**Formulario POST y búsqueda GET**

![Formulario POST y búsqueda GET](./clase5/capturas/clase5-formularios.png)

**POST procesado correctamente**

![Confirmación de procesamiento POST](./clase5/capturas/clase5-post-exito.png)

**Error de validación POST y campos conservados**

![Validación de datos y conservación de campos](./clase5/capturas/clase5-validacion.png)

**Resultados filtrados por GET**

![Resultados filtrados por GET](./clase5/capturas/clase5-busqueda.png)

**Mensajes de retroalimentación al enviar el formulario**

![Mensaje de error de validación](./clase5/capturas/retroalimentacion-error.png)

![Confirmación de envío exitoso](./clase5/capturas/retroalimentacion-exito.png)

**Validación del lado del servidor en `auth.php`**

![Validaciones de campos obligatorios y formato de correo en el registro](./clase5/capturas/auth-codigo-registro.png)

![Validaciones de campos obligatorios y formato de correo al iniciar sesión](./clase5/capturas/auth-codigo-inicio-sesion.png)

**Ejemplos de mensajes para campos vacíos y correo inválido**

![Mensaje de campo obligatorio vacío](./clase5/capturas/auth-campo-vacio.png)

![Mensaje para correo electrónico inválido](./clase5/capturas/auth-correo-invalido.png)

**Base de usuarios y acceso**

![Estructura de la tabla de usuarios en phpMyAdmin](./clase5/capturas/clase5-base-usuarios.png)

![Inicio de sesión y acceso automático a la portada](./clase5/capturas/acceso-inicio-automatico.png)

![Registro y acceso automático a la portada](./clase5/capturas/acceso-registro-portada.png)
