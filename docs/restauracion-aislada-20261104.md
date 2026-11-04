# Prueba de restauración aislada — 4 de noviembre de 2026

Resultado: satisfactoria para los respaldos conservados durante la publicación v30. La prueba se ejecutó íntegramente en este PC, dentro de `.local/restauracion-20261104`, sin conexión a Hostinger, sin arrancar Laravel, trabajadores de cola, correo ni pasarela de pagos. No se cambió la base SQLite habitual ni el `.env` del proyecto.

## Qué se recuperó

Se verificaron las huellas SHA256 de los dos archivos originales antes de utilizarlos:

- `database-window.sql.gz`: `76ebc8a9fa279be09e1aa3d7cd01fde4451bf6d9c82ae73e074437897f34a2c5`.
- `files-before.tar.gz`: `5897b0288cfc7fadad188ea03140ba3b6796b5a40e2c9abb9f7dec71cf2c8fcf`.

El SQL se importó en una base nueva llamada `propaps_restore_20261104`, con 27 tablas y 24 migraciones iniciales. Los siete documentos se extrajeron a almacenamiento privado independiente. El programa de restauración rechaza una base ya existente para evitar sobrescribirla.

La instancia portátil escuchó únicamente en `127.0.0.1:13367`, sin servicio Windows ni programador de eventos; su contraseña aleatoria y los datos restaurados permanecen privados y excluidos de Git. Al terminar se realizó un apagado limpio y se comprobó que dejó de escuchar.

## Evidencias de conservación

Los recuentos y huellas de las diez tablas de negocio recogidas en el informe de Hostinger coinciden exactamente: diez usuarios, siete documentos, tres mensajes, tres servicios, dos actividades y trece órdenes Wompi existentes; asignaciones, pagos, trabajos y trabajos fallidos vacíos. Las trece órdenes recuperadas son registros del respaldo: no se ejecutaron transacciones ni consultas a Wompi.

Los siete archivos coinciden byte por byte con sus SHA256 originales. `CHECK TABLE` resultó correcto para todas las tablas restauradas y no se encontraron referencias huérfanas en las claves foráneas declaradas.

Se aplicaron después, exclusivamente en esta copia, las tres migraciones del 3 de noviembre: document_submissions, work_submissions y users.session_epoch. Se volvió a comprobar la conservación de las 26 tablas originales distintas del registro de migraciones, además de los siete archivos. El resultado final tiene 29 tablas y 27 migraciones; todos los datos anteriores se mantienen. La comparación normaliza únicamente la nueva columna session_epoch cuando es nula.

## Precauciones comprobadas y alcance

La lectura de las fechas TIMESTAMP debe usar UTC, como el servidor original. La primera comparación con la zona horaria de Windows produjo huellas distintas; al establecer `SET time_zone='+00:00'` coincidieron sin modificar los registros. No convertir fechas para resolver esta diferencia de presentación.

El dump identifica MariaDB 11.8.9; la prueba utilizó la distribución portátil oficial MariaDB 10.11.14, con SHA256 oficial verificado. La importación, integridad y actualización de este respaldo concreto fueron satisfactorias. Esto no sustituye una prueba en la versión exacta del servidor ni garantiza respaldos futuros. Tampoco se probó una restauración sobre producción o la autenticación web con las cuentas recuperadas.

La instalación portátil sigue el procedimiento oficial: https://mariadb.com/docs/server/server-management/install-and-upgrade-mariadb/installing-mariadb/binary-packages/installing-mariadb-windows-zip-packages

## Registro privado y repetición

En `.local/restauracion-20261104` quedan `import-ok.json`, `audit-before.json`, `audit-after.json`, `all-tables-before.json`, `migrations-result.json`, `closure.json`, los programas de comprobación y la base apagada. No subir esta carpeta, los documentos, el SQL ni `connection.json` a GitHub. Para repetir, preparar otro directorio y otra base vacía; conservar estas evidencias.

Para una recuperación real: detener escrituras y el trabajador; reconciliar cualquier expediente o pago posterior al respaldo; restaurar en una base nueva; validar datos y documentos con UTC; aplicar solo las migraciones necesarias y comprobar la aplicación antes de decidir el cambio de base. Mantener APP_KEY y la configuración privada original. La prueba actual no autoriza sobrescribir datos recientes de producción.
