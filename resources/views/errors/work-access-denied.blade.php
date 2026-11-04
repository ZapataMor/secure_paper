<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revisa tu acceso - Propaps</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #032522; color: #edf9f6; font: 17px/1.6 system-ui, sans-serif; }
        main { box-sizing: border-box; width: min(620px, calc(100% - 32px)); padding: 32px; border: 1px solid #246859; border-radius: 18px; background: #05332d; }
        h1 { font-size: 28px; line-height: 1.25; } p { color: #c0ded7; } strong { color: #36d0a5; }
        a { display: inline-block; margin-top: 12px; padding: 12px 20px; border-radius: 9px; background: #2cc295; color: #00251b; font-weight: 700; text-decoration: none; }
        a:focus-visible { outline: 3px solid white; outline-offset: 4px; }
    </style>
</head>
<body>
    <main>
        <strong>propaps.com</strong>
        <h1>No puedes compartir en este expediente</h1>
        <p>Tu cuenta no tiene permiso para enviar a este expediente. La asignación del proyecto o tu acceso pudo haber cambiado.</p>
        <p>Este intento no añadió documentos ni mensajes. El historial del expediente se conserva.</p>
        <p>Vuelve a tu lista de proyectos. Si necesitas aclarar la asignación, consulta con la administración. No se repetirá el envío automáticamente.</p>
        <a href="{{ route('admin.works.index') }}">Volver a mis proyectos</a>
    </main>
</body>
</html>
