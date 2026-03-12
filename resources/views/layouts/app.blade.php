<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SIMRS</title>
   @vite([
'resources/js/app.js',
'resources/css/app.css',
'resources/css/dashboard.css',
'resources/css/bpjs.css',
'resources/css/master_pasien_create.css',
'resources/css/master_pasien.css',
'resources/js/dashboard.js',
'resources/js/registrasi.js',
'resources/js/rawat_jalan.js'
])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>

<div class="app">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    <main class="content-area">

        {{-- Topbar --}}
        @include('layouts.topbar')

        <div class="page">
            @yield('content')
        </div>

    </main>

</div>
@stack('scripts')
</body>
</html>
