<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Gera helpers de rotas para usar em Vue -->
    @routes
    <!-- Carrega CSS e JS compilados via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Ativa compartilhamento de dados com Inertia no head -->
    @inertiaHead
</head>

<body class="bg-gray-50 text-gray-800">
    <!-- Renderiza a página Inertia -->
    @inertia
</body>

</html>