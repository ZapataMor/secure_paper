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

## Siguiente bloque: entorno local de pruebas

La copia de codigo aun requiere preparar PHP, Composer, dependencias, configuracion local, base de datos de pruebas y recursos del frontend.
Trabajar desde la nueva raiz en D. Usar datos ficticios, una base de datos aislada y correo de pruebas; no copiar credenciales ni datos privados de produccion.
Revisar los requisitos de `composer.json`, `composer.lock` y `package-lock.json` antes de instalar.
No ejecutar el script `composer setup` hasta disponer de una configuracion local aislada, porque incluye migraciones.
Conservar cada mejora en Git y verificarla antes de publicarla.

## Alcance operativo

El traslado y este registro no certifican la operacion de pagos, correos ni recorridos privados en produccion.
Actualizacion confirmada por el usuario el 3 de noviembre de 2026: el ultimo gran avance fue una prueba de pago real realizada satisfactoriamente. Esta confirmacion sustituye el pendiente historico de Wompi descrito en los registros de octubre. En esta sesion no se repitio el cobro ni se consulto el detalle de la transaccion; los recorridos restantes se revisaran por bloques.
No ejecutar una actualizacion completa en Hostinger solamente para copiar este documento sin revisar primero el estado y los cambios pendientes del servidor.