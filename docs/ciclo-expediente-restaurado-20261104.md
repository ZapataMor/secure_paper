# Ciclo completo del expediente en copia aislada — 4 de noviembre de 2026

Resultado: satisfactorio. Se recorrió carga, asignación, devolución, respuesta y consulta final con cuentas ficticias en una tercera copia de la aplicación. Se realizaron **52 solicitudes HTTP locales y 76 comprobaciones**, todas satisfactorias. No se modificó ni contactó propaps.com; no se enviaron correos ni se hicieron cobros.

## Preparación y aislamiento

- Código de partida: `ce34f9f6c082c5c882a72e5c36a72189e3e30c87`, sin modificaciones de código de aplicación durante esta prueba.
- Carpeta privada: `D:\PROYECTOS\secure_paper\.local\ciclo-expediente-20261104`; aplicación en `app`.
- Base independiente: `propaps_cycle_20261104`, clonada desde la restauración actualizada. Se copiaron las 29 tablas con sus definiciones, restricciones e índices y se comprobó la igualdad inicial de datos.
- Usuario de aplicación con SELECT, INSERT, UPDATE y DELETE exclusivamente en esa base; sin permisos de esquema ni acceso a las bases anteriores.
- Motor portátil enlazado a `127.0.0.1:13367`, sin eventos ni servicio Windows; servidor PHP temporal enlazado a `127.0.0.1:8089`.
- Correo en memoria, avisos desactivados, Wompi sin claves, sesiones en archivos exclusivos de esta copia y caché en memoria. En cada solicitud se bloquearon conexiones HTTP externas y se simularon correo, notificaciones y colas; PHP también tuvo deshabilitados los mecanismos de red usados por HTTP y correo.

Se crearon cinco cuentas ficticias con dominios `.test`: administración, cliente, asesor, otro cliente y otro asesor. Dos clientes recibieron servicios simulados activos con importe cero, sin crear pagos ni órdenes Wompi. Se reutilizaron los PDF ficticios locales de manuscrito y devolución; no se enviaron documentos reales como datos de prueba.

## Bloques comprobados

1. **Carga:** inicio de sesión del cliente mediante el formulario de acceso, carga del manuscrito PDF y sus indicaciones; descarga con los mismos bytes. Repetir el mismo envío conservó un único documento y una única entrada de indicaciones.
2. **Asignación:** el asesor no pudo abrir el expediente antes de estar asignado. Administración lo asignó y el asesor pudo consultar las indicaciones y recuperar el PDF. El cliente vio el estado En revisión.
3. **Devolución:** el asesor compartió el segundo PDF y un mensaje ficticio. Ambos aparecieron en el cliente; el PDF coincidió con el original de prueba. Reintentar la devolución no duplicó documento ni mensaje. El cliente vio En ajustes.
4. **Respuesta y entrega:** la respuesta ficticia del cliente apareció en el expediente del asesor junto al contexto anterior. Repetirla no duplicó las indicaciones. El cliente vio Entregado y conservó ambos archivos y el mensaje.
5. **Privacidad:** otro cliente con servicio activo no pudo abrir ninguno de los documentos; un asesor no asignado tampoco pudo abrir el expediente ni los archivos. Sin sesión, las descargas exigieron iniciar sesión. Administración mantuvo acceso al expediente completo.

Las solicitudes utilizaron sesiones y cookies reales del servidor local, contraseñas ficticias, tokens CSRF y formularios multipart para los archivos. No se inyectó una identidad en memoria para evitar la autenticación. Los reintentos fueron secuenciales con el mismo identificador de envío: esta prueba no simula solicitudes simultáneas.

## Resultado en los registros

El expediente ficticio final contiene exactamente dos documentos, un mensaje del asesor y dos entradas de indicaciones del cliente (carga inicial y respuesta). Hay dos recibos de envío del cliente y uno del asesor; la asignación corresponde al asesor ficticio y el estado final es `delivered`.

Se agregaron únicamente los registros de la prueba: cinco usuarios, un plan, dos servicios simulados, un tipo documental, dos documentos, una asignación, un mensaje, cuatro actividades y tres recibos de envío. Las tablas financieras conservan sus huellas originales. No hay nuevos trabajos, trabajos fallidos, notificaciones, pagos u órdenes Wompi.

El registro por solicitud contabilizó cero intentos de correo, correo en cola, notificaciones, trabajos en cola y solicitudes HTTP externas. Los dos archivos ficticios almacenados coinciden con sus originales.

## Conservación y cierre

Todos los registros originales de las 29 tablas permanecen idénticos en la tercera copia. También permanecen intactas las 27 tablas de la restauración histórica y las 29 de la restauración actualizada. Los siete documentos originales mantienen sus huellas en las tres copias. El `.env` y la base SQLite del entorno habitual no cambiaron; los 274 archivos versionados copiados a esta prueba conservan sus huellas.

No se encontraron referencias huérfanas. Se detuvo únicamente el servidor PHP de esta prueba, se apagó limpiamente MariaDB y se confirmó que dejaron de escuchar los puertos 8089 y 13367. Se conservan las tres copias y los datos ficticios para revisiones posteriores.

Evidencias privadas: `clone-result.json`, `seed-result.json`, `http-state-private.json`, `request-audit-private.jsonl`, `original-row-hashes-private.json` y `closure.json`. Las credenciales y cookies están en archivos privados excluidos de Git. Los programas `clone.php`, `seed.php`, `helpers.php`, `router.php`, `exercise.mjs` y `verify-close.php` quedan junto a las evidencias; no publicar la carpeta completa.

## Alcance

La comprobación utiliza HTTP real local y verifica contenido, permisos, sesiones, archivos y datos; no es una revisión visual en navegador ni una prueba de pagos o correo reales. Los PDF se contrastaron por su contenido binario, sin revisar su aspecto en un visor. Se mantiene la diferencia de versiones de MariaDB indicada en las restauraciones anteriores (dump de 11.8.9, motor de prueba 10.11.14).

Las pruebas y sus escrituras se realizaron exclusivamente sobre la tercera copia. No se desplegó nada en Hostinger ni se cambió la configuración de red o del antivirus del PC.
