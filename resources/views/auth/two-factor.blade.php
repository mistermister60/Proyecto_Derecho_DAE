@extends('layouts.app') {{-- Revisa si tus compañeros usan otro layout como layouts.auth --}}

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; font-family: Arial, sans-serif;">
        <h2 style="color: #1e3a8a; margin-bottom: 10px;">Verificación de Seguridad</h2>
        <p style="color: #6b7280; font-size: 14px; margin-bottom: 25px;">Hemos enviado un código de acceso a tu correo institucional de la USAP.</p>

        <form action="{{ route('auth.two-factor.verify') }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <input type="text" name="code" placeholder="000000" maxlength="6" required 
                       style="width: 100%; padding: 15px; font-size: 24px; text-align: center; letter-spacing: 5px; border: 2px solid #e5e7eb; border-radius: 8px; font-weight: bold; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #1e3a8a; color: white; padding: 12px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">
                Verificar Código
            </button>
        </form>
    </div>
</div>
@endsection
