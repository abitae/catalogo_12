<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ElizaLte</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-blue-100 to-white">
    <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-xl px-8 py-10 flex flex-col items-center text-center">
        <div class="text-4xl font-extrabold tracking-wide text-blue-600 mb-2">ElizaLte</div>
        <div class="text-base text-slate-500 mb-6">Bienvenido a ElizaLte, tu plataforma inteligente para la gestión y control eficiente de tu negocio.</div>
        <div class="flex justify-center mb-8">
            <svg class="w-28 h-28" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="60" cy="60" r="56" fill="#3b82f6" fill-opacity="0.08"/>
                <circle cx="60" cy="60" r="40" fill="#3b82f6" fill-opacity="0.15"/>
                <circle cx="60" cy="60" r="24" fill="#3b82f6" fill-opacity="0.25"/>
                <circle cx="60" cy="60" r="10" fill="#3b82f6"/>
            </svg>
        </div>
        <div class="flex justify-center w-full mt-6">
            <a href="{{ route('login') }}" class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">Iniciar sesión</a>
        </div>
        <div class="mt-8 text-xs text-slate-400 text-center w-full">
            Desarrollado por Abel Arana Cortez &middot; <a href="https://open9.cloud" class="underline hover:text-blue-600 transition" target="_blank">open9.cloud</a>
        </div>
    </div>
</body>
</html>
