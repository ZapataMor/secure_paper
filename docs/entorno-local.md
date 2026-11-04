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
## Bloque 4: devolución del asesor recibida por el cliente (3 de noviembre de 2026)

Se preparó el PDF ficticio `.local/fixtures/PRUEBA-LOCAL-devolucion-asesor-v1.pdf` y un mensaje de seguimiento para el cliente de prueba. El usuario completó la selección y el envío desde la sesión local del asesor; la interfaz confirmó **Archivo enviado al usuario correctamente**. No se modificó el código de la aplicación.

### Recorrido verificado

1. Desde el asesor, comprobar un documento compartido y un mensaje en el historial, identificado con el remitente **Asesor de Prueba**.
2. Cerrar sesión e ingresar como `cliente@propaps.test`.
3. Abrir **Cargar documentos**: en **Devoluciones del equipo** aparece el PDF y en **Mensajes del equipo** aparece el texto completo de la devolución.
4. Abrir el PDF desde el enlace del cliente: el visor muestra la devolución ficticia v1 correctamente.

El expediente conserva el manuscrito original, la indicación del cliente, el PDF devuelto y un mensaje del asesor. Se comprobó en SQLite que el asesor figura como autor del documento y remitente del mensaje, que el cliente tiene permiso para abrirlo y que la huella SHA-256 del PDF almacenado coincide con el original generado.

El estado del proyecto permanece **Recibido**: compartir documentos no cambia automáticamente el seguimiento. No se ejecutó un cambio a **Entregado**. El correo permanece en `log`, los avisos automáticos están desactivados y los recuentos de trabajos en cola, pagos y órdenes Wompi permanecieron sin cambios. No se enviaron correos reales ni se modificó Hostinger.

### Evidencias y respaldo

- Antes: `.local/backups/antes-devolucion-20261103-173119.sqlite`.
- Después: `.local/backups/despues-devolucion-20261103-173841.sqlite`.
- Comprobaciones: `.local/logs/comprobacion-devolucion-cliente.json`.
- Capturas: `.local/logs/devolucion-recibida-cliente.jpg` y `.local/logs/devolucion-pdf-cliente.jpg`.

Los respaldos SQLite fueron verificados con `PRAGMA integrity_check`; los adjuntos permanecen en el almacenamiento privado local. La sesión del cliente queda abierta en su expediente.

Observación de interfaz: el cliente ve **Equipo propaps.com** como remitente, aunque administración y la base de datos identifican a **Asesor de Prueba**. Propuesta para un bloque posterior: mostrar el nombre del remitente para aclarar quién responde. Queda también por probar el cambio de estado y su reflejo en el panel del cliente, además de la respuesta posterior del cliente.
## Bloque 5: estados reflejados en el cliente (3 de noviembre de 2026)

Se recorrió **Recibido → En revisión → En ajustes → Entregado** en el expediente ficticio. Cada cambio se envió por HTTP a la ruta normal de actualización, con una sesión independiente autenticada como `asesor@propaps.test` y el token CSRF del formulario. El servidor confirmó cada cambio y se comprobó su persistencia al volver a cargar la vista del asesor. No se editaron directamente los estados en SQLite ni se modificó código de la aplicación.

La cuenta del cliente permaneció abierta en Brave. Se comprobaron visualmente tanto Inicio como el expediente después de cada cambio:

| Estado del expediente | Orientación en Inicio | Acción disponible |
| --- | --- | --- |
| En revisión | Proyecto en revisión / Sigue el avance de tu manuscrito | Ver seguimiento |
| En ajustes | Proyecto en ajustes / Revisa las observaciones | Consultar ajustes |
| Entregado | Proyecto entregado / Consulta tu entrega | Ver documentos entregados |

**Consultar ajustes** lleva a Seguimiento y comunicación. **Ver documentos entregados** lleva a Documentos del proyecto, donde se mantienen el manuscrito y la devolución. Los cambios se observaron después de recargar o navegar; no se probó una actualización automática en tiempo real.

### Integridad y aislamiento

- Tres entradas de auditoría registran la secuencia completa con el asesor ficticio como actor.
- Los dos PDF mantienen sus huellas SHA-256; se conserva el mensaje del asesor y la indicación original del cliente.
- Los recuentos de pagos, órdenes Wompi y trabajos de cola permanecen en cero.
- El correo sigue en `log` y los avisos automáticos están desactivados: no se enviaron correos reales.
- Las fechas y el pago ficticio del servicio no cambiaron. **Entregado** no cierra la carga; esta sigue vinculada a la vigencia del servicio, hasta el 02/12/2026 inclusive.
- Estado final conservado: **Entregado**. Hostinger permanece sin cambios.

### Evidencias

- Respaldo previo verificado: `.local/backups/antes-estados-20261103-174147.sqlite`.
- Respaldo posterior verificado: `.local/backups/despues-estados-20261103-174345.sqlite`.
- Resultado: `.local/logs/comprobacion-estados-cliente.json`.
- Procedimiento HTTP local: `.local/probar-estado.ps1` (usa las protecciones locales y las credenciales ficticias sin imprimirlas).
- Capturas: `.local/logs/estado-en-revision-cliente.jpg`, `.local/logs/estado-en-ajustes-cliente.jpg`, `.local/logs/estado-entregado-cliente.jpg` y `.local/logs/estado-entregado-inicio-cliente.jpg`.

Este bloque verifica la presentación de los tres estados con insumos y una devolución existentes. Quedan para otro bloque las respuestas posteriores del cliente y los permisos entre expedientes de distintos clientes/asesores.
## Bloque 6: respuesta del cliente visible para el asesor (3 de noviembre de 2026)

Se probó una respuesta de texto sin adjuntos desde la sesión del cliente en Brave, manteniendo el expediente ficticio en **Entregado** y el servicio vigente.

1. En **Cargar documentos**, escribir en **Indicaciones para la revisión** una nota de prueba que confirma la lectura de la devolución v1 y solicita una aclaración ficticia sobre la conexión entre objetivo y resultados.
2. Pulsar **Enviar al equipo** sin seleccionar archivos.
3. Comprobar **Informacion adicional guardada correctamente** y la presencia de dos indicaciones: la original y la nueva respuesta, cada una con su fecha y hora.
4. Cerrar sesión, ingresar como `asesor@propaps.test`, abrir **Proyectos** y el expediente asignado.
5. Comprobar que **Indicaciones del cliente** muestra el texto completo de ambas notas, con la nueva primero.

La respuesta del cliente se guarda como una nueva entrada de información adicional; no se crea un mensaje del equipo. Se verificó que la nota original se conserva exactamente, que solo se añadió una entrada y que los dos documentos mantienen sus huellas SHA-256. El mensaje previo del asesor sigue disponible. Responder no reabre automáticamente el proyecto: permanece **Entregado**.

Los recuentos de trabajos en cola, pagos y órdenes Wompi siguen en cero. El correo permanece en `log` y los avisos automáticos están desactivados; no se enviaron correos reales. No se modificaron código de aplicación ni Hostinger.

### Conservación

- Respaldo previo verificado: `.local/backups/antes-respuesta-cliente-20261103-174519.sqlite`.
- Respaldo posterior verificado: `.local/backups/despues-respuesta-cliente-20261103-174644.sqlite`.
- Resultado: `.local/logs/comprobacion-respuesta-cliente.json`.
- Evidencias: `.local/logs/respuesta-guardada-cliente.jpg` y `.local/logs/respuesta-visible-asesor.jpg`.

Se dejó abierta la sesión del asesor en el expediente. Mejora propuesta, todavía sin implementar: hacer visible una acción **Responder al asesor**, ya que actualmente la respuesta se envía mediante el campo **Indicaciones para la revisión**. El siguiente bloque recomendado es comprobar el aislamiento de expedientes entre clientes y asesores distintos mediante cuentas exclusivamente ficticias.
## Bloque 7: aislamiento entre clientes y asesores (3 de noviembre de 2026)

Se añadieron 13 casos automatizados en `tests/Feature/ProjectAccessIsolationTest.php`, usando dos clientes con servicio activo, dos asesores y un administrador ficticios. Las pruebas ejecutan las rutas y controladores de Laravel con autenticación simulada por el framework; no sustituyen una prueba de inicio de sesión ni comprueban CSRF en el navegador.

La ejecución usa SQLite `:memory:` y `Storage::fake('local')`. Correo, notificaciones y cola se simulan, los avisos del proyecto se desactivan y se prohíben solicitudes HTTP externas inesperadas. El expediente local persistente permanece separado.

### Resultados

- Ambos clientes consultan su manuscrito y devolución, pero reciben **403** al intentar abrir los archivos del otro, aun teniendo un servicio vigente.
- Cada asesor solo ve su expediente en la lista y puede abrir sus documentos e indicaciones. El expediente y los archivos del otro cliente devuelven **403**.
- Un asesor no asignado o un cliente no pueden cambiar el estado, reasignar el expediente ni enviarle archivos o mensajes: **403**, sin cambios en documentos, mensajes, auditoría ni almacenamiento.
- Manipular los campos `user_id` o `uploaded_by` en una carga de cliente no permite atribuir el archivo a otro usuario: el servidor mantiene al cliente autenticado como propietario y autor.
- La reasignación administrativa retira el acceso al asesor anterior, incluidos los documentos que él mismo había subido; el nuevo asesor sí puede acceder.
- Un asesor inactivo o eliminado pierde el acceso al expediente, archivos y acciones de escritura.
- Se verifica también el acceso permitido: el asesor asignado puede actualizar y devolver trabajo en su expediente, y administración puede consultar ambos expedientes.
- Sin sesión, las rutas protegidas redirigen al inicio de sesión.

Resultado del bloque: **19 pruebas aprobadas, 227 comprobaciones** (13 casos nuevos y 6 pruebas existentes de `DocumentAccessTest.php`). Formato PHP aprobado. No fue necesario modificar las reglas de acceso de la aplicación.

### Conservación y reproducción

- Respaldo previo verificado: `.local/backups/antes-aislamiento-20261103-175035.sqlite`.
- Resultado automatizado: `.local/logs/pruebas-aislamiento.xml`.
- Verificación del entorno persistente: `.local/logs/comprobacion-aislamiento.json`.
- Se compararon exactamente 11 tablas de negocio con el respaldo y las huellas SHA-256 de los dos PDF: sin cambios. El expediente local conserva **Entregado**, dos documentos, dos indicaciones del cliente y un mensaje del asesor.

Para repetir este bloque desde la raíz del proyecto, sin configuración cacheada:

```powershell
$env:APP_ENV = 'testing'
$env:DB_CONNECTION = 'sqlite'
$env:DB_DATABASE = ':memory:'
$env:DB_URL = ''
$env:MAIL_MAILER = 'array'
$env:PROPAPS_PROJECT_EMAIL_ENABLED = 'false'
& .\.local\php\php.exe vendor/bin/pest tests/Feature/ProjectAccessIsolationTest.php tests/Feature/DocumentAccessTest.php --compact
```

Estas variables corresponden a la sesión de terminal de pruebas; abrir otra terminal para el servidor local. No se ejecutó nuevamente toda la suite: los siete fallos antiguos documentados en el primer bloque siguen pendientes. Esta comprobación de permisos no equivale a una auditoría integral de seguridad o de producción. Hostinger permanece sin cambios.
## Bloque 8: remitente visible y acceso para responder (3 de noviembre de 2026)

Se mejoró la comunicación en el expediente del cliente:

- Cada mensaje muestra el nombre completo de quien lo envió. La consulta carga los remitentes junto con los mensajes y mantiene el filtro por destinatario. Se conserva **Equipo propaps.com** como texto de respaldo si falta un nombre.
- Cuando hay mensajes, aparece **Responder al asesor** si existe un asesor responsable disponible; en caso contrario, **Responder al equipo**. El enlace lleva al campo de texto y coloca allí el cursor.
- El campo ahora se llama **Indicaciones y respuestas** y explica que se puede enviar solo texto. El historial se titula **Tus indicaciones y respuestas**.
- El acceso no envía el formulario ni borra lo escrito. El envío conserva su botón **Enviar al equipo** y agrega la respuesta al historial del expediente; no crea un hilo privado separado con el autor de un mensaje anterior.

### Validación

Se revisó en Brave con la cuenta local del cliente: nombre **Asesor de Prueba** visible, enlace operativo con clic y con Enter, foco en `additional_information` y conservación de un borrador. El borrador se retiró sin enviarlo. Permanecen dos indicaciones, dos PDF y un mensaje; no se crearon trabajos de correo.

Las 19 pruebas de aislamiento y documentos volvieron a aprobar, con 227 comprobaciones. Evidencias: `.local/logs/pruebas-comunicacion-cliente.xml`, `.local/logs/comprobacion-comunicacion-cliente.json` y `.local/logs/comunicacion-cliente-mejorada.jpg`.

Archivos de aplicación modificados: `app/Http/Controllers/PrivateDocumentUploadController.php` y `resources/views/private/upload-document.blade.php`. No se cambiaron las reglas de autorización, el almacenamiento de respuestas ni la configuración de correo. La mejora está únicamente en la copia local y no se desplegó en Hostinger.
## Bloque 9: expediente en pantallas de celular (3 de noviembre de 2026)

Se revisó el expediente del cliente en Brave con tamaños solicitados de 390 x 844 y 320 x 720. El navegador reportó un ancho efectivo de 391 y 321 píxeles, respectivamente. También se comprobó Inicio a 320 x 720. Es una revisión de escritorio con ventana reducida; no sustituye una prueba en un teléfono físico y su teclado virtual.

- El expediente, sus tarjetas de documentos, nombres de PDF, indicaciones y mensajes se adaptan sin desbordamiento horizontal en los tamaños comprobados.
- El menú del cliente abre y muestra Configuración y Cerrar sesión dentro de la pantalla.
- Se amplió a 44 píxeles la altura mínima de las opciones del menú en pantallas de hasta 640 píxeles: antes medían 32.
- Se amplió a 44 píxeles la zona de pulsación de los enlaces secundarios del expediente en pantallas de hasta 540 píxeles: antes algunos medían entre 15 y 19.
- Responder al asesor lleva el foco a Indicaciones y respuestas en la pantalla pequeña. El campo quedó vacío; no se envió ningún formulario ni se añadieron mensajes o documentos.
- Se restauró el tamaño normal del navegador al terminar.

Cambios limitados a estilos en `resources/views/layouts/app/navbar.blade.php` y `resources/views/private/upload-document.blade.php`. Verificación visual, geometría del DOM, comprobación sintáctica PHP y `git diff --check` satisfactorias. No se repitieron pruebas de negocio porque este bloque solo modifica CSS. El control del entorno local confirmó SQLite en D, correo en archivos y pagos externos desactivados. Sin despliegue en Hostinger ni publicación en GitHub.

Evidencia: `.local/logs/expediente-movil.jpg`. Siguiente bloque propuesto: mostrar las fechas y horas del expediente con la zona horaria acordada para el cliente; actualmente se presentan en UTC.
## Bloque 10: horario de Colombia en el expediente (3 de noviembre de 2026)

Las fechas de documentos y las fechas y horas de indicaciones y mensajes del expediente se presentan en `America/Bogota`. Cliente, asesor y administración comparten el mismo criterio. Las dos vistas explican que el horario mostrado es Colombia (UTC−5).

Se agregó `app.display_timezone` para la presentación. `app.timezone` permanece en UTC; no se cambian escritura, almacenamiento, orden cronológico, vencimiento del servicio ni tareas programadas. La conversión trabaja sobre copias de las fechas. Las indicaciones sin fecha conservan el texto Sin fecha; los mensajes sin fecha también muestran ese texto sin inventar una hora.

### Validación y conservación

- Tres casos nuevos, uno por rol, prueban el cruce de medianoche: 04/11/2026 02:15 UTC se muestra como 03/11/2026 21:15.
- Se verifican notas que ya incluyen un desfase horario explícito, orden cronológico, fechas ausentes y conservación exacta de los valores originales en la base de pruebas.
- Las pruebas de documentos y aislamiento, junto con estos casos, aprobaron: **22 pruebas, 300 comprobaciones**. SQLite en memoria; correo, notificaciones y cola simulados, sin llamadas HTTP externas permitidas.
- En Brave, el expediente existente del cliente muestra 17:45 y 17:20 para las indicaciones, y 17:36 para el mensaje del asesor. Antes se veían 22:45, 22:20 y 22:36. Se mantienen dos documentos, dos indicaciones y un mensaje; no se enviaron formularios durante esta revisión.
- Evidencias: `.local/logs/pruebas-horario-colombia.xml` y `.local/logs/horario-colombia-expediente.jpg`.
- Entorno local verificado: SQLite en D, correo en archivos, pagos externos desactivados. Cambio guardado únicamente en Git local, sin publicación ni despliegue. Si se despliega más adelante, deberá regenerarse la caché de configuración para incluir la nueva opción.

Este bloque cubre el expediente. No modifica otras pantallas, correos ni reglas de vencimiento; la suite completa mantiene pendientes los fallos anteriores documentados. Siguiente revisión propuesta: comprobar con datos ficticios qué puede consultar y hacer un cliente cuando vence su servicio, conservando su expediente.

## Bloque 11: revisión del vencimiento sin perder el expediente (3 de noviembre de 2026)

Se añadieron ocho casos en `tests/Feature/ServiceExpiryReviewTest.php`. Los clientes, asesores, documentos y servicios de este bloque existen únicamente en SQLite `:memory:` y almacenamiento simulado. Se fijó el reloj de las pruebas en noviembre de 2030; no se cambió el reloj del PC, del servidor local ni la suscripción del cliente que se usa en Brave. La TRM se simuló, se bloquearon solicitudes HTTP externas y se simularon correo, cola y notificaciones.

### Comportamiento actual comprobado

- El último segundo del día UTC de vencimiento aún permite acceder. Al comenzar el día siguiente UTC, se pierde el acceso: un servicio con fecha final 03/11/2030 deja de funcionar a las **19:00 del 03/11/2030 en Colombia**, cinco horas antes del fin del día colombiano. La mejora de presentación del bloque 10 no cambió esta regla.
- Inicio sigue disponible y muestra Período de carga finalizado / Consulta las condiciones de tu servicio. La acción dirige a Planes, que sigue accesible y ofrece contratación; no muestra un resumen específico del servicio vencido en esta prueba.
- El expediente completo redirige a Planes con un aviso de membresía activa requerida. El cliente no puede consultar allí indicaciones, mensajes ni devoluciones. Los enlaces directos a ambos PDF devuelven 403.
- Los envíos de texto y archivos también se bloquean; las solicitudes JSON reciben 403. Se compararon exactamente ocho tablas de negocio y las huellas de los archivos antes y después de los intentos: no se modificó el expediente, ni se generaron pagos u órdenes.
- Asesor asignado y administración conservan acceso de lectura. El asesor puede incluso enviar una nueva devolución al expediente vencido; el cliente no puede abrirla mientras no recupere un servicio activo. Esta prueba también se ejecutó exclusivamente con datos efímeros y comunicaciones simuladas.
- Registrar una nueva suscripción ficticia vigente, por valor cero y sin pasar por la pasarela, devuelve el acceso al mismo expediente y a los mismos documentos, notas y mensajes. No duplica ni sustituye el expediente. Esto verifica la condición de acceso, no un proceso real de renovación o cobro.

### Resultado y conservación

**30 pruebas aprobadas, 430 comprobaciones**: ocho de vencimiento y 22 anteriores de documentos, aislamiento y horario. Formato del nuevo archivo aprobado. Evidencia automatizada: `.local/logs/pruebas-vencimiento.xml`.

Comprobación independiente de la base local persistente: integridad SQLite correcta; cliente de prueba con servicio activo; proyecto Entregado; dos documentos, dos indicaciones, un mensaje; cero trabajos en cola, pagos y órdenes Wompi. Los dos PDF coinciden por SHA-256 con los archivos ficticios originales. Informe: `.local/logs/comprobacion-vencimiento.json`.

Este bloque agrega pruebas y documentación; no cambia las reglas actuales de la app. Las pruebas de límite horario describen expresamente el comportamiento encontrado, no lo validan como política deseada. No hubo cambios en Hostinger, publicación en GitHub, cobros ni correos reales. No se repitió toda la suite; siguen pendientes los fallos anteriores documentados.

### Mejora propuesta para el siguiente bloque

Corregir primero el cálculo de vigencia para respetar el día completo de Colombia, manteniendo UTC para los registros de eventos. Revisar juntos los criterios de servicio activo, programado y vencido para que el acceso y la orientación coincidan. Después conviene definir si el cliente conservará consulta de solo lectura tras el vencimiento: el aviso actual menciona únicamente la carga, aunque también se bloquea la consulta de lo ya entregado. Esta política de lectura no se ha cambiado.

## Bloque 12: vigencia hasta el fin del día de Colombia (3 de noviembre de 2026)

Se corrigió el cierre anticipado de las 19:00. Las fechas de inicio y final del servicio se comparan con el día calendario de `America/Bogota`. La fecha final es inclusiva: permite el acceso hasta las 23:59:59 de Colombia y lo bloquea desde las 00:00 del día siguiente. El inicio también espera la medianoche de Colombia; ya no se anticipa a la tarde anterior.

Se mantuvo el mismo calendario en los avisos de servicio programado/vencido, la disponibilidad de acuerdos pendientes y la validación administrativa de su fecha final. Las nuevas suscripciones generadas por una aprobación de pago toman la fecha colombiana. Los registros de aprobación y otros eventos siguen en UTC. El cálculo de duración conserva las reglas existentes; no se cambian importes, estados de pago ni requisitos de autorización. El aviso de carga ahora aclara que la fecha inclusive corresponde a Colombia.

### Validación

Primero se reprodujeron cinco fallos con las expectativas corregidas. Después del cambio aprobaron **47 pruebas y 556 comprobaciones**: vencimiento, acceso a documentos, aislamiento, presentación de fechas y pagos simulados.

- Acceso permitido a las 18:59:59, 19:00:00 y 23:59:59 del último día colombiano; bloqueado a las 00:00 del siguiente. PDF y orientación al cliente coinciden con el acceso.
- Una respuesta enviada en el último segundo se conserva; la enviada después de medianoche se bloquea sin modificar el expediente.
- Servicios futuros permanecen programados hasta su fecha local de inicio.
- Acuerdos que vencen hoy siguen disponibles y admiten validación administrativa durante la noche colombiana; al día siguiente vencen. Permanecen pendientes de pago y no dan acceso por sí mismos.
- Aprobaciones simuladas de Corrección Completa y Modo Indexación durante la noche generan el inicio en la fecha colombiana, conservando el registro de aprobación en UTC. No se contactó Wompi ni se hicieron cobros reales.

Evidencias: `.local/logs/pruebas-vigencia-colombia.xml`, `.local/logs/comprobacion-vigencia-colombia.json` y `.local/logs/vigencia-horario-colombia.jpg`. Se verificaron los mismos recuentos del expediente persistente, servicio activo, estado Entregado y coincidencia SHA-256 de ambos PDF originales. Correo local y pagos externos desactivados.

La corrección está guardada en Git local. No se publicó en GitHub ni se desplegó en Hostinger. No se cambiaron las fechas existentes ni se ejecutaron migraciones. El bloqueo de lectura una vez vencido el servicio sigue siendo la política anterior; queda pendiente definir si se ofrecerá consulta de solo lectura. Los fallos antiguos de la suite completa siguen fuera de este bloque.

## Bloque 13: consulta del expediente después del vencimiento (3 de noviembre de 2026)

El cliente con un servicio pagado vencido conserva acceso de lectura a su expediente, PDF, indicaciones y mensajes. Las nuevas cargas y respuestas requieren un servicio activo. El final de vigencia sigue calculándose con el día completo de Colombia.

### Comportamiento implementado

- La ruta GET del expediente admite un servicio pagado vigente o vencido; el POST conserva el middleware de membresía activa. Intentar enviar texto, archivos o JSON directamente sin vigencia sigue bloqueado.
- La política de archivos admite lectura histórica del propietario y exige que el documento pertenezca al cliente autenticado. Ser el cargador de un archivo atribuido a otro cliente no concede lectura.
- Se comparte la condición de servicio pagado vencido entre orientación y autorización. Se mantienen las restricciones para servicios cancelados, no pagados, fechas incoherentes y servicios únicamente futuros. Una renovación futura no oculta el historial de un servicio anterior pagado vencido.
- Inicio y el menú ofrecen acceso al expediente. Sin vigencia, el menú se llama Mi expediente, la pantalla muestra Solo consulta y explica cómo continuar; no se renderizan el formulario, los campos de archivos/respuesta ni Responder al asesor.
- El asesor puede compartir una devolución posterior al vencimiento y el cliente puede leerla. Una renovación vigente devuelve los controles de envío al mismo expediente.

### Pruebas y demostración local

**54 pruebas aprobadas, 660 comprobaciones** en los bloques de vencimiento, aislamiento, documentos y pagos simulados. Se verificaron los límites de medianoche, la ausencia de controles de envío, el bloqueo de POST/JSON, la conservación de datos ante envíos rechazados, la renovación y el rechazo de documentos ajenos incluso con un cargador manipulado. Evidencia: `.local/logs/pruebas-solo-consulta.xml`.

Para revisar Brave se creó una cuenta adicional exclusivamente local: **consulta-vencida@propaps.test**, Cliente Consulta Vencida, con la misma clave local de pruebas registrada en `.local/accesos-pruebas.txt`. Tiene un servicio ficticio pagado de valor cero finalizado el día anterior, un expediente Entregado, dos copias de los PDF ficticios, una indicación y un mensaje. La preparación está en `.local/preparar-cliente-consulta.php`; no repetirla si la cuenta ya existe.

Respaldo previo: `.local/backups/antes-solo-consulta-20261103-235613.sqlite`. Se compararon exactamente usuario, suscripciones, documentos, mensajes y asignación del cliente original antes y después de crear la demostración: sin cambios. El cliente original conserva su servicio activo y sus documentos originales; las copias de prueba tienen rutas propias. Cero pagos, órdenes Wompi y trabajos de correo. Informe: `.local/logs/comprobacion-solo-consulta.json`.

En Brave se comprobó Inicio, entrada por Mi expediente, lectura del historial y apertura visual de la devolución PDF con el servicio vencido. El DOM del expediente contiene cero campos de archivos, cero áreas de respuesta y cero formularios de envío. Evidencia: `.local/logs/expediente-solo-consulta.jpg`. Se dejó abierta la cuenta ficticia vencida para revisión del usuario.

La versión se conserva en Git local. Sin publicación en GitHub, cambios en Hostinger, correos ni cobros reales. Las fechas de los servicios existentes no se modifican. La cuenta demostrativa y sus datos pertenecen solo al entorno local y no forman parte del commit. Los fallos antiguos de la suite completa siguen pendientes; no se ejecutó toda la suite en este bloque.

## Bloque 14: recuperación de contraseña y conservación del expediente (3 de noviembre de 2026)

Se revisó la recuperación con cuentas ficticias creadas únicamente en SQLite `:memory:`, almacenamiento simulado y reloj de pruebas. No se cambiaron contraseñas de las cuentas persistentes del navegador ni de producción.

Los tres fallos antiguos de `PasswordResetTest.php` se reprodujeron: las pruebas esperaban la notificación estándar `ResetPassword`, pero el modelo envía `AccountPasswordReset`, la notificación personalizada de Propaps. Se corrigió la expectativa y se utilizó el nombre de ruta POST `password.email`. No fue necesario modificar el código de recuperación de la aplicación.

Se añadió `tests/Feature/Auth/PasswordRecoveryLifecycleTest.php` con nueve casos:

- Solicitud y renderizado real del correo personalizado mediante transporte `log`, en un archivo de almacenamiento de pruebas. Se verifica su texto, duración de 60 minutos y enlace local; se abre la vista y se completa el restablecimiento mediante peticiones del entorno de pruebas.
- Rechazo de enlaces vencidos, inválidos, ya utilizados y asociados a otra cuenta, conservando la contraseña ante cada rechazo.
- Una solicitud inmediata se limita sin reemplazar el enlace vigente; pasada la espera configurada, un nuevo enlace invalida el anterior.
- Una confirmación de contraseña distinta se rechaza sin consumir el enlace válido.
- Recuperación e inicio de sesión de clientes con servicio activo y vencido: la contraseña anterior falla, la nueva funciona y se renueva el token de Recordarme. Se conserva exactamente el resto de los atributos de la cuenta y las tablas del expediente, junto con las huellas de los PDF. El servicio vencido continúa en solo consulta y no permite responder; no se adquieren permisos administrativos.

### Resultados y conservación

Las cuatro pruebas existentes corregidas más los nueve casos nuevos aprobaron: **13 pruebas, 151 comprobaciones**. La ejecución conjunta con inicio de sesión, vencimiento, documentos y aislamiento aprobó: **61 pruebas, 787 comprobaciones**. Evidencias: `.local/logs/pruebas-recuperacion.xml` y `.local/logs/pruebas-recuperacion-y-acceso.xml`.

La mayoría de casos simula notificaciones. El caso de integración renderiza el correo completo con el transporte local de archivos; su registro vive en `storage/framework/testing/disks/recovery-mail` y se limpia al preparar el siguiente caso. No se usan SMTP ni proveedores externos. Las solicitudes HTTP externas están bloqueadas. No se conservan enlaces de recuperación reutilizables en la documentación ni se muestran claves o tokens en el informe.

Comprobación independiente de la base persistente: cliente original activo con dos PDF, dos indicaciones y un mensaje; cliente Consulta Vencida con dos PDF, una indicación y un mensaje, manteniendo lectura habilitada y escritura deshabilitada. Los cuatro PDF coinciden con sus originales ficticios. Cero tokens de recuperación, trabajos de correo, pagos y órdenes en esa base. Informe: `.local/logs/comprobacion-recuperacion.json`.

Este bloque usa peticiones automatizadas al framework; no realiza un cambio de contraseña mediante la interfaz de Brave ni una entrega real de correo. Las peticiones de prueba no sustituyen la comprobación de CSRF o la entrega SMTP de producción. Se conserva la configuración de contraseñas existente. La suite completa no se repitió; quedan fuera del bloque los otros fallos previamente documentados.

Cambio guardado únicamente en Git local: pruebas y documentación. Sin publicación en GitHub, despliegue en Hostinger, cobros ni correos reales. Siguiente bloque sugerido: revisar el registro de una cuenta nueva y la aceptación de condiciones, donde aún hay pruebas antiguas pendientes.

## Bloque 15: registro y aceptación de condiciones (3 de noviembre de 2026)

Se revisó el alta de cuentas con datos ficticios en SQLite `:memory:`, correo y notificaciones simulados, y solicitudes HTTP externas bloqueadas. No se cambió el código de registro ni los textos de términos y privacidad.

La prueba antigua de registro fallaba porque no enviaba la aceptación obligatoria. Se añadió `terms=1` al caso válido y se simularon notificaciones. La validación real del servidor se conserva: rechaza solicitudes sin aceptación afirmativa.

Se añadieron 15 casos en `tests/Feature/Auth/RegistrationConsentTest.php`:

- Casilla obligatoria sin marcar inicialmente y enlaces accesibles a términos y privacidad.
- Rechazo de aceptación ausente, falsa, cero o negativa, sin crear usuario ni registro de aceptación; mensaje de validación explícito.
- Alta como cliente activo, contraseña cifrada y correo normalizado; manipular rol, estado, verificación, identidad de Google, versión aceptada o datos de pago no cambia los valores elegidos por el servidor.
- Registro de aceptación asociado al usuario, con fecha, método de registro y versiones de términos y privacidad `2026-10-04`.
- Sin suscripción, pago, orden, documentos ni proyecto automáticos. La cuenta puede iniciar sesión, pero no cargar archivos, responder ni entrar a administración.
- Rechazo de datos inválidos y correo duplicado sin sobrescribir la cuenta existente ni añadir aceptaciones.
- Si falla el registro de aceptación, la transacción revierte la creación del usuario.
- Con verificación de correo activada solo en la prueba se genera la notificación simulada y se exige verificar; con ella desactivada, se respeta la configuración y no se habilita una membresía.
- Google simulado: no se crea la cuenta al recibir la identidad; se exige aceptación posterior y se ignoran identidad y rol enviados desde el formulario. No se contacta a Google. Este caso no verifica el proveedor OAuth real ni su validación de estado, que está sustituida por el simulador.

### Resultados y conservación

Las dos pruebas existentes de registro y los 15 casos nuevos aprobaron en la ejecución conjunta con recuperación, autenticación y permisos: **78 pruebas, 1.023 comprobaciones**. Evidencia final: `.local/logs/pruebas-registro-y-acceso.xml`. Las pruebas antiguas fuera de esos bloques siguen pendientes de revisión; no se ejecutó la suite completa.

Se comprobó por separado el mensaje de rechazo con una sesión HTTP real del servidor local, token CSRF y un registro ficticio **sin aceptar condiciones**. El formulario mostró Debes aceptar los términos y la política de privacidad. La cuenta `rechazo-sin-consentimiento@propaps.test` no se creó. Evidencia: `.local/logs/registro-rechazado-http.json`. La aserción automática comprueba el mensaje de sesión; la comprobación visual del HTML tras redirección se hizo en esta solicitud local, separada de la sesión en memoria de PHPUnit.

El servidor local estaba detenido y se volvió a iniciar con el script habitual, tras verificar SQLite en D, correo en archivos y pagos externos desactivados. Quedó disponible en `http://127.0.0.1:8000`.

La base persistente conserva cuatro cuentas de prueba, los mismos recuentos y permisos de ambos expedientes, y cero trabajos de correo, pagos y órdenes. Informe: `.local/logs/comprobacion-registro.json`. No se crearon cuentas persistentes nuevas ni se modificaron contraseñas existentes. No se aceptaron condiciones mediante una cuenta real.

Versión guardada únicamente en Git local: pruebas y documentación. Sin publicación en GitHub, despliegue en Hostinger, cobros ni correos reales. Siguiente bloque sugerido: revisar las pruebas antiguas restantes del panel y acceso a servicios para completar la validación local pendiente.
