# Actualización de una restauración automática — 4 de noviembre de 2026

Resultado: satisfactoria. Se restauró otra copia del SQL automático del punto **2026-11-03 23:20**, se combinó con el código local actual `3737433a68146da6f9b42f05e3bfba83bb4d5373` y se aplicaron sus migraciones. El cliente con servicio pagado vencido puede consultar el expediente, sus indicaciones, los tres mensajes y los siete documentos. Los intentos de cargar archivos o responder se rechazan. No se modificó ni contactó propaps.com, no se enviaron correos y no se ejecutaron cobros.

## Copia independiente

- Carpeta privada: `D:\PROYECTOS\secure_paper\.local\actualizacion-restaurada-20261104`; aplicación dentro de `app`.
- Base nueva: `propaps_auto_upgrade_20261104`, en MariaDB portátil enlazado exclusivamente a `127.0.0.1:13367`, sin eventos programados ni servicio Windows.
- Se conservaron la restauración histórica, su base y sus documentos. El SQL se importó de nuevo sin sobrescribir ninguna base anterior.
- Se copiaron los 273 archivos versionados del código actual, conservando correctamente los nombres Unicode. Sus huellas permanecen iguales tras las pruebas.
- Las dependencias de la copia histórica son compatibles: su `composer.lock` coincide con el actual. Se reconstruyó el build con Vite y las dependencias ya disponibles localmente, sin descargar paquetes.
- Se mantuvo la APP_KEY original en configuración privada. El usuario normal de la aplicación tiene únicamente SELECT. El usuario temporal para migrar tuvo permisos limitados a la base nueva y se retiró al terminar.

La configuración local mantiene correo en memoria, avisos desactivados, Wompi sin claves, sesiones y caché en memoria. Las comprobaciones bloquearon HTTP externo y simularon correo y colas; PHP también tuvo deshabilitados los mecanismos de red usados por HTTP y correo. No se inició ningún trabajador ni servidor web.

## Migraciones verificadas

Se utilizó el migrador real de Laravel (`migrate --force`) contra la nueva base local. Las únicas pendientes eran:

1. `2026_11_03_210000_create_document_submissions_table`.
2. `2026_11_03_220000_create_work_submissions_table`.
3. `2026_11_03_230000_add_session_epoch_to_users`.

Resultado: de 24 a 27 migraciones registradas y de 27 a 29 tablas; ninguna migración pendiente. Las dos tablas de recibos de envío están vacías. Los diez usuarios tienen la nueva columna `session_epoch` nula, como establece la migración; no se cambiaron contraseñas ni servicios.

Se conservaron los registros de las 26 tablas originales distintas del registro de migraciones. La comparación normaliza exclusivamente la nueva columna nula de usuarios. `CHECK TABLE` fue correcto para las 29 tablas y no hay referencias huérfanas en las claves foráneas declaradas. Una segunda ejecución de `migrate` terminó correctamente sin cambiar ninguna tabla ni registro.

## Acceso del cliente y protección del expediente

Se hicieron 17 solicitudes a través del kernel HTTP de Laravel desde CLI: 13 respuestas 200, tres rechazos 403 previstos y una redirección 302 al acceso. Los actores se establecieron en memoria; no se utilizaron ni modificaron contraseñas reales.

- Inicio, acceso, salud y panel del cliente respondieron correctamente.
- Se confirmó que el cliente tiene un servicio pagado vencido. El expediente devuelve 200 y muestra **Solo consulta**, en lugar de la redirección a planes observada con el código histórico.
- Aparecen sus indicaciones, los tres mensajes y los siete enlaces documentales.
- Las siete respuestas de archivo autorizadas para el cliente coinciden con las huellas originales.
- No aparecen formularios para subir documentos ni responder.
- Se intentó enviar un PDF ficticio y una respuesta ficticia con sesión/CSRF y formato de solicitud adecuados. Ambos se rechazaron con 403 y el mensaje del control de membresía; no se contabilizó un rechazo CSRF o un error de permisos SQL como éxito de esta prueba.
- Otro cliente recibe 403 al intentar acceder a un documento ajeno. Una visita sin autenticar se redirige al acceso.
- Administración conserva acceso al expediente.

## Conservación y cierre

Después de las solicitudes, las 29 tablas coinciden exactamente con el estado posterior a las migraciones. Los siete documentos permanecen intactos y no hay recibos de envío, trabajos ni trabajos fallidos nuevos. Las 27 tablas y los siete documentos de la restauración histórica también coinciden con sus referencias anteriores. El `.env` y la base SQLite del proyecto habitual no cambiaron.

Se retiró el usuario temporal de migración, se apagó limpiamente MariaDB y se confirmó que el puerto 13367 dejó de escuchar. Las dos restauraciones se conservan separadas para poder compararlas.

Evidencias privadas en la carpeta nueva: `database-restore-result.json`, `migrations-result.json`, `client-access-result.json`, `tables-before-private.json`, `tables-after-migration-private.json`, `source-files-private.json`, `app-preparation-result.json` y `closure.json`. Los programas de esta prueba permanecen allí junto a las evidencias. No publicar esa carpeta, sus credenciales, documentos o SQL.

## Alcance y siguiente revisión

Esta prueba valida la actualización del respaldo concreto y la consulta del expediente con servicio vencido. No es un despliegue, una prueba visual en navegador, una comprobación de contraseñas reales ni una prueba de cobros, correo o envíos permitidos con servicio activo. El dump procede de MariaDB 11.8.9 y se probó en 10.11.14; se mantiene la limitación de versión indicada en la restauración anterior. Las comparaciones usan UTC.

Para una recuperación real habría que reconciliar los datos posteriores al punto del respaldo antes de cualquier cambio. Continúa pendiente confirmar en Hostinger un punto automático posterior a la publicación del 4 de noviembre; esta prueba local no acredita esa cobertura.

Siguiente revisión sugerida: ensayo completo de recuperación con tiempos y lista de comprobación, manteniendo las restauraciones aisladas y probando cualquier operación con escritura sobre otra copia con datos ficticios.

## Ciclo completo posterior

Se conservó esta copia actualizada sin cambios y se creó una tercera para probar escrituras con cuentas ficticias. El recorrido de carga, asignación, devolución, respuesta y privacidad pasó 76 comprobaciones mediante 52 solicitudes HTTP locales, sin pagos ni correos. Véase [Ciclo completo del expediente en copia aislada](ciclo-expediente-restaurado-20261104.md).
