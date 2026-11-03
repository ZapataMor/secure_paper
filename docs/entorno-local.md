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
