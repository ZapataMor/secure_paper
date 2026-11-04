# Revisión de respaldos automáticos de Hostinger — 4 de noviembre de 2026

Revisión de solo lectura en hPanel, sitio seleccionado propaps.com. No se crearon ni restauraron copias, no se descargaron archivos del alojamiento, no se modificaron datos, cron, configuración o facturación. El acceso SSH temporal anterior permanece revocado.

## Estado confirmado en la cuenta

- Respaldo automático: **Diariamente**, ya activo. No hace falta activarlo ni contratarlo de nuevo para esta frecuencia.
- Último respaldo mostrado: **2026-11-03 23:20**.
- Próximo respaldo mostrado: **2026-11-04**, sin hora en el resumen.
- Archivos y base de datos: 24 puntos visibles por categoría, desde **2026-09-19 23:12** hasta **2026-11-03 23:20**. La lista contiene puntos diarios, puntos más espaciados y copias personalizadas; no equivale a 24 copias automáticas diarias garantizadas.
- Base correcta: `u826249991_propaps_db`, contrastada con la cabecera del dump que se restauró localmente. Figura en la lista y tiene disponible el punto más reciente. El selector presentaba inicialmente una base distinta: siempre elegir la base correcta expresamente.
- Copias manuales: el panel permite crearlas cada 24 horas. No se inició ninguna.

La documentación oficial indica que las horas se presentan en la zona local. Las fechas anteriores se transcriben tal como aparecen en el panel; no se convirtieron a UTC ni se dedujo una hora fija para futuras copias.

## Alcance comprobado y punto pendiente

Al abrir Mostrar archivos para el último respaldo, el visor muestra `domains/propaps.com/public_html`, con su entrada pública, configuración web, build y recursos. La aplicación Laravel real está en la carpeta hermana `secure_paper`; los expedientes privados están en `secure_paper/storage/app/private`.

**La vista consultada no permite dar por comprobada la inclusión de secure_paper ni de los documentos privados.** Esto tampoco demuestra que se hayan excluido: la documentación describe la descarga de archivos del plan completo y el menú del panel ofrece Descargar los últimos respaldos del plan. No se inició esa preparación ni se descargaron datos de otros sitios.

Siguiente comprobación recomendada: revisar una copia completa del plan en almacenamiento privado y confirmar la presencia de `secure_paper`, su configuración, documentos y la entrada `public_html`; contrastarla con el respaldo de la base de datos de la misma fecha. Extraer y comprobar solo Propaps. Cualquier prueba de importación debe hacerse en una base aislada. No usar Restaurar archivos ni Restaurar base de datos sobre producción durante esa revisión.

El último punto automático listado es anterior a la publicación del 4 de noviembre. Hasta que el panel muestre una copia posterior, no afirmar que las últimas mejoras ya estén dentro del respaldo automático. Las copias privadas de la ventana y la prueba de restauración anterior siguen siendo una protección independiente, pero corresponden al estado previo a esa publicación.

## Retención y exclusiones

La guía oficial de descarga consultada indica siete días para copias diarias y seis semanas para semanales. En esta cuenta hay puntos más antiguos y más diarios que ese mínimo documentado; registrar lo observado sin prometer que seguirán disponibles. El panel no mostró una política contractual específica de retención. Conviene conservar copias independientes de hitos importantes.

hPanel muestra un aviso de exclusiones de archivos de respaldo de plugins, caché y exportaciones de base de datos. El artículo enlazado detalla principalmente carpetas de plugins WordPress; Propaps es Laravel. No inferir desde ese artículo que un SQL o archivo comprimido creado manualmente esté cubierto. La base debe recuperarse desde Respaldo de la base de datos, además de los archivos de aplicación y documentos.

Fuentes oficiales consultadas el 4 de noviembre de 2026:

- Descarga, zona horaria, retención y alcance del plan: https://www.hostinger.com/support/5981435-how-to-download-backups-at-hostinger/
- Exclusiones: https://www.hostinger.com/support/which-files-are-excluded-from-hostinger-backups/

## Evidencia y continuidad

Captura local de la base correcta seleccionada: `propaps-respaldo-base-20261104.png`, en la carpeta de visualizaciones de esta revisión. La prueba previa de restauración de las copias privadas está descrita en `docs/restauracion-aislada-20261104.md`; no fue una restauración de un archivo descargado del sistema automático de Hostinger.

Conclusión: frecuencia diaria activa y copia de la base correcta disponibles; falta comprobar el contenido privado de la copia completa del plan y la existencia del próximo punto posterior a la publicación. No se requiere modificar propaps.com para esas comprobaciones.
