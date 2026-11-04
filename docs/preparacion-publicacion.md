# Preparación de publicación de Propaps

Revisión: 4 de noviembre de 2026, horario de Colombia.
Proyecto local: `D:\PROYECTOS\secure_paper`.
Repositorio de la aplicación: `ZapataMor/secure_paper`.

## Estado por bloques

1. Configuración local: revisada. Seis cuentas ficticias, siete documentos presentes, 27 migraciones aplicadas, cero trabajos de correo, pagos y órdenes Wompi. El expediente de vencimiento/renovación mantiene cinco de esos documentos. PHP local 8.5.11 cumple los requisitos de Composer.
2. Comprobación funcional local: 225 pruebas PHP aprobadas (3.213 comprobaciones) y 12 pruebas JavaScript de borradores. Incluye recuperación, registro, verificación, pagos simulados, permisos, expedientes y doce casos nuevos de avisos en cola. Compilación Vite correcta. La revisión móvil ya quedó documentada en los bloques 9 y 25 del entorno local; en esta revisión no se volvió a hacer inspección visual.
3. Integraciones: lógica revisada con comunicaciones simuladas. En Hostinger se confirmó configuración SMTP, credenciales presentes de Google y retorno correcto, Wompi production listo, sandbox_access=false y trabajador completando su tarea sin trabajos fallidos. No se envió correo real ni se repitió el pago real previo; no se ensayó un nuevo login externo de Google.
4. Publicación del código: v27 y v28 subidas con todo su historial y etiquetas; main integrado por avance directo. Se retiró del panel la conexión al repositorio ajeno. El repositorio Git real de secure_paper usa ZapataMor/secure_paper. Las actualizaciones se aplican por SSH siguiendo este procedimiento; no se habilitó un despliegue automático que sobrescriba public_html.
5. Actualización del servidor: aplicada. Tres migraciones nuevas, recursos compilados copiados a las dos carpetas públicas y cachés regeneradas. Se conserva APP_KEY, credenciales, documentos y adaptación de public_html/index.php. Se fijó SESSION_SECURE_COOKIE=true.
6. Aceptación en producción: pasaron consultas de cliente, asesor y administrador con cuentas temporales ficticias, apertura de PDF con igualdad de bytes y rechazo del cliente en administración. Se retiraron los datos temporales. Los registros y documentos originales coinciden exactamente con los anteriores a la actualización. No se probaron envíos de correo ni cobros reales.

## Evidencias locales y conservación

Los informes privados `.local/logs/preparacion-v27-antes.json` y `preparacion-v27-despues.json` contienen recuentos y huellas de los registros y documentos, sin el contenido de los expedientes. El primero identifica el respaldo SQLite realizado antes de esta revisión. Pruebas: `.local/logs/pruebas-completas-v27.xml`, `pruebas-completas-v27.txt`, `avisos-cola-v27.txt`, `borradores-v27.txt` y `compilacion-v27.txt`.

Las pruebas usan SQLite en memoria. Los avisos nuevos usan Mail::fake y un transporte de respaldo en memoria, aunque el nombre del correo sea smtp para ejercer la condición del trabajo. No se usa SMTP real. Se comprueba de nuevo el destinatario al ejecutar el trabajo: reasignación, desactivación, eliminación lógica, correo no verificado, cliente inactivo, administrador retirado de la lista y otro cliente. Los avisos autorizados no adjuntan documentos ni incluyen el título del expediente.

La plantilla `.env.example` ahora deja `PROPAPS_PROJECT_EMAIL_ENABLED=false` y `WOMPI_SANDBOX_ACCESS=false`. `.gitignore` excluye todas las variantes privadas `.env.*` y permite expresamente las dos plantillas sin credenciales. Estos cambios no modifican el `.env` efectivo de ninguna instalación.

## Hallazgos de Hostinger

Comprobación visual de hPanel, de solo lectura:

- propaps.com figura como sitio PHP/HTML, con PHP 8.4 seleccionado.
- Límites del panel: maxFileUploads 20, uploadMaxFilesize 2048M, postMaxSize 2048M. La aplicación limita a diez archivos de 20 MB; comprobar también cualquier `.user.ini` y la configuración efectiva de PHP web. No se modificaron los límites del plan.
- Existe un cron cada minuto que ejecuta `worker-project-mail-v29.php` dentro de `domains/propaps.com/secure_paper`. Falta comprobar su resultado reciente y la versión de PHP CLI.
- El panel GIT muestra `Androide001/aplicativo-aburra`, rama main, directorio public_html y último despliegue manual del 10 de septiembre de 2026. **Esta conexión no corresponde a este repositorio. No utilizar Redistribuir para actualizar Propaps.** Revisar el estado efectivo de implementación automática y corregir la vinculación como una operación separada y controlada, después del respaldo.
- SSH figura activo, pero la autenticación automática disponible en este equipo fue rechazada. No se cambiaron contraseñas ni se añadieron claves.

La presencia de un cron no demuestra que esté enviando correos correctamente. La configuración de PHP en el panel tampoco demuestra que la versión CLI sea la misma.

## Configuración que debe verificarse en el servidor

No reemplazar `.env` por ninguna plantilla local. Consultar únicamente estados y presencia de valores; no imprimir claves ni credenciales.

| Área | Comprobación necesaria |
| --- | --- |
| Aplicación | APP_ENV=production, APP_DEBUG=false, APP_URL=https://propaps.com. Conservar exactamente APP_KEY. |
| Base de datos | Conexión MySQL existente; comparar migraciones antes de ejecutar las pendientes. Nunca copiar SQLite local ni ejecutar seeders de prueba. |
| Sesiones | SESSION_DRIVER=database, cookies seguras y HttpOnly, SameSite=lax; comprobar respuesta HTTPS efectiva. |
| Archivos | Documentos en storage/app/private; descarga mediante rutas autorizadas. Solo public debe ser accesible por HTTP. Conservar documentos actuales y permisos de escritura de storage/bootstrap/cache. |
| Correo | MAIL_MAILER=smtp, remitente y servidor válidos; MAIL_URL debe ser null si se usan campos SMTP separados (no cadena vacía). Conservar secretos existentes. |
| Avisos | Cola database/project-mail; revisar cron, informe privado del trabajador y failed_jobs. No reintentar trabajos antiguos en bloque sin revisar destinatarios. |
| Verificación | PROPAPS_EMAIL_VERIFICATION_REQUIRED solo se activa después de comprobar entrega, considerando las cuentas existentes sin verificar. |
| Wompi | Ambiente production, presencia y prefijos compatibles de las cuatro claves, WOMPI_SANDBOX_ACCESS=false. Revisar endpoint `/pagos/wompi/eventos/production` y retorno sin crear cobros nuevos. |
| Google | Credenciales existentes y URI HTTPS `/auth/google/callback`; no reemplazar por URI local. |
| PHP | PHP >=8.4 con el composer.lock actual y `composer check-platform-reqs --no-dev` correcto con el binario CLI usado. Revisar PDO MySQL, cURL, OpenSSL, fileinfo y los requisitos del lock. |
| Frontend | Copiar el build generado completo, con manifest.json y todos los assets. public/build está ignorado por Git: subir código no lo genera por sí solo. |

El trabajador actual usa la cola project-mail. Ejecutar `php artisan queue:work` sin indicar esa cola no sustituye este cron. `routes/console.php` no define tareas de negocio: no hace falta añadir otro programador para las funciones revisadas.

## Procedimiento para futuras actualizaciones

1. Obtener acceso SSH a la carpeta correcta. Leer `pwd`, `git status --short --branch`, `git log -1 --oneline`, `php -v` y `php artisan migrate:status`; comprobar las rutas reales de entrada y almacenamiento. Cualquier modificación de servidor debe compararse y conservarse antes de integrar código.
2. Confirmar que la versión de GitHub seleccionada corresponde a la revisión aprobada. Resolver la conexión ajena mostrada por hPanel sin pulsar Redistribuir ni sobrescribir public_html.
3. Preparar respaldo privado de archivos, `.env`, documentos y base de datos MySQL. Verificar tamaño, integridad del archivo y restauración del dump en una base aislada. Registrar hora, commit y huellas; no exponer el respaldo bajo public_html.
4. Preparar dependencias desde composer.lock y assets desde package-lock.json. Usar `composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction` y `npm ci`/`npm run build` en un entorno apropiado. No usar `composer update`, `composer setup` ni `key:generate` en producción.
5. Coordinar una ventana breve sin escrituras. Pausar el trabajador, dejar acabar trabajos en curso y comprobar órdenes de pago pendientes. Considerar los reintentos del webhook durante mantenimiento antes de abrir la ventana. Conservar el sitio anterior como versión recuperable.
6. Instalar el código revisado preservando configuración, storage y adaptación de la entrada pública. Aplicar únicamente migraciones pendientes mediante `php artisan migrate --force`. Las novedades desde v19 son document_submissions, work_submissions y users.session_epoch (tres migraciones del 3 de noviembre). No eliminar tablas ni registros.
7. Regenerar cachés de configuración y vistas con la configuración definitiva, compilar/copiar assets y normalizar únicamente los directorios del build público a 755 y sus archivos a 644. Verificar por HTTPS cada CSS/JS referenciado y su tipo de contenido, además de inspeccionar la página en navegador. Abrir el servicio y reanudar el trabajador solo con la configuración verificada. No iniciar envíos reales como parte de una prueba ficticia.
8. Comprobar inicio, login, paneles por rol, documentos autorizados, rechazo de accesos cruzados y expediente existente. Comparar recuentos/huellas del respaldo. Comprobar logs e informe del trabajador sin publicar datos personales. Las comprobaciones no deben crear cargos ni enviar correos reales.

## Restauración si falla

Detener nuevas escrituras y el trabajador. Si la base se conserva compatible, recuperar el código, dependencias y assets anteriores, mantener los datos nuevos y regenerar cachés; estas tres migraciones son aditivas, pero comprobar compatibilidad concreta antes de volver atrás. No ejecutar automáticamente migrate:rollback: eliminaría comprobantes de envíos y la marca de revocación.

Restaurar una base de datos completa solo si es necesario y tras reconciliar cualquier escritura o pago posterior al respaldo. Una restauración ciega puede perder expedientes o aprobaciones recientes. Conservar copia del estado fallido para análisis y registrar lo ejecutado.

## Condición de cierre

No considerar publicada la versión por un simple HTTP 200. Deben coincidir el código elegido, el esquema, los assets y los documentos; funcionar los recorridos relevantes; estar probado el respaldo; y quedar registrado el resultado real de las integraciones. Los puntos pendientes de servidor permanecen abiertos hasta obtener esa evidencia.
## Resultado de la integración del 4 de noviembre

La versión funcional aplicada fue cf8d1da (v28), idéntica al commit probado en la copia privada de Hostinger con PHP 8.4.27: 225 pruebas y 3.213 comprobaciones. La versión v29 añade documentación de cierre y corrige la matriz de GitHub Actions a PHP 8.4/8.5, acorde con Symfony 8 ya fijado en el lock; no cambia dependencias ni comportamiento de la aplicación. También ejecuta las doce pruebas de borradores y usa npm ci.

Respaldos e informes privados en `/home/u826249991/propaps-integracion-20261104`, fuera de public_html: `files-before.tar.gz` (archivos, configuración y documentos), `database-before.sql.gz` y `database-window.sql.gz` (segundo dump durante mantenimiento), `backups.sha256`, `audit-window-before.json`, `audit-after.json`, `audit-final.json`, `production-smoke.json`, `tests-php84.xml` y `deploy.log`. Se verificó la integridad de los archivos comprimidos y el listado del tar; no se realizó una restauración completa del dump MySQL en una segunda base de datos. Los respaldos y credenciales nunca se subieron a GitHub.

La ventana usó el mismo bloqueo de archivo que el trabajador de correos. Se conservó una salida de recuperación del código, configuración y assets ante fallo. La actualización terminó correctamente y el sitio volvió a estar disponible. El trabajador reanudó su ciclo normal.

HTTP público: inicio, login, planes y /up responden 200; el expediente sin sesión redirige al login (302); /.env responde 403. Las pruebas autenticadas de producción solo consultaron paneles y documentos, sin cargas ni mensajes por formularios. Los tres usuarios temporales, plan, servicio, asignación, mensaje, PDF y sesiones de prueba se retiraron. No se borró ningún registro anterior. La comparación final conserva los diez usuarios, siete documentos, tres mensajes, tres servicios y trece órdenes Wompi que ya existían. Cero trabajos de correo y cero trabajos fallidos al finalizar las comprobaciones.

La autenticación temporal SSH se autorizó expresamente para esta integración y debe revocarse al cerrar el trabajo. La clave privada queda excluida de Git. La vinculación de GitHub en el repositorio real y su historial no dependen de esa clave temporal.
## Cierre visual y permisos de los recursos públicos

La inspección visual final detectó que los archivos del build transferidos desde Windows habían quedado con permisos 700/600 y no eran legibles para el servidor web. Se corrigieron exclusivamente los dos directorios de build público (carpetas 755, archivos 644), conservando los permisos privados de documentos, configuración y respaldos. CSS y JavaScript volvieron a responder 200 y se verificó la presentación correcta en Brave. Un HTTP 200 de la página HTML por sí solo no bastaba para detectar este problema.

GitHub Actions de v29 terminó correctamente: comprobación de formato y batería PHP 8.4/8.5 con las pruebas JavaScript. Ejecución: https://github.com/ZapataMor/secure_paper/actions/runs/41876092566. V30 registra la corrección operativa de permisos y su verificación; no cambia el código funcional.