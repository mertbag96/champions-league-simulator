<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.webp" sizes="any">
        <link rel="icon" href="/favicon.webp" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/favicon.webp">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto-mono:300,400,500,600,700" rel="stylesheet" />

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased text-white selection:bg-blue-800">
        @inertia
    </body>
</html>
