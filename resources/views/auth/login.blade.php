{{--
    Vista: auth/login
    Propósito: Formulario de inicio de sesión del sistema. Autentica al usuario mediante correo electrónico y contraseña.
    Variables: $errors (errores de validación del formulario)
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Consultorio Jurídico USAP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
</head>
<body style="background: #F3F4F6; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif;">
    <div class="w-full max-w-md px-4 md:px-0">
        {{-- Logo --}}
        <div class="text-center mb-6 md:mb-8">
            <div class="inline-flex items-center justify-center rounded-xl mb-4 w-12 h-12 md:w-16 md:h-16" style="background: #1E3A5F;">
                <svg class="w-6 h-6 md:w-8 md:h-8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h1 class="text-lg md:text-xl font-bold" style="color: #111827;">Consultorio Jurídico USAP</h1>
            <p class="text-xs md:text-sm mt-1" style="color: #6B7280;">Sistema de Gestión de Casos</p>
        </div>

        {{-- Formulario --}}
        <div class="rounded-xl p-4 md:p-6" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <h2 class="text-lg font-semibold mb-5" style="color: #111827;">Iniciar sesión</h2>

            @if ($errors->any())
            <div class="rounded-lg p-3 mb-4" style="background: #FEE2E2; border: 1px solid #FECACA;">
                @foreach ($errors->all() as $error)
                <p class="text-sm" style="color: #DC2626;">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-4">
                    <label class="text-sm font-medium mb-1.5 block" style="color: #374151;">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-colors border border-gray-200 text-gray-900 bg-white focus:border-blue-600 min-h-[44px]"
                           placeholder="usuario@usap.edu">
                </div>

                <div class="mb-5">
                    <label class="text-sm font-medium mb-1.5 block" style="color: #374151;">Contraseña</label>
                    <input type="password" name="contrasena" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-colors border border-gray-200 text-gray-900 bg-white focus:border-blue-600 min-h-[44px]"
                           placeholder="••••••••">
                </div>

                 <button type="submit" class="w-full py-3 md:py-2.5 rounded-lg text-sm font-medium transition-all bg-[#1E3A5F] text-white hover:bg-[#16304F] min-h-[44px]">
                    Entrar
                </button>
            </form>
        </div>


    </div>
</body>
</html>
