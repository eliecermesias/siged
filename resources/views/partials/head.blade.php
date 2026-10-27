<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - UNICUCES' : 'UNICUCES' }}
</title>

<link rel="icon" href="{{ asset('images/unicuces-mark.png') }}" type="image/png" sizes="270x270">
<link rel="apple-touch-icon" href="{{ asset('images/unicuces-mark.png') }}">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
