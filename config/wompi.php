<?php

return [
    // Política aprobada: IVA adicional a la tarifa base de ambos extras.
    'peer_review_vat' => 'added',
    'environment' => env('WOMPI_ENVIRONMENT', 'sandbox'),
    'public_key' => env('WOMPI_PUBLIC_KEY', ''),
    'private_key' => env('WOMPI_PRIVATE_KEY', ''),
    'integrity_secret' => env('WOMPI_INTEGRITY_SECRET', ''),
    'events_secret' => env('WOMPI_EVENTS_SECRET', ''),
    // Sin valores implícitos: deben aprobarse antes de ofrecer un importe estándar.
    'cop_per_usd' => env('WOMPI_COP_PER_USD'),
    'tax_basis_points' => env('WOMPI_TAX_BASIS_POINTS'),
    // Vigencia: un año para Indexación; fechas registradas por servicio para los demás.
    'sandbox_access' => env('WOMPI_SANDBOX_ACCESS', false),
    'catalog' => [
        'correccion-completa' => ['name' => 'Corrección Completa', 'options' => ['documento' => ['name' => 'Hasta 40.000 palabras', 'usd' => 550]]],
        'modo-indexacion' => ['name' => 'Modo Indexación', 'options' => ['pregrado' => ['name' => 'Pregrado', 'usd' => 510], 'maestria' => ['name' => 'Maestría', 'usd' => 540], 'doctorado' => ['name' => 'Doctorado', 'usd' => 570]]],
        'grandes-volumenes' => ['name' => 'Grandes Volúmenes', 'options' => ['100-paginas' => ['name' => '100 páginas', 'usd' => 650], '200-paginas' => ['name' => 'Más de 100 y hasta 200 páginas', 'usd' => 720], 'mas-200' => ['name' => 'Más de 200 páginas', 'usd' => 800]]],
    ],
];
