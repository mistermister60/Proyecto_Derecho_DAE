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
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center rounded-xl mb-4" style="width: 64px; height: 64px; background: #1E3A5F;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h1 class="text-xl font-bold" style="color: #111827;">Consultorio Jurídico USAP</h1>
            <p class="text-sm mt-1" style="color: #6B7280;">Sistema de Gestión de Casos</p>
        </div>

        {{-- Formulario --}}
        <div class="rounded-xl p-6" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
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
                           class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-colors"
                           style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;"
                           placeholder="usuario@usap.edu"
                           onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
                </div>

                <div class="mb-5">
                    <label class="text-sm font-medium mb-1.5 block" style="color: #374151;">Contraseña</label>
                    <input type="password" name="contrasena" required
                           class="w-full rounded-lg px-3 py-2.5 text-sm outline-none transition-colors"
                           style="border: 1px solid #E5E7EB; color: #111827; background: #FFFFFF;"
                           placeholder="••••••••"
                           onfocus="this.style.borderColor='#2563EB';" onblur="this.style.borderColor='#E5E7EB';">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-medium transition-all"
                        style="background: #1E3A5F; color: white;"
                        onmouseover="this.style.background='#16304F';" onmouseout="this.style.background='#1E3A5F';">
                    Entrar
                </button>
            </form>
        </div>

        {{-- Usuarios de prueba --}}
        <div class="rounded-xl p-4 mt-4" style="background: #FFFFFF; border: 1px solid #E5E7EB;">
            <p class="text-xs font-semibold mb-2" style="color: #6B7280;">USUARIOS DE PRUEBA</p>
            <div class="space-y-1">
                <p class="text-xs" style="color: #374151;"><strong>Director:</strong> director@usap.edu / director123</p>
                <p class="text-xs" style="color: #374151;"><strong>Procurador:</strong> iris.rodriguez@usap.edu / procurador123</p>
            </div>
        </div>
    </div>
</body>
</html>
