<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
Propaps.com
</x-mail::header>
</x-slot:header>

# {{ $heading }}

Hola,

{{ $intro }}

<x-mail::button :url="$accountActionUrl" color="success">
{{ $buttonLabel }}
</x-mail::button>

{{ $expiry }}

{{ $notice }}

Atentamente,<br>
El equipo de Propaps.com

<x-slot:subcopy>
<x-mail::subcopy>
Si el botón no funciona, copia y pega este enlace en tu navegador:

[{{ $accountActionUrl }}]({{ $accountActionUrl }})
</x-mail::subcopy>
</x-slot:subcopy>

<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} Professional Papers S.A.S. · Propaps.com<br>
Inteligencia humana + inteligencia artificial = inteligencia híbrida
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
