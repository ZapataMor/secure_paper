@php
    $clientJourney = $clientJourney ?? app(\App\Services\ClientJourney::class)->forUser(auth()->user());
@endphp
<style>
    .sp-journey { min-width: 0; padding: 24px; border: 1px solid #2cc29545; border-radius: 18px; background: linear-gradient(140deg,#006a4c30,#001c18a6); color: #f1f7f6; }
    .sp-journey p,.sp-journey h2 { margin: 0; }
    .sp-journey-status { color: #2cc295; font-size: 12px; font-weight: 600; }
    .sp-journey h2 { margin-top: 12px; font-size: 23px; font-weight: 650; line-height: 1.3; letter-spacing: -.02em; }
    .sp-journey-description { margin-top: 12px !important; color: #b5cec8; font-size: 14px; line-height: 1.7; }
    .sp-journey-context { margin-top: 12px !important; color: #c5ddd5; font-size: 12px; line-height: 1.7; }
    .sp-journey-tasks { display: grid; gap: 13px; margin-top: 20px; padding-top: 17px; border-top: 1px solid #2cc29526; }
    .sp-journey-tasks strong { display: block; margin-bottom: 4px; font-size: 12px; color: #f1f7f6; }
    .sp-journey-tasks p { color: #b5cec8; font-size: 13px; line-height: 1.65; }
    .sp-journey-action { display: inline-flex; align-items: center; justify-content: center; gap: 12px; min-height: 44px; max-width: 100%; margin-top: 21px; padding: 11px 17px; border-radius: 9px; background: #2cc295; color: #00251b; font-size: 13px; font-weight: 700; text-align: center; text-decoration: none; line-height: 1.5; }
    .sp-journey-action:hover { background: #51d3ac; }
    .sp-journey-action:focus-visible { outline: 3px solid #2cc295; outline-offset: 4px; }
    .sp-journey * { overflow-wrap: anywhere; box-sizing: border-box; }
    .sp-upload > .sp-journey { margin-bottom: 22px; }
    @media(max-width:600px) { .sp-journey { padding: 21px 18px; } .sp-journey-action { width: 100%; } }
</style>
<aside class="sp-journey" aria-label="Tu siguiente paso" data-client-step="{{ $clientJourney['kind'] }}">
    <span class="sp-journey-status">{{ $clientJourney['status'] }}</span>
    <h2>{{ $clientJourney['title'] }}</h2>
    <p class="sp-journey-description">{{ $clientJourney['description'] }}</p>
    @if($clientJourney['active'])
        <p class="sp-journey-context">{{ $clientJourney['plan'] }}<br>Carga habilitada hasta el {{ $clientJourney['until'] }}, inclusive.</p>
    @endif
    <div class="sp-journey-tasks">
        <div><strong>Tu parte</strong><p>{{ $clientJourney['client'] }}</p></div>
        <div><strong>Trabajo del equipo</strong><p>{{ $clientJourney['team'] }}</p></div>
    </div>
    <a class="sp-journey-action" href="{{ $clientJourney['url'] }}">{{ $clientJourney['action'] }} <span aria-hidden="true">→</span></a>
</aside>
