<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $legalTitle }} · propaps.com</title>
<link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
.sp-legal-page{max-width:1060px;margin:0 auto;padding:130px 24px 64px;color:#f1f7f6}.sp-legal-eyebrow{color:#2cc295;font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}.sp-legal-page h1{font-size:clamp(30px,5vw,46px);line-height:1.15;margin:12px 0 18px}.sp-legal-nav{display:flex;flex-wrap:wrap;gap:10px;margin:24px 0 30px}.sp-legal-nav a{border:1px solid #17644f;padding:10px 14px;border-radius:10px;text-decoration:none;font-size:14px}.sp-legal-nav a[aria-current=page]{background:#06513d;color:#fff;border-color:#2cc295}.sp-legal-content{border:1px solid #15533f;background:linear-gradient(135deg,#032e27,#001d1b);border-radius:22px;padding:32px;line-height:1.8;overflow-wrap:anywhere}.sp-legal-content p,.sp-legal-content ul{margin:0 0 16px;color:#c1d9d3;font-size:15px}.sp-legal-content section+section{padding-top:24px;margin-top:24px;border-top:1px solid #15533f}.sp-legal-content h3{font-size:19px;margin:0 0 12px;line-height:1.4}.sp-legal-content a{color:#46dab0;text-decoration:underline;text-underline-offset:3px}.sp-legal-content ul{padding-left:22px;list-style:disc}.sp-legal-content li{margin-bottom:10px}.sp-legal-content details{border-bottom:1px solid #17604a;padding:18px 0}.sp-legal-content details:first-child{padding-top:0}.sp-legal-content details:last-child{border:0;padding-bottom:0}.sp-legal-content summary{cursor:pointer;font-weight:600;font-size:17px;line-height:1.5}.sp-legal-content details>div{padding-top:16px}.sp-legal-page a:focus-visible,.sp-legal-content summary:focus-visible{outline:2px solid #2cc295;outline-offset:5px}@media(max-width:640px){.sp-legal-page{padding:175px 18px 40px}.sp-legal-content{padding:22px 18px;border-radius:16px}.sp-legal-content summary{font-size:16px}.sp-legal-nav a{font-size:13px;padding:9px 10px}}
.sp-legal-content .sp-faq-item{background:#002a24;border:1px solid #17604a;margin:0 0 12px;padding:0!important}.sp-legal-content .sp-faq-item summary{color:#f1f7f6;padding:18px;font-size:16px}.sp-legal-content .sp-faq-item[open] summary{background:#064a39;border-color:#17604a}.sp-legal-content .sp-faq-item>div{background:#001e1b;padding:18px}.sp-legal-content .sp-faq-item a{color:#46dab0}</style></head>
<body class="min-h-screen bg-[#000F1F] font-sans text-[#F1F7F6]">
@include('partials.public.header')
<main class="sp-legal-page">
<p class="sp-legal-eyebrow">Información y condiciones del servicio</p><h1>{{ $legalTitle }}</h1>
<p style="color:#aac8c4">Consulta el alcance del acompañamiento y las condiciones para utilizar propaps.com.</p>
<nav class="sp-legal-nav" aria-label="Información del servicio">
@foreach(['terms'=>['legal.terms','Términos'],'privacy'=>['legal.privacy','Privacidad'],'faq'=>['legal.faq','Preguntas frecuentes']] as $key=>$item)
<a href="{{ route($item[0]) }}" @if($legalDocument===$key) aria-current="page" @endif>{{ $item[1] }}</a>
@endforeach
<a href="{{ route('plans') }}">Ver portafolio</a></nav>
<article class="sp-legal-content" aria-label="{{ $legalTitle }}">@include('partials.legal.'.$legalDocument)</article>
</main>
@include('partials.public.footer')
</body></html>
