# Ubicacion local y continuidad de Propaps

Fecha: 3 de noviembre de 2026.

## Carpeta de trabajo

La raiz local del proyecto en este PC es `D:\PROYECTOS\secure_paper`.
La ubicacion anterior era `C:\Users\Usuario\Documents\PROYECTOS\secure_paper`.
El traslado permite alojar en D las dependencias y los datos del futuro entorno de pruebas.
Las rutas internas de la aplicacion son relativas al proyecto; la letra del disco no se configura en GitHub ni en el servidor.

El repositorio es `ZapataMor/secure_paper`, rama principal `main`.
Hostinger mantiene su directorio existente `secure_paper`. Este cambio de ubicacion local no requiere modificar DNS, dominio, credenciales ni rutas de produccion.
Este documento es el registro que debe mantenerse tambien en la copia del repositorio de Hostinger.

## Versiones conservadas

- Version local anterior al traslado: `20fb8ae24d29376b61be8a9bfbe95626ef1f07f1`.
- Referencia Git: `respaldo/antes-traslado-d-20261103`.
- Respaldo independiente del historial: `D:\PROYECTOS\respaldos-propaps\traslado-20261103\secure_paper-antes-entorno-local.bundle`.
- Verificacion inicial: 262 archivos copiados, con coincidencia SHA-256, e integridad del historial comprobada con Git.
- Registro de hashes: `D:\PROYECTOS\respaldos-propaps\traslado-20261103\verificacion-copia.csv`.
- Version ya existente en GitHub al revisar el traslado: `f777bab9d6eca249f7cbcc805d31f731ccf84331`.
- La copia en D se actualizo mediante avance directo a esa version, preservando la anterior. Incluye tres commits posteriores: configuracion de ejemplo, pruebas de Wompi y cobros en pesos enteros.

La carpeta historica `C:\Users\Usuario\Documents\ChatGPT\Ajustes propaps.com` conserva versiones y evidencias anteriores; su traslado no forma parte de esta operacion.

## Entorno local v1 preparado

Preparado el 3 de noviembre de 2026 sobre `453bb93`, en la rama `codex/entorno-local-pruebas`. La rama `main` conserva la versión anterior a esta preparación.

Herramientas portátiles dentro de `.local/`: PHP 8.5.11, Composer 2.10.3, Node 24.22.2 y npm 11.19.1. Node se descargó de `nodejs.org` y su SHA-256 coincide con el publicado por el proveedor. PHP y Composer se copiaron de las herramientas históricas del proyecto.

Las dependencias se instalaron desde `composer.lock` y `package-lock.json`, conservando sus versiones. Las herramientas, descargas, cachés, archivos temporales y base de pruebas están en D. `.local/`, `.env`, `vendor/`, `node_modules/` y los recursos compilados no se incluyen en Git.

### Cómo abrir y cerrar la app

1. Abre `D:\PROYECTOS\secure_paper` y haz doble clic en `INICIAR-LOCAL.cmd`.
2. Visita `http://127.0.0.1:8000`. El servidor solo escucha en este equipo.
3. Para entrar, consulta `.local/accesos-pruebas.txt`. Contiene las claves de las cuentas ficticias `cliente@propaps.test`, `asesor@propaps.test` y `administracion@propaps.test`.
4. Al terminar, haz doble clic en `DETENER-LOCAL.cmd`. Se detiene únicamente el proceso de esta copia, comprobando su ejecutable y fecha de inicio; los datos se conservan.

La ventana del iniciador puede cerrarse después de mostrar la dirección. Los errores quedan en `.local/logs/`. Si el puerto 8000 está ocupado, el iniciador avisa sin detener otros procesos.

### Aislamiento de las pruebas

- Base SQLite independiente: `.local/database.sqlite`. Se aplicaron las 24 migraciones y se crearon únicamente las tres cuentas ficticias; no se importaron datos del servidor.
- Correos: `MAIL_MAILER=log`, guardados en `storage/logs/laravel.log`.
- Avisos automáticos: desactivados. La cola usa la base local; no se inicia un trabajador de correos.
- Wompi: entorno sandbox sin claves; esta copia no permite realizar pagos externos. Las pruebas automatizadas simulan las respuestas del proveedor.
- Google: sin credenciales en local. Las cuentas ficticias entran con email y contraseña.
- Los enlaces públicos a WhatsApp, email y referencias siguen siendo los del sitio; no forman parte de las pruebas locales.
- El iniciador comprueba el entorno, la ruta de SQLite, el correo y la ausencia de claves externas antes de arrancar. Se verificó que rechaza `APP_ENV=production`.

La plantilla `.env.local.example` documenta los valores locales sin secretos. La clave de aplicación y las contraseñas ficticias se generaron solo en archivos ignorados por Git. No copiar `.env.example` sobre la configuración local: ese archivo refleja la preparación del servidor.

### Comprobaciones realizadas

- Requisitos de PHP y extensiones: aprobados por Composer.
- Recursos visuales: `npm run build` completado.
- Inicio y parada mediante el controlador local: comprobados.
- 20 comprobaciones HTTP correctas: nueve páginas públicas, acceso con los tres roles y páginas privadas básicas. No equivalen a validar un recorrido documental completo.
- Suite existente: **45 pruebas aprobadas y 7 fallidas**, con 123 comprobaciones. Las **9 pruebas de Wompi aprobaron**.
- Formato de las herramientas PHP nuevas: aprobado.
- Evidencias locales: `.local/logs/tests-iniciales.xml`, `.local/logs/comprobacion-http.json` y `.local/logs/propaps-local-listo.jpg`.

Los siete fallos proceden de pruebas conservadas del proyecto: tres esperan la antigua notificación `ResetPassword`; uno no envía la aceptación legal exigida por el registro; otro usa la ruta inexistente `private.planes.select`; los dos restantes esperan textos anteriores sobre membresías. Quedan documentados para revisar su correspondencia con los requisitos actuales en el siguiente bloque. No se alteró la aplicación para forzar que esas pruebas aprobaran.

### Continuar por bloques

Primero revisar el acceso y la navegación del cliente ficticio. Después actualizar las comprobaciones antiguas y preparar, con datos ficticios, el recorrido de carga, asignación, revisión y devolución. Conservar un commit por bloque verificado.

Para ejecutar de nuevo las pruebas desde esta carpeta:

```powershell
& .\.local\php\php.exe vendor/bin/pest --compact
```

Para reconstruir los estilos y scripts:

```powershell
$env:Path = "$PWD\.local\node;" + $env:Path
& .\.local\node\npm.cmd run build
```

No ejecutar `composer setup` ni migraciones en Hostinger para preparar esta copia. Las herramientas de `tools/local/` son exclusivas del desarrollo local.

## Alcance operativo

El traslado y este registro no certifican la operacion de pagos, correos ni recorridos privados en produccion.
Actualizacion confirmada por el usuario el 3 de noviembre de 2026: el ultimo gran avance fue una prueba de pago real realizada satisfactoriamente. Esta confirmacion sustituye el pendiente historico de Wompi descrito en los registros de octubre. En esta sesion no se repitio el cobro ni se consulto el detalle de la transaccion; los recorridos restantes se revisaran por bloques.
No ejecutar una actualizacion completa en Hostinger solamente para copiar este documento sin revisar primero el estado y los cambios pendientes del servidor.

## Bloque 2: servicio ficticio y primera carga (3 de noviembre de 2026)

Se preparó exclusivamente en SQLite local la cuenta `cliente@propaps.test` con el servicio **PRUEBA LOCAL - Revision de manuscrito - Sin cobro**, del 03/11/2026 al 02/12/2026 inclusive. El importe es cero. `payment_status=paid` simula el requisito de acceso; no representa un pago real y no crea registros en `payments` ni en `wompi_orders`.

### Preparación reproducible

```powershell
& .\.local\php\php.exe tools/local/activate-test-service.php
```

La herramienta usa las protecciones de `tools/local/bootstrap.php`, solo admite la cuenta ficticia activa de cliente y conserva cualquier servicio previo. Antes de crear el servicio, genera una copia SQLite consistente mediante `VACUUM INTO` y comprueba su integridad. Si el servicio ficticio ya está vigente, no lo duplica ni cambia su fecha de finalización. Si existe otro servicio, se detiene para revisarlo. No usarla como herramienta de administración en producción.

### Recorrido comprobado en Brave

1. Actualizar Inicio: aparece **Servicio activo**, el nombre de prueba y su fecha de vencimiento; se habilita **Cargar documentos**.
2. Abrir **Cargar documentos**: aparecen selección de archivos, indicaciones, documentos y seguimiento.
3. Pulsar **Enviar al equipo** sin contenido: aparece «Adjunta al menos un archivo o escribe tus indicaciones»; no se guarda nada.
4. Seleccionar un `.txt` ficticio: el navegador lo rechaza y muestra los formatos permitidos.
5. Seleccionar `.local/fixtures/PRUEBA-LOCAL-manuscrito.pdf`, escribir indicaciones ficticias y enviar: aparece **Archivo cargado correctamente**.
6. El expediente conserva un archivo y una indicación. La orientación cambia a **Insumos recibidos**.
7. Abrir el enlace del documento: el PDF se muestra correctamente en una pestaña nueva.

Se verificó que la huella SHA-256 del archivo almacenado coincide con la del PDF original. Los recuentos de pagos, órdenes Wompi y trabajos de cola permanecieron en cero. Se comprobó también que repetir la activación no crea otra suscripción y que `APP_ENV=production` bloquea la herramienta antes de hacer cambios.

### Evidencias y conservación

- Respaldo anterior: `.local/backups/antes-servicio-ficticio-20261103-221832-9219af.sqlite`.
- Respaldo posterior: `.local/backups/despues-primera-carga-*.sqlite` (conserva el expediente; el archivo adjunto permanece en `storage/app/private/documents/`).
- Resultado de comprobaciones: `.local/logs/comprobacion-carga-documentos.json`.
- PDF original de una página: `.local/fixtures/PRUEBA-LOCAL-manuscrito.pdf`.
- Captura del expediente: `.local/logs/primera-carga-cliente.jpg`.

Las bases, adjuntos y credenciales locales están excluidos de Git. El código de la herramienta y este registro permiten conservar el procedimiento por separado de los datos. No se modificaron Hostinger, Wompi ni las reglas de acceso de la aplicación.

### Siguiente bloque y límites

Pendiente: asignación al asesor ficticio, revisión y devolución de una versión al cliente. En esta revisión se probó un PDF con indicaciones, el envío vacío y el rechazo de `.txt` en el navegador; no se certificaron todavía todos los formatos, tamaños límite ni envíos múltiples.

Observaciones para una mejora posterior: Configuración conserva textos en inglés; las horas del expediente se muestran en UTC (22:20 en la prueba, equivalente a 17:20 de Bogotá); los contadores usan «1 archivos» y «1 indicaciones». No se cambiaron estos textos en este bloque.
## Bloque 3: asignación y acceso del asesor (3 de noviembre de 2026)

Se asignó el expediente del cliente ficticio desde la interfaz local de administración, sin cambiar código ni reglas de permisos.

### Recorrido comprobado

1. Ingresar como `administracion@propaps.test`, abrir **Proyectos** y el expediente de **Cliente de Prueba**.
2. Establecer el título **PRUEBA LOCAL - Manuscrito de Cliente de Prueba**, seleccionar **Asesor de Prueba**, conservar **Recibido** y pulsar **Guardar asignación y estado**.
3. Comprobar el aviso **Asignación y seguimiento del proyecto guardados**.
4. Cerrar la sesión administrativa e ingresar como `asesor@propaps.test`.
5. Abrir **Proyectos**: aparece el expediente asignado. Dentro se ven el PDF y las indicaciones del cliente.
6. Abrir el PDF desde la sesión del asesor: se muestra correctamente en el visor del navegador.

La interfaz del asesor permite actualizar seguimiento y compartir documentos o mensajes; no presenta los controles administrativos para cambiar el responsable o el nombre del proyecto. En este bloque no se envió ninguna devolución ni mensaje y el estado sigue en **Recibido**.

### Verificación y conservación

Antes de asignar, la cuenta del asesor no tenía acceso según `ProjectAccess`; después, tanto ese servicio como `DocumentPolicy` permiten consultar el expediente y el documento. El acceso posterior también se verificó con la sesión real del asesor en Brave. No se realizó todavía una prueba HTTP de rechazo con un segundo asesor no asignado.

Se conservaron un archivo y una indicación, y la huella SHA-256 del PDF no cambió. La asignación quedó registrada a nombre del administrador ficticio y se creó su entrada de auditoría. Los recuentos de pagos, órdenes Wompi, trabajos en cola y mensajes permanecieron sin cambios. Hostinger no fue modificado.

- Respaldo previo verificado: `.local/backups/antes-asignacion-20261103-172422.sqlite`.
- Respaldo posterior verificado: `.local/backups/despues-asignacion-20261103-172809.sqlite`.
- Resultado: `.local/logs/comprobacion-asignacion-asesor.json`.
- Evidencias: `.local/logs/asignacion-admin-local.jpg`, `.local/logs/expediente-asesor-local.jpg` y `.local/logs/pdf-abierto-asesor-local.jpg`.

Se dejó abierta la sesión del asesor en el expediente local. Siguiente bloque: preparar una devolución ficticia y comprobar que el cliente puede consultar el documento y el mensaje del asesor.