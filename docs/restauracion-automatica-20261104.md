# Restauración de la copia automática — 4 de noviembre de 2026

Resultado: restauración aislada satisfactoria del punto automático mostrado por Hostinger como **2026-11-03 23:20**. Se recuperó el código de ese punto, su SQL y los siete documentos privados. No se modificó ni se contactó propaps.com durante esta prueba; no se enviaron correos ni se ejecutaron cobros.

## Copia utilizada

- Archivo completo del plan: 24.883.457.231 bytes; SHA256 `f73eb0c6108ccfe1de73fb84d1ebe72dd2ce95997a74fe481f17c7cac9308a08`.
- SQL automático comprimido: SHA256 `5feeaf1659c13091634a9495553ce1a53742be2844c60b57d571bd8d18b828d9`.
- Se comprobó la presencia de `domains/propaps.com/secure_paper`, su configuración privada, documentos y la entrada pública de `public_html`.
- La extracción de la aplicación utilizó solo archivos regulares seleccionados de Propaps. Se excluyeron otros sitios, Git, sesiones, cachés, registros y enlaces simbólicos. Se recuperaron también las dependencias y el build incluidos en el respaldo; no se descargaron dependencias nuevas.

## Aislamiento

Carpeta nueva: `D:\PROYECTOS\secure_paper\.local\restauracion-automatica-20261104`, con acceso restringido y excluida de Git. Aplicación: `extracted/domains/propaps.com/secure_paper` dentro de esa carpeta.

Se reutilizó el motor portátil MariaDB, escuchando exclusivamente en `127.0.0.1:13367`, sin eventos programados ni servicio Windows. La base nueva `propaps_auto_restore_20261104` se creó sin sobrescribir las dos restauraciones anteriores. El usuario de la aplicación tiene únicamente SELECT; una sentencia UPDATE sin filas confirmó que las escrituras son rechazadas.

La configuración de producción extraída se conservó fuera de la raíz de la aplicación y se sustituyó por una configuración local, manteniendo la APP_KEY original. No hay claves de Wompi, Google ni SMTP en la configuración de ejecución. Correo en memoria, avisos desactivados, sesiones y caché en memoria. Durante las comprobaciones se simularon correo y colas y se bloquearon solicitudes HTTP externas; PHP también tuvo deshabilitados los mecanismos de red usados por correo y HTTP. No se inició ningún trabajador ni servidor web.

## Conservación comprobada

- 27 tablas, 10 usuarios, 7 documentos y 3 mensajes recuperados.
- Las 27 tablas coinciden con la importación previa e independiente del mismo SQL automático.
- CHECK TABLE satisfactorio en las 27 tablas y ninguna referencia huérfana en las claves foráneas declaradas.
- Los siete archivos coinciden con las huellas originales de referencia, tanto en almacenamiento como en las respuestas de descarga autorizadas para administración.
- Tras las pruebas, las 27 tablas y los siete documentos permanecen idénticos. Colas y trabajos fallidos: cero.
- El `.env` y la base SQLite del proyecto habitual conservan sus huellas anteriores a la prueba.

## Aplicación y alcance funcional

Se ejecutaron 16 solicitudes GET a través del kernel HTTP de Laravel desde CLI: 15 respuestas 200 y una redirección 302 prevista por el código restaurado. Inicio, acceso, salud, paneles, usuarios, listado de proyectos y expediente administrativo renderizaron correctamente. Los tres mensajes y los siete enlaces documentales están presentes en el expediente; las siete respuestas de archivo coinciden con los documentos originales.

Los actores se establecieron en memoria para esta revisión. No se cambiaron contraseñas ni se probaron autenticación real, Google, correos, pagos, escrituras o recorridos visuales en navegador. Las respuestas de archivo se validaron contra su archivo servido, sin transmitir documentos por una red.

**Limitación del punto recuperado:** el cliente dueño del expediente no tiene una membresía activa según esta versión; el acceso al módulo de documentos devuelve 302. El middleware de esta copia redirige ese caso a planes. No se alteró su servicio para eludir el control. El respaldo es anterior a las mejoras publicadas el 4 de noviembre, incluido el acceso de consulta tras el vencimiento. Esta prueba recupera el estado histórico; no acredita que incluya la versión actual del servidor. Antes de una recuperación real con código actual, probar por separado sus migraciones y políticas de acceso.

Windows extrajo siete nombres de vistas con el prefijo Unicode mal interpretado. Se normalizaron a `⚡` solo en la copia aislada y se confirmó que su contenido no cambió; el archivo de respaldo permanece intacto. Para futuras extracciones en Windows, verificar explícitamente estos nombres antes de ejecutar Livewire.

El dump procede de MariaDB 11.8.9 y la prueba empleó 10.11.14. La importación y lecturas de esta copia fueron satisfactorias; no equivalen a una validación en la versión exacta del alojamiento ni a una garantía para cualquier respaldo futuro. Las comparaciones de datos se realizaron con zona UTC.

## Cierre y evidencias

Se solicitó el apagado limpio del motor portátil y se confirmó que el puerto 13367 dejó de escuchar. La carpeta restaurada se conserva para las siguientes pruebas, sin procesos de aplicación expuestos.

Evidencias privadas: `database-restore-result.json`, `app-preparation-result.json`, `app-smoke-result.json`, `tables-before-private.json`, `unicode-filenames-repaired.json` y `closure.json`. Los programas `import-audit.php`, `prepare-app.php`, `smoke-app.php` y `verify-close.php` permanecen junto a esas evidencias. No subir SQL, documentos, configuración ni credenciales a GitHub.

## Procedimiento para otra prueba

1. Elegir archivos y SQL del mismo punto y verificar sus huellas. Conservar también cualquier información posterior que sea necesaria; nunca sobrescribir producción como parte de una prueba.
2. Crear otra carpeta privada y otra base vacía. Revisar las rutas y tipos de los miembros antes de extraer únicamente Propaps.
3. Aislar configuración, base, correo, pagos, colas y conexiones externas antes de iniciar Laravel. Usar un usuario de solo lectura para las primeras comprobaciones.
4. Importar el SQL, comprobar tablas, referencias y documentos; revisar páginas, mensajes y archivos con el código de ese respaldo.
5. Comparar los datos antes y después, apagar los procesos de prueba y conservar evidencias. Una prueba posterior de actualización debe hacerse sobre otra copia, manteniendo esta restauración histórica.

## Prueba posterior de actualización

La restauración histórica descrita aquí se conservó intacta. Se creó otra copia con el código actual y se aplicaron sus tres migraciones pendientes: el cliente con servicio vencido ya puede consultar documentos, mensajes e indicaciones, mientras siguen bloqueados los envíos. Los datos originales y ambas copias quedaron verificados. Véase [Actualización de una restauración automática](actualizacion-restauracion-20261104.md) para resultados y límites.
