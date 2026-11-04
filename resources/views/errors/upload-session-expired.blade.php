<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recupera tu envío - Propaps</title>
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
        <h1>Vuelve a ingresar para continuar</h1>
        <p>La sesión del formulario venció o se actualizó. Este envío no se guardó; tu expediente anterior se conserva.</p>
        <p>Continúa en esta misma pestaña e ingresa con la misma cuenta. Si hay un borrador temporal disponible, podrás recuperar tus indicaciones y revisarlas antes de enviar.</p>
        <p>Deberás seleccionar los archivos de nuevo. El envío no se repetirá automáticamente.</p>
        <a href="{{ route('private.upload-document') }}">Volver al expediente y recuperar</a>
    </main>
</body>
</html>
